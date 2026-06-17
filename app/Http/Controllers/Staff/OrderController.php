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

class OrderController extends Controller
{
    /**
     * Display a listing of assigned orders with search, status filtering, date filters, and header sorting.
     */
    public function index(Request $request): View
    {
        $staffId = Auth::id();
        $query = Order::where('staff_id', $staffId)->with(['customer', 'orderItems.service']);

        // Search by order number
        if ($request->filled('search')) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        // Filter by status (including "active" status parameter from sidebar link)
        if ($request->filled('status')) {
            $status = $request->status;
            if ($status === 'active') {
                $query->whereIn('status', ['confirmed', 'washing', 'drying', 'ironing', 'folding']);
            } else {
                $query->where('status', $status);
            }
        }

        // Filter by date range (pickup_time)
        if ($request->filled('date_from')) {
            $query->whereDate('pickup_time', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('pickup_time', '<=', $request->date_to);
        }

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
            'status' => 'required|string|in:washing,drying,ironing,folding,ready_for_delivery',
        ]);

        $currentStatus = $order->status;
        $newStatus = $request->status;

        // Enforce transition rules
        $validNext = [
            'confirmed'          => 'washing',
            'washing'            => 'drying',
            'drying'             => 'ironing',
            'ironing'            => 'folding',
            'folding'            => 'ready_for_delivery',
        ];

        if (!isset($validNext[$currentStatus]) || $validNext[$currentStatus] !== $newStatus) {
            Log::warning("Invalid status transition attempt by Staff ID " . Auth::id() . " on Order #{$order->order_number} from '{$currentStatus}' to '{$newStatus}'");
            return redirect()->back()->with('error', 'Invalid status transition. You must update orders sequentially.');
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
            'washing'            => 'Washing',
            'drying'             => 'Drying',
            'ironing'            => 'Ironing',
            'folding'            => 'Folding',
            'ready_for_delivery' => 'Ready for Delivery',
        ];
        $label = $statusLabels[$newStatus] ?? ucwords(str_replace('_', ' ', $newStatus));

        return redirect()->route('staff.orders.show', $order)->with('success', "Order status successfully updated to {$label}.");
    }
}
