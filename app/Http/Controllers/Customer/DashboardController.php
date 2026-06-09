<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the customer dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();

        // Stats Cards
        $totalOrders = $user->orders()->count();
        $activeOrders = $user->orders()->whereNotIn('status', ['delivered', 'cancelled'])->count();
        $completedOrders = $user->orders()->where('status', 'delivered')->count();
        $totalSpent = $user->payments()->where('status', 'completed')->sum('amount');

        // Recent Orders (last 5)
        $recentOrders = $user->orders()
            ->with('orderItems.service')
            ->latest()
            ->take(5)
            ->get();

        // Motivational message based on order count
        if ($totalOrders === 0) {
            $motivationalMessage = "Welcome to LOMS! Start your clean journey by placing your first order today.";
        } elseif ($totalOrders <= 5) {
            $motivationalMessage = "Thanks for trusting us with your clothes! We are committed to keeping them fresh and clean.";
        } else {
            $motivationalMessage = "You're a laundry superstar! Thank you for being a loyal LOMS customer.";
        }

        $currentDate = Carbon::now()->format('F d, Y');

        return view('customer.dashboard', compact(
            'user',
            'totalOrders',
            'activeOrders',
            'completedOrders',
            'totalSpent',
            'recentOrders',
            'motivationalMessage',
            'currentDate'
        ));
    }
}
