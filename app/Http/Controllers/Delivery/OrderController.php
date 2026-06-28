<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\NotificationService;
use App\Services\WhatsAppService;
use App\Services\WhatsAppTemplates;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    /**
     * Display a listing of orders assigned to the delivery agent.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $agentId = Auth::id();

        // Determine status filter
        $statusFilter = ($request->get('status') === 'delivered')
            ? ['delivered']
            : ['pending_pickup', 'picked_up_from_customer', 'ready_for_delivery',
               'picked_up_from_laundry', 'on_the_way'];

        $query = Order::where('delivery_agent_id', $agentId)
            ->whereIn('status', $statusFilter)
            ->where(function ($q) {
                // Cash orders: always visible
                $q->where('payment_method', 'cash')
                  // Mobile money: only after staff verifies
                  ->orWhere(function ($q2) {
                      $q2->whereIn('payment_method', ['zaad', 'edahab'])
                         ->where('payment_status', 'verified');
                  });
            })
            ->with(['customer']);

        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        $orders = $query->orderBy('updated_at', 'desc')->paginate(10)->withQueryString();

        return view('delivery.orders.index', compact('orders'));
    }

    /**
     * Display the specified assigned order.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\View\View
     */
    public function show(Order $order): View
    {
        if ($order->delivery_agent_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        $paymentOk = $order->payment_method === 'cash' || $order->payment_status === 'verified';
        if (!$paymentOk) {
            abort(403, 'Unauthorized.');
        }

        $order->load(['customer', 'orderItems.service', 'payment']);

        return view('delivery.orders.show', compact('order'));
    }

    /**
     * Update the status of the assigned order.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        if ($order->delivery_agent_id !== Auth::id()) {
            abort(403, 'Unauthorized.');
        }

        $paymentOk = $order->payment_method === 'cash' || $order->payment_status === 'verified';
        if (!$paymentOk) {
            abort(403, 'Unauthorized.');
        }

        $validTransitions = [
            'pending_pickup'          => 'picked_up_from_customer',
            'picked_up_from_customer' => 'delivered_to_laundry',
            'ready_for_delivery'      => 'picked_up_from_laundry',
            'picked_up_from_laundry'  => 'on_the_way',
            'on_the_way'              => 'delivered',
        ];

        if (!isset($validTransitions[$order->status])) {
            abort(403, 'Invalid delivery status transition.');
        }

        $newStatus = $validTransitions[$order->status];

        try {
            DB::transaction(function () use ($order, $newStatus) {
                $order->status = $newStatus;

                // Handle payment status and confirmation details if delivered
                if ($newStatus === 'delivered') {
                    $order->delivery_time = now();

                    if ($order->payment_method === 'cash') {
                        $order->payment_status = 'paid';
                        if ($order->payment) {
                            $order->payment->status = 'completed';
                            $order->payment->paid_at = now();
                            $order->payment->save();

                            // Payment confirmed notification
                            NotificationService::paymentConfirmed($order->payment);
                        }
                    }
                }

                $order->save();

                // If delivered to laundry, auto-advance to processing
                if ($newStatus === 'delivered_to_laundry') {
                    $order->status = 'processing';
                    $order->save();
                }
            });
        } catch (\Exception $e) {
            Log::error("Failed updating order #{$order->id} delivery status: " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update status due to a database error.');
        }

        // Send notifications
        if ($newStatus === 'delivered') {
            NotificationService::deliveryCompleted($order);

            // WhatsApp notification for delivered
            try {
                $order->load('customer');
                $waMessage = WhatsAppTemplates::orderDelivered($order);
                WhatsAppService::send(
                    $waMessage,
                    $order->customer->phone ?? null
                );
            } catch (\Exception $e) {
                Log::error('WhatsApp delivered failed: ' . $e->getMessage());
            }

            return redirect()->route('delivery.orders.index')->with('success', "Order {$order->order_number} marked as delivered successfully!");
        } else {
            // Note: if it became delivered_to_laundry, it was auto-advanced to processing.
            if ($newStatus === 'delivered_to_laundry') {
                NotificationService::orderStatusUpdated($order, 'delivered_to_laundry');
                NotificationService::orderStatusUpdated($order, 'processing');
            } else {
                NotificationService::orderStatusUpdated($order, $newStatus);
            }
        }

        return redirect()->route('delivery.orders.show', $order)->with('success', 'Order status updated successfully.');
    }
}
