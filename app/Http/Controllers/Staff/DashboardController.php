<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    use HasStaffOrderFilters;

    /**
     * Display the staff dashboard.
     */
    public function index(Request $request): View
    {
        $staffId = Auth::id();

        // 1. Total Assigned Orders
        $totalAssigned = Order::where('staff_id', $staffId)->count();

        // 2. Active Orders
        $activeOrdersCount = Order::where('staff_id', $staffId)
            ->whereIn('status', ['delivered_to_laundry', 'processing'])
            ->count();

        // 3. Ready for Delivery
        $readyForDeliveryCount = Order::where('staff_id', $staffId)
            ->where('status', 'ready_for_delivery')
            ->count();

        // 4. Completed Today
        $completedTodayCount = Order::where('staff_id', $staffId)
            ->where('status', 'ready_for_delivery')
            ->whereDate('updated_at', Carbon::today())
            ->count();

        // 5. Active orders (Recent default vs full filtered)
        $isFiltered = $request->anyFilled(['search', 'status', 'date_from', 'date_to']);

        if ($isFiltered) {
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
            $activeOrders = $query->orderBy('updated_at', 'desc')->get();
        } else {
            $activeOrders = Order::where('staff_id', $staffId)
                ->whereIn('status', ['delivered_to_laundry', 'processing'])
                ->with(['customer', 'orderItems.service'])
                ->orderBy('created_at', 'desc')
                ->limit(8)
                ->get();
        }

        // 6. Quick Summary Chart data (statuses with at least 1 order)
        $statusCountsQuery = Order::where('staff_id', $staffId)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->get();

        $chartLabels = [];
        $chartData = [];
        foreach ($statusCountsQuery as $row) {
            $chartLabels[] = ucwords(str_replace('_', ' ', $row->status));
            $chartData[] = $row->total;
        }

        return view('staff.dashboard', compact(
            'totalAssigned',
            'activeOrdersCount',
            'readyForDeliveryCount',
            'completedTodayCount',
            'activeOrders',
            'chartLabels',
            'chartData'
        ));
    }
}
