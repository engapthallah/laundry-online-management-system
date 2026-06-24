<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Models\SupportMessage;
use App\Models\Review;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        // Stats Cards - Row 1
        $totalOrders = Order::count();
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $activeCustomers = User::where('role', 'customer')->where('is_active', true)->count();
        $confirmedOrders = Order::where('status', 'pending_pickup')->count();

        // Stats Cards - Row 2
        $ordersToday = Order::whereDate('created_at', Carbon::today())->count();
        $revenueThisMonth = Payment::where('status', 'completed')
            ->whereMonth('paid_at', Carbon::now()->month)
            ->whereYear('paid_at', Carbon::now()->year)
            ->sum('amount');
        $openSupportMessages = SupportMessage::where('status', 'pending')->count();
        $averageRating = round(Review::avg('rating') ?? 0.0, 1);

        // Chart 1: Orders by Status
        $statuses = [
            'pending_pickup',
            'picked_up_from_customer',
            'delivered_to_laundry',
            'processing',
            'ready_for_delivery',
            'picked_up_from_laundry',
            'on_the_way',
            'delivered',
            'cancelled'
        ];
        $ordersByStatusData = [];
        foreach ($statuses as $status) {
            $ordersByStatusData[$status] = Order::where('status', $status)->count();
        }

        // Chart 2: Revenue Last 7 Days
        $revenueLast7DaysData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $formattedDate = $date->format('Y-m-d');
            $labelDate = $date->format('M d');
            
            $dayRevenue = Payment::where('status', 'completed')
                ->whereDate('paid_at', $formattedDate)
                ->sum('amount');
                
            $revenueLast7DaysData[$labelDate] = $dayRevenue;
        }

        // Recent Activity Tables
        $latestOrders = Order::with('customer')->latest()->take(5)->get();
        $latestSupportMessages = SupportMessage::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalOrders',
            'totalRevenue',
            'activeCustomers',
            'confirmedOrders',
            'ordersToday',
            'revenueThisMonth',
            'openSupportMessages',
            'averageRating',
            'ordersByStatusData',
            'revenueLast7DaysData',
            'latestOrders',
            'latestSupportMessages'
        ));
    }
}
