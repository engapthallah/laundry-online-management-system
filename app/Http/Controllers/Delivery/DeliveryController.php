<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAssignment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Services\WhatsAppService;
use App\Services\WhatsAppTemplates;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the assigned deliveries with tabbeds views, search, and date range filtering.
     */
    public function index(Request $request): View
    {
        $agentId = Auth::id();

        // Resolve active tab using status or tab parameters
        $status = $request->get('status');
        $tab = $request->get('tab');

        if ($status === 'active') {
            $tab = 'active';
        } elseif ($status === 'delivered') {
            $tab = 'completed';
        }

        if (!$tab || !in_array($tab, ['active', 'completed'])) {
            $tab = 'active';
        }
        
        // Base Query joining orders table to support search and sorting on delivery_time
        $query = DeliveryAssignment::where('delivery_agent_id', $agentId)
            ->join('orders', 'delivery_assignments.order_id', '=', 'orders.id')
            ->select('delivery_assignments.*')
            ->with(['order.customer']);

        // Search by order number
        if ($request->filled('search')) {
            $query->where('orders.order_number', 'like', '%' . $request->search . '%');
        }

        // Date range filter for assigned_at
        if ($request->filled('start_date')) {
            $query->whereDate('delivery_assignments.assigned_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('delivery_assignments.assigned_at', '<=', $request->end_date);
        }

        // Scope and sort based on active tab
        if ($tab === 'active') {
            $query->whereIn('delivery_assignments.status', ['assigned', 'picked_up', 'on_the_way'])
                  ->orderBy('delivery_assignments.assigned_at', 'desc');
        } else {
            $query->where('delivery_assignments.status', 'delivered')
                  ->orderBy('delivery_assignments.delivered_at', 'desc');
        }

        // Paginate to 10 items per page as requested
        $assignments = $query->paginate(10)->withQueryString();

        return view('delivery.deliveries.index', compact('assignments', 'tab'));
    }

    /**
     * Display the specified delivery assignment detail.
     */
    public function show(DeliveryAssignment $assignment): View
    {
        // Ownership Check
        if ($assignment->delivery_agent_id !== Auth::id()) {
            abort(403, 'This delivery is not assigned to you.');
        }

        $assignment->load(['order.customer', 'order.orderItems.service', 'order.payment']);

        return view('delivery.deliveries.show', compact('assignment'));
    }

    /**
     * Update the delivery assignment status in a strict sequence.
     */
    public function updateStatus(Request $request, DeliveryAssignment $assignment): RedirectResponse
    {
        // Ownership Check
        if ($assignment->delivery_agent_id !== Auth::id()) {
            abort(403, 'This delivery is not assigned to you.');
        }

        // Input validation
        $request->validate([
            'status' => 'required|string|in:picked_up,on_the_way,delivered',
        ]);

        $currentStatus = $assignment->status;
        $newStatus = $request->status;

        // Enforce sequence transition
        $validNext = [
            'assigned'   => 'picked_up',
            'picked_up'  => 'on_the_way',
            'on_the_way' => 'delivered',
        ];

        if (!isset($validNext[$currentStatus]) || $validNext[$currentStatus] !== $newStatus) {
            Log::warning("Invalid status transition attempt by Agent ID " . Auth::id() . " on Assignment ID {$assignment->id} from '{$currentStatus}' to '{$newStatus}'");
            return redirect()->back()->with('error', 'Invalid status transition. Deliveries must be updated in sequence.');
        }

        // Perform updates within a Database Transaction
        try {
            DB::transaction(function () use ($assignment, $newStatus) {
                $assignment->status = $newStatus;

                if ($newStatus === 'picked_up') {
                    $assignment->picked_up_at = now();
                    $assignment->order->status = 'out_for_delivery';
                    $assignment->order->save();
                } elseif ($newStatus === 'on_the_way') {
                    // Status is updated on assignment model; no additional updates needed
                } elseif ($newStatus === 'delivered') {
                    $assignment->delivered_at = now();
                    $assignment->order->status = 'delivered';
                    $assignment->order->delivery_time = now();

                    // Collect cash payment if cash on delivery
                    if ($assignment->order->payment_method === 'cash') {
                        $assignment->order->payment_status = 'paid';
                        if ($assignment->order->payment) {
                            $assignment->order->payment->status = 'completed';
                            $assignment->order->payment->paid_at = now();
                            $assignment->order->payment->save();

                            // Event 3: Cash payment confirmed notification to customer
                            \App\Services\NotificationService::paymentConfirmed($assignment->order->payment);
                        }
                    }
                    $assignment->order->save();
                }

                $assignment->save();
            });
        } catch (\Exception $e) {
            Log::error("Database transaction failed for Delivery status update (Assignment ID: {$assignment->id}): " . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update status due to a database error. Please try again.');
        }

        // Trigger customer/admin notifications
        if ($newStatus === 'delivered') {
            \App\Services\NotificationService::deliveryCompleted($assignment->order);
            
            // WhatsApp notification for delivered
            try {
                $order     = $assignment->order->load('customer');
                $waMessage = WhatsAppTemplates::orderDelivered($order);
                WhatsAppService::send(
                    $waMessage,
                    $order->customer->phone ?? null
                );
            } catch (\Exception $e) {
                Log::error(
                    'WhatsApp delivered failed: '
                    . $e->getMessage()
                );
            }

            return redirect()->route('delivery.deliveries.index')->with('success', "Order {$assignment->order->order_number} marked as delivered successfully!");
        } else {
            \App\Services\NotificationService::orderStatusUpdated($assignment->order, 'out_for_delivery');
        }

        return redirect()->route('delivery.deliveries.show', $assignment)->with('success', 'Delivery status updated successfully.');
    }
}
