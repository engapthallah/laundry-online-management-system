<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Display a listing of all customer reviews.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $query = Review::with(['customer', 'order.orderItems.service']);

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // Search by order number or customer name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($customerQuery) use ($search) {
                    $customerQuery->where('name', 'like', "%{$search}%");
                })->orWhereHas('order', function($orderQuery) use ($search) {
                    $orderQuery->where('order_number', 'like', "%{$search}%");
                });
            });
        }

        $reviews = $query->latest()->paginate(15)->withQueryString();

        // Summary analytics
        $averageRating = Review::avg('rating') ?: 0.0;
        $totalReviews = Review::count();
        $fiveStarCount = Review::where('rating', 5)->count();
        $oneStarCount = Review::where('rating', 1)->count();

        return view('admin.reviews.index', compact(
            'reviews',
            'averageRating',
            'totalReviews',
            'fiveStarCount',
            'oneStarCount'
        ));
    }

    /**
     * Display the specified review details.
     *
     * @param \App\Models\Review $review
     * @return \Illuminate\View\View
     */
    public function show(Review $review): View
    {
        $review->load(['customer.orders', 'order.orderItems.service']);

        return view('admin.reviews.show', compact('review'));
    }

    /**
     * Remove the specified review from storage.
     *
     * @param \App\Models\Review $review
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return redirect()->route('admin.reviews.index')
            ->with('success', 'Review deleted successfully.');
    }
}
