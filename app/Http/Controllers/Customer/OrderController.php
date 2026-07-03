<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Services\StaffAssignmentService;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Order::where('customer_id', Auth::id());

        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'like', "%{$request->input('search')}%");
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Get all active services
        $services = Service::where('is_active', true)->get();
        $user = Auth::user();

        return view('customer.orders.create', compact('services', 'user'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'services' => ['required', 'array', 'min:1'],
            'services.*.selected' => ['nullable', 'string', 'in:1'],
            'services.*.service_id' => ['required', 'exists:services,id'],
            'services.*.quantity' => ['required', 'integer', 'min:1'],
            'services.*.weight_kg' => ['nullable', 'numeric', 'min:0.1'],
            'services.*.care_instructions' => ['nullable', 'string', 'max:500'],
            'pickup_address' => ['required', 'string', 'max:500'],
            'delivery_address' => ['required', 'string', 'max:500'],
            'pickup_time' => ['required', 'date', 'after:now'],
            'delivery_time' => ['required', 'date', 'after:pickup_time'],
            'special_instructions' => ['nullable', 'string', 'max:1000'],
            'payment_method' => ['required', 'in:cash,zaad,edahab'],
            'payment_phone' => [
                'required_if:payment_method,zaad',
                'required_if:payment_method,edahab',
                'nullable',
                'string',
                'max:20'
            ],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $totalPrice = 0;
            $itemsData = [];

            // Calculate running total using secure database prices
            foreach ($validated['services'] as $item) {
                // If service is not checked, skip it
                if (!isset($item['selected'])) {
                    continue;
                }

                $service = Service::findOrFail($item['service_id']);
                $subtotal = 0;

                if (!empty($item['weight_kg'])) {
                    $subtotal = $item['weight_kg'] * $service->price_per_kg;
                } else {
                    $subtotal = $item['quantity'] * $service->price_per_item;
                }

                $totalPrice += $subtotal;
                $itemsData[] = [
                    'service_id' => $service->id,
                    'quantity' => $item['quantity'],
                    'price' => !empty($item['weight_kg']) ? $service->price_per_kg : $service->price_per_item,
                    'notes' => $item['care_instructions'] ?? null,
                    'weight' => $item['weight_kg'] ?? null,
                ];
            }

            if (empty($itemsData)) {
                return back()->withErrors(['services' => 'Please select at least one service.'])->withInput();
            }

            // Generate unique order number
            do {
                $orderNumber = 'LOMS-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            } while (Order::where('order_number', $orderNumber)->exists());

            // Save order
            $order = new Order();
            $order->order_number = $orderNumber;
            $order->customer_id = Auth::id();
            $order->total_price = $totalPrice;
            $order->status = 'pending_pickup';
            $order->payment_status = 'pending';
            $order->payment_method = $validated['payment_method'];
            $order->pickup_address = $validated['pickup_address'];
            $order->delivery_address = $validated['delivery_address'];
            $order->pickup_time = $validated['pickup_time'];
            $order->delivery_time = $validated['delivery_time'];
            $order->special_instructions = $validated['special_instructions'] ?? null;
            
            // Log weight of first item if applicable or total weight
            $totalWeight = 0;
            $hasWeight = false;
            foreach ($itemsData as $item) {
                if ($item['weight'] !== null) {
                    $totalWeight += $item['weight'];
                    $hasWeight = true;
                }
            }
            $order->weight = $hasWeight ? $totalWeight : null;
            $order->save();

            // Auto-assign order to staff using Round-Robin
            $assignedStaff = app(\App\Services\StaffAssignmentService::class)->assignNextStaff();
            if ($assignedStaff) {
                $order->staff_id = $assignedStaff->id;
            }

            // Auto-assign delivery agent only for Cash orders.
            // For Zaad/Edahab, delivery agent is assigned AFTER staff verifies payment.
            if ($validated['payment_method'] === 'cash') {
                $assignedAgent = app(\App\Services\DeliveryAssignmentService::class)->assignNextAgent();
                if ($assignedAgent) {
                    $order->delivery_agent_id = $assignedAgent->id;
                }
            }
            // Zaad/Edahab: delivery_agent_id remains null until payment is verified by staff

            $order->save();

            // Save order items
            foreach ($itemsData as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->service_id = $item['service_id'];
                $orderItem->quantity = $item['quantity'];
                $orderItem->price = $item['price'];
                $orderItem->notes = $item['notes'];
                $orderItem->save();
            }

            // Save payment record
            $payment = new Payment();
            $payment->order_id = $order->id;
            $payment->user_id = Auth::id();
            $payment->payment_method = $validated['payment_method'];
            $payment->amount = $totalPrice;
            $payment->status = 'pending';
            // Save payment phone reference in reference if needed
            if ($validated['payment_method'] !== 'cash' && !empty($validated['payment_phone'])) {
                $payment->transaction_reference = 'MOBILEPAY-' . $validated['payment_phone'];
            }
            $payment->save();

            // Determine payment_status based on method
            if (in_array($validated['payment_method'], ['zaad', 'edahab'])) {
                // If customer confirmed payment in Step 3, go straight to awaiting_staff_review
                $customerConfirmed = $request->input('customer_payment_confirmed', '0');
                $order->payment_status = ($customerConfirmed === '1')
                    ? 'awaiting_staff_review'
                    : 'pending_verification';

                // Save proof fields to payments table
                $payment->wallet_phone = $request->input('wallet_phone');
                $payment->sender_name  = $request->input('sender_name');
                // Keep existing transaction_reference logic as-is
                $payment->save();
            } else {
                // Cash — no verification needed, keep as 'pending'
                $order->payment_status = 'pending';
            }
            $order->save();

            // Create notifications (customer system/email + admin alert)
            try {
                \App\Services\NotificationService::orderPlaced($order);
            } catch (\Exception $e) {
                Log::error("Failed to send order placed notification: " . $e->getMessage());
            }

            return redirect()->route('customer.orders.show', $order->id)->with('success', 'Your order has been placed successfully!');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): View
    {
        // Enforce ownership check
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $order->load(['orderItems.service', 'payment', 'deliveryAssignment.deliveryAgent']);

        return view('customer.orders.show', compact('order'));
    }

    /**
     * Cancel the order.
     */
    public function cancel(Order $order): RedirectResponse
    {
        // Enforce ownership check
        if ($order->customer_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow cancel if status = 'pending_pickup'
        if ($order->status !== 'pending_pickup') {
            return back()->with('error', 'Only orders in pending pickup status can be cancelled.');
        }

        $order->status = 'cancelled';
        $order->save();

        // Create notification
        \App\Services\NotificationService::orderStatusUpdated($order, 'cancelled');

        return redirect()->route('customer.orders.show', $order->id)->with('success', 'Order cancelled successfully.');
    }

    public function confirmPayment(Order $order)
    {
        // Ensure this order belongs to the authenticated customer
        if ($order->customer_id !== auth()->id()) {
            abort(403);
        }

        // Only allow confirmation on pending_verification orders
        if ($order->payment_status !== 'pending_verification') {
            return back()->with('error', 'This action is no longer available for this order.');
        }

        // Only for mobile payment orders
        if (!in_array($order->payment_method, ['zaad', 'edahab'])) {
            return back()->with('error', 'This action is only for mobile payment orders.');
        }

        // Customer confirms they have paid
        $order->payment_status = 'awaiting_staff_review';
        $order->save();

        // Notify the customer
        $order->customer->notifications()->create([
            'order_id' => $order->id,
            'title'    => 'Payment Confirmation Received',
            'message'  => 'We have received your payment confirmation for Order #' 
                          . $order->order_number 
                          . '. Our staff will verify it shortly.',
            'type'     => 'payment_pending_review',
            'is_read'  => false,
        ]);

        return back()->with('success', 
            'Thank you! Your payment confirmation has been sent to our staff for review.');
    }
}
