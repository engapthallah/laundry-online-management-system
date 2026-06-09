<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\DeliveryAssignment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the delivery agent dashboard.
     */
    public function index(): View
    {
        $agentId = Auth::id();

        // 1. Total Deliveries (All Time)
        $totalAssigned = DeliveryAssignment::where('delivery_agent_id', $agentId)->count();

        // 2. Active Deliveries
        $activeDeliveriesCount = DeliveryAssignment::where('delivery_agent_id', $agentId)
            ->whereIn('status', ['assigned', 'picked_up', 'on_the_way'])
            ->count();

        // 3. Delivered Today
        $deliveredTodayCount = DeliveryAssignment::where('delivery_agent_id', $agentId)
            ->where('status', 'delivered')
            ->whereDate('delivered_at', Carbon::today())
            ->count();

        // 4. Total Deliveries This Month
        $deliveriesThisMonthCount = DeliveryAssignment::where('delivery_agent_id', $agentId)
            ->where('status', 'delivered')
            ->whereMonth('delivered_at', Carbon::now()->month)
            ->whereYear('delivered_at', Carbon::now()->year)
            ->count();

        // 5. Up to 6 current active delivery assignments
        $activeAssignments = DeliveryAssignment::where('delivery_agent_id', $agentId)
            ->whereIn('status', ['assigned', 'picked_up', 'on_the_way'])
            ->with(['order.customer'])
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // 6. Performance Summary Chart data (doughnut status counts this month)
        $statusCounts = DeliveryAssignment::where('delivery_agent_id', $agentId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $chartData = [
            'assigned' => $statusCounts['assigned'] ?? 0,
            'picked_up' => $statusCounts['picked_up'] ?? 0,
            'on_the_way' => $statusCounts['on_the_way'] ?? 0,
            'delivered' => $statusCounts['delivered'] ?? 0,
        ];

        // Check if there are any assignments to render the chart
        $hasData = array_sum($chartData) > 0;

        // 7. Today's Schedule (assigned_at = today, sorted by orders.delivery_time)
        $todaysSchedule = DeliveryAssignment::where('delivery_agent_id', $agentId)
            ->whereDate('assigned_at', Carbon::today())
            ->join('orders', 'delivery_assignments.order_id', '=', 'orders.id')
            ->select('delivery_assignments.*')
            ->with(['order.customer'])
            ->orderBy('orders.delivery_time', 'asc')
            ->get();

        return view('delivery.dashboard', compact(
            'totalAssigned',
            'activeDeliveriesCount',
            'deliveredTodayCount',
            'deliveriesThisMonthCount',
            'activeAssignments',
            'chartData',
            'hasData',
            'todaysSchedule'
        ));
    }
}
