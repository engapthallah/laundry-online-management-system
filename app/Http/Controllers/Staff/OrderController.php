<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\WhatsAppService;
use App\Services\WhatsAppTemplates;
use App\Services\DeliveryAssignmentService;

class OrderController extends Controller
{
    use HasStaffOrderFilters;

    /**
     * Display a listing of assigned orders with search, status filtering, date filters, and header sorting.
     */
    public function index(Request $request): View
    {
        $staffId = Auth::id();
        $query = Order::where('staff_id', $staffId)
            ->whereIn('status', [
                'pending_pickup',
                'picked_up_from_customer',
                'delivered_to_laundry',
                'processing',
                'ready_for_delivery',
            ])
            ->with(['customer', 'orderItems.service']);

        $query = $this->applyOrderFilters($query, $request);

        // Sorting configuration
        $sortBy = $request->get('sort_by', 'updated_at');
        $sortDir = $request->get('sort_dir', 'desc');

        if (in_array($sortBy, ['pickup_time', 'status', 'updated_at']) && in_array($sortDir, ['asc', 'desc'])) {
            $query->orderBy($sortBy, $sortDir);
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        // Paginate to 12 orders per page and append request inputs
        $orders = $query->paginate(12)->withQueryString();

        return view('staff.orders.index', compact('orders'));
    }

    /**
     * Display the specified assigned order detail page.
     */
    public function show(Order $order): View
    {
        // Ownership Check
        if ($order->staff_id !== Auth::id()) {
            abort(403, 'This order is not assigned to you.');
        }

        $order->load(['customer', 'orderItems.service', 'payment']);

        return view('staff.orders.show', compact('order'));
    }

    /**
     * Update the processing status of the assigned order in sequence.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        // Ownership Check
        if ($order->staff_id !== Auth::id()) {
            abort(403, 'This order is not assigned to you.');
        }

        // Input validation
        $request->validate([
            'status' => 'required|string|in:ready_for_delivery',
        ]);

        $currentStatus = $order->status;
        $newStatus = $request->status;

        // Enforce transition rules
        $validTransitions = [
            'processing'  => 'ready_for_delivery',
        ];

        if (!isset($validTransitions[$currentStatus]) || $validTransitions[$currentStatus] !== $newStatus) {
            Log::warning("Invalid status transition attempt by Staff ID " . Auth::id() . " on Order #{$order->order_number} from '{$currentStatus}' to '{$newStatus}'");
            abort(403, 'Invalid status transition.');
        }

        // Perform the status change
        $order->status = $newStatus;
        $order->save();

        // Trigger customer notifications
        \App\Services\NotificationService::orderStatusUpdated($order, $newStatus);

        // WhatsApp notification for ready_for_delivery
        if ($newStatus === 'ready_for_delivery') {
            try {
                $waMessage = WhatsAppTemplates::readyForDelivery($order);
                WhatsAppService::send(
                    $waMessage,
                    $order->customer->phone ?? null
                );
            } catch (\Exception $e) {
                Log::error(
                    'WhatsApp notification failed: '
                    . $e->getMessage()
                );
            }
        }

        $statusLabels = [
            'ready_for_delivery' => 'Ready for Delivery',
        ];
        $label = $statusLabels[$newStatus] ?? ucwords(str_replace('_', ' ', $newStatus));

        return redirect()->route('staff.orders.show', $order)->with('success', "Order status successfully updated to {$label}.");
    }

    /**
     * Verify mobile money payment for the assigned order.
     */
    public function verifyPayment(Order $order)
    {
        // Only allow verification of mobile payment orders
        if (!in_array($order->payment_method, ['zaad', 'edahab'])) {
            return back()->with('error', 'This order does not require payment verification.');
        }

        if (!in_array($order->payment_status, ['pending_verification', 'awaiting_staff_review'])) {
            return back()->with('error', 'This payment has already been processed.');
        }

        // Update order payment_status
        $order->payment_status = 'verified';

        // Assign delivery agent now that payment is verified
        if (is_null($order->delivery_agent_id)) {
            $assignedAgent = app(\App\Services\DeliveryAssignmentService::class)->assignNextAgent();
            if ($assignedAgent) {
                $order->delivery_agent_id = $assignedAgent->id;
            }
        }

        $order->save();

        if ($order->customer) {
            $order->customer->notifications()->create([
                'order_id' => $order->id,
                'title'    => 'Payment Verified ✅',
                'message'  => 'Your payment for Order #' . $order->order_number 
                              . ' has been verified. Your laundry is being processed.',
                'type'     => 'system',
                'is_read'  => false,
            ]);
        }

        // Update payment record
        if ($order->payment) {
            $order->payment->verified_by = auth()->id();
            $order->payment->verified_at = now();
            $order->payment->status      = 'completed';
            $order->payment->paid_at     = now();
            $order->payment->save();
        }

        return back()->with('success', 'Payment verified successfully. Delivery agent can now proceed.');
    }

    /**
     * Reject mobile money payment for the assigned order.
     */
    public function rejectPayment(Order $order)
    {
        // Only allow rejection of mobile payment orders pending verification
        if (!in_array($order->payment_method, ['zaad', 'edahab'])) {
            return back()->with('error', 'This order does not require payment verification.');
        }

        if (!in_array($order->payment_status, ['pending_verification', 'awaiting_staff_review'])) {
            return back()->with('error', 'This payment has already been processed.');
        }

        // Update order payment_status to rejected and status to cancelled
        $order->payment_status = 'rejected';
        $order->status = 'cancelled';
        $order->save();

        // Update payment record
        if ($order->payment) {
            $order->payment->status      = 'rejected';
            $order->payment->verified_by = auth()->id();
            $order->payment->verified_at = now();
            $order->payment->save();
        }

        // Trigger notification pipeline (System + Email)
        \App\Services\NotificationService::orderStatusUpdated($order, 'cancelled');

        return back()->with('success', 'Payment rejected and order has been cancelled. Customer has been notified.');
    }
}
