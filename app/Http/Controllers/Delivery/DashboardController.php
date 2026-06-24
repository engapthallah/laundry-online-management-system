<?php

namespace App\Http\Controllers\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
        $totalAssigned = Order::where('delivery_agent_id', $agentId)->count();

        // 2. Active Deliveries
        $activeDeliveriesCount = Order::where('delivery_agent_id', $agentId)
            ->whereIn('status', [
                'pending_pickup',
                'picked_up_from_customer',
                'ready_for_delivery',
                'picked_up_from_laundry',
                'on_the_way'
            ])
            ->count();

        // 3. Delivered Today
        $deliveredTodayCount = Order::where('delivery_agent_id', $agentId)
            ->where('status', 'delivered')
            ->whereDate('delivery_time', Carbon::today())
            ->count();

        // 4. Total Deliveries This Month
        $deliveriesThisMonthCount = Order::where('delivery_agent_id', $agentId)
            ->where('status', 'delivered')
            ->whereMonth('delivery_time', Carbon::now()->month)
            ->whereYear('delivery_time', Carbon::now()->year)
            ->count();

        // 5. Up to 6 current active delivery assignments (orders)
        $activeAssignments = Order::where('delivery_agent_id', $agentId)
            ->whereIn('status', [
                'pending_pickup',
                'picked_up_from_customer',
                'ready_for_delivery',
                'picked_up_from_laundry',
                'on_the_way'
            ])
            ->with(['customer'])
            ->orderBy('updated_at', 'desc')
            ->limit(6)
            ->get();

        // 6. Performance Summary Chart data (doughnut status counts this month)
        $statusCounts = Order::where('delivery_agent_id', $agentId)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $chartData = [
            'pending_pickup' => $statusCounts['pending_pickup'] ?? 0,
            'picked_up_from_customer' => $statusCounts['picked_up_from_customer'] ?? 0,
            'ready_for_delivery' => $statusCounts['ready_for_delivery'] ?? 0,
            'picked_up_from_laundry' => $statusCounts['picked_up_from_laundry'] ?? 0,
            'on_the_way' => $statusCounts['on_the_way'] ?? 0,
            'delivered' => $statusCounts['delivered'] ?? 0,
        ];

        // Check if there are any assignments to render the chart
        $hasData = array_sum($chartData) > 0;

        // 7. Today's Schedule
        $todaysSchedule = Order::where('delivery_agent_id', $agentId)
            ->where(function($query) {
                $query->whereDate('pickup_time', Carbon::today())
                      ->orWhereDate('delivery_time', Carbon::today())
                      ->orWhereIn('status', [
                          'pending_pickup',
                          'picked_up_from_customer',
                          'ready_for_delivery',
                          'picked_up_from_laundry',
                          'on_the_way'
                      ]);
            })
            ->with(['customer'])
            ->orderByRaw('COALESCE(delivery_time, pickup_time) ASC')
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
