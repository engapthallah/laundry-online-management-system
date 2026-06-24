<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Order::with(['customer', 'orderItems']);

        // Search by order number or customer name
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cQ) use ($search) {
                      $cQ->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->input('payment_status'));
        }

        // Filter by date range (from)
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->input('date_from'));
        }

        // Filter by date range (to)
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->input('date_to'));
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): View
    {
        // Load relationships
        $order->load(['customer', 'staff', 'orderItems.service', 'payment', 'deliveryAssignment.deliveryAgent']);
        
        // Get active staff members that can be assigned
        $staffMembers = User::where('role', 'staff')->where('is_active', true)->get();

        return view('admin.orders.show', compact('order', 'staffMembers'));
    }

    /**
     * Update the status of the specified order.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => [
                'required',
                'in:confirmed,processing,ready_for_delivery,out_for_delivery,delivered,cancelled'
            ],
        ]);

        $order->status = $validated['status'];
        
        // If order is delivered, update payment status if payment method was cash
        if ($validated['status'] === 'delivered' && $order->payment_status === 'pending' && $order->payment_method === 'cash') {
            $order->payment_status = 'paid';
            
            // Also update status of payment record if it exists
            if ($order->payment) {
                $order->payment->status = 'completed';
                $order->payment->paid_at = now();
                $order->payment->save();
            }
        }

        $order->save();

        // Trigger customer/admin notifications
        if ($validated['status'] === 'confirmed') {
            \App\Services\NotificationService::orderStatusUpdated($order, 'confirmed');
        } elseif ($validated['status'] === 'delivered') {
            \App\Services\NotificationService::deliveryCompleted($order);
        } else {
            \App\Services\NotificationService::orderStatusUpdated($order, $validated['status']);
        }

        return back()->with('success', "Order #{$order->order_number} status updated to " . str_replace('_', ' ', $validated['status']) . ".");
    }

    /**
     * Assign laundry staff to the specified order.
     */
    public function assignStaff(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'staff_id' => ['required', 'exists:users,id'],
        ]);

        // Check if user has staff role
        $staff = User::findOrFail($validated['staff_id']);
        if ($staff->role !== 'staff') {
            return back()->with('error', 'The selected user is not a staff member.');
        }

        $order->staff_id = $staff->id;
        $order->save();

        return back()->with('success', "Order #{$order->order_number} successfully assigned to staff: {$staff->name}.");
    }
}
