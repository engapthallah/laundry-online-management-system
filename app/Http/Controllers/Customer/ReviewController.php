<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ReviewController extends Controller
{
    /**
     * Display a listing of customer's reviews.
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        $reviews = Review::where('customer_id', Auth::id())
            ->with(['order.orderItems.service'])
            ->latest()
            ->paginate(10);

        $reviewableOrders = Order::where('customer_id', Auth::id())
            ->where('status', 'delivered')
            ->whereDoesntHave('review')
            ->latest()
            ->get();

        return view('customer.reviews.index', compact('reviews', 'reviewableOrders'));
    }

    /**
     * Show the form for creating a new review.
     *
     * @param \App\Models\Order $order
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create(Order $order): View|RedirectResponse
    {
        if ($order->customer_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'delivered') {
            abort(403, 'You can only review delivered orders.');
        }

        if (Review::where('order_id', $order->id)->exists()) {
            return redirect()->route('customer.reviews.index')
                ->with('error', 'You have already submitted a review for this order.');
        }

        $order->load('orderItems.service');

        return view('customer.reviews.create', compact('order'));
    }

    /**
     * Store a newly created review in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['nullable', 'string', 'max:1000'],
            'order_id' => ['required', 'exists:orders,id'],
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->customer_id !== Auth::id()) {
            abort(403);
        }

        if ($order->status !== 'delivered') {
            return redirect()->back()->with('error', 'You can only review delivered orders.');
        }

        if (Review::where('order_id', $order->id)->exists()) {
            return redirect()->route('customer.reviews.index')
                ->with('error', 'You have already submitted a review for this order.');
        }

        Review::create([
            'order_id' => $order->id,
            'customer_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        // Create admin notification
        try {
            $customer = Auth::user();
            $admin = User::where('role', 'admin')->first();
            if ($admin && $customer) {
                NotificationService::send(
                    $admin->id,
                    "New {$request->rating}-Star Review Received",
                    "{$customer->name} left a {$request->rating}-star review for order {$order->order_number}.",
                    'system',
                    $order->id
                );
            }
        } catch (\Exception $e) {
            Log::error("Failed to send admin notification for review: " . $e->getMessage());
        }

        return redirect()->route('customer.reviews.index')
            ->with('success', 'Thank you! Your review has been submitted successfully.');
    }
}
