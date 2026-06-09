<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAssignment;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = DeliveryAssignment::with(['order.customer', 'deliveryAgent']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $assignments = $query->latest()->paginate(15)->withQueryString();

        return view('admin.delivery.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        // Dropdown: only shows orders with status='ready_for_delivery'
        $orders = Order::where('status', 'ready_for_delivery')
            ->whereDoesntHave('deliveryAssignment')
            ->get();

        // Dropdown: only shows active delivery agents
        $deliveryAgents = User::where('role', 'delivery')
            ->where('is_active', true)
            ->get();

        return view('admin.delivery.create', compact('orders', 'deliveryAgents'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'order_id' => ['required', 'exists:orders,id'],
            'delivery_agent_id' => ['required', 'exists:users,id'],
        ]);

        // Verify order is ready_for_delivery
        $order = Order::findOrFail($validated['order_id']);
        if ($order->status !== 'ready_for_delivery') {
            return back()->with('error', 'Only orders with status "Ready for Delivery" can be assigned.');
        }

        // Verify delivery agent exists and has delivery role
        $agent = User::findOrFail($validated['delivery_agent_id']);
        if ($agent->role !== 'delivery') {
            return back()->with('error', 'The selected user is not a delivery agent.');
        }

        // Create delivery assignment
        $assignment = new DeliveryAssignment();
        $assignment->order_id = $order->id;
        $assignment->delivery_agent_id = $agent->id;
        $assignment->status = 'assigned';
        $assignment->assigned_at = now();
        $assignment->save();

        // Update order status to out_for_delivery
        $order->status = 'out_for_delivery';
        $order->save();

        return redirect()->route('admin.delivery.index')->with('success', "Order #{$order->order_number} successfully assigned to {$agent->name}.");
    }

    /**
     * Display the specified resource.
     */
    public function show(DeliveryAssignment $delivery): View
    {
        $delivery->load(['order.customer', 'deliveryAgent']);
        return view('admin.delivery.show', compact('delivery'));
    }

    /**
     * Update the status of the specified delivery.
     */
    public function updateStatus(Request $request, DeliveryAssignment $delivery): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:assigned,picked_up,on_the_way,delivered'],
        ]);

        $delivery->status = $validated['status'];
        
        if ($validated['status'] === 'picked_up') {
            $delivery->picked_up_at = now();
            // Ensure order status matches
            if ($delivery->order->status !== 'out_for_delivery') {
                $delivery->order->status = 'out_for_delivery';
                $delivery->order->save();
            }
        }

        if ($validated['status'] === 'delivered') {
            $delivery->delivered_at = now();
            
            // Update order status to delivered
            $delivery->order->status = 'delivered';
            $delivery->order->delivery_time = now();
            
            // If payment was cash and pending, mark as paid
            if ($delivery->order->payment_status === 'pending' && $delivery->order->payment_method === 'cash') {
                $delivery->order->payment_status = 'paid';
                if ($delivery->order->payment) {
                    $delivery->order->payment->status = 'completed';
                    $delivery->order->payment->paid_at = now();
                    $delivery->order->payment->save();
                }
            }
            $delivery->order->save();
        }

        $delivery->save();

        return back()->with('success', "Delivery assignment status updated to " . str_replace('_', ' ', $validated['status']) . ".");
    }
}
