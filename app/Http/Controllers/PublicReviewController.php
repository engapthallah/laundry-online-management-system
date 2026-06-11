<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicReviewController extends Controller
{
    /**
     * Display LOMS's public reputation reviews page.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request): View
    {
        $query = Review::with(['order.orderItems.service', 'customer']);

        // Filter by rating
        if ($request->filled('rating')) {
            $query->where('rating', $request->rating);
        }

        // Sort by
        $sortBy = $request->input('sort_by', 'recent');
        switch ($sortBy) {
            case 'highest':
                $query->orderBy('rating', 'desc')->orderBy('created_at', 'desc');
                break;
            case 'lowest':
                $query->orderBy('rating', 'asc')->orderBy('created_at', 'desc');
                break;
            case 'recent':
            default:
                $query->latest();
                break;
        }

        $reviews = $query->paginate(12)->withQueryString();

        $stats = [
            'average' => Review::avg('rating') ?: 0,
            'total' => Review::count(),
            'five_star' => Review::where('rating', 5)->count(),
            'four_star' => Review::where('rating', 4)->count(),
            'three_star' => Review::where('rating', 3)->count(),
            'two_star' => Review::where('rating', 2)->count(),
            'one_star' => Review::where('rating', 1)->count(),
        ];

        return view('public.reviews', compact('reviews', 'stats'));
    }
}
