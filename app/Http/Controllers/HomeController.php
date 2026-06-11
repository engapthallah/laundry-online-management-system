<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Order;
use App\Models\User;
use App\Models\Review;

class HomeController extends Controller
{
    /**
     * Display the public home page with statistics, services preview, and reviews.
     */
    public function index()
    {
        $servicesCount = Service::where('is_active', true)->count();
        $ordersCompleted = Order::where('status', 'delivered')->count();
        $happyCustomers = User::where('role', 'customer')->count();
        $avgRating = Review::avg('rating') ?? 5.0;
        
        $services = Service::where('is_active', true)->take(3)->get();
        $reviews = Review::with(['order', 'customer'])->latest()->take(6)->get();

        return view('public.home', compact(
            'servicesCount',
            'ordersCompleted',
            'happyCustomers',
            'avgRating',
            'services',
            'reviews'
        ));
    }
}
