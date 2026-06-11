@extends('layouts.website')

@section('title', 'Customer Reviews — Iimaan Dry Cleaner')

@section('content')
<!-- Custom Styles for Reviews Page -->
<style>
    .star-gold {
        color: var(--accent);
    }
    .star-muted {
        color: #e2e8f0;
    }
    .reviews-hero {
        background: linear-gradient(135deg, #eff6ff 0%, #f0f9ff 50%, #fefce8 100%);
        padding: 4rem 0;
    }
    .rating-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        border: none;
    }
    .progress-bar-custom {
        background-color: var(--accent);
    }
    .filter-pill {
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 500;
        padding: 0.4rem 1.2rem;
        transition: var(--transition);
        text-decoration: none;
    }
    .filter-pill-active {
        background-color: var(--primary) !important;
        color: white !important;
        border: 1px solid var(--primary);
    }
    .filter-pill-inactive {
        background-color: transparent;
        color: var(--muted) !important;
        border: 1px solid var(--border);
    }
    .filter-pill-inactive:hover {
        background-color: var(--light);
        border-color: var(--muted);
    }
    .review-item-card {
        background: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        border: none;
        transition: var(--transition);
    }
    .review-item-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }
    .review-avatar-circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: var(--primary);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
    }
</style>

<!-- Hero Section with Aggregate Ratings and Distributions -->
<section class="reviews-hero border-bottom">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6 text-center text-lg-start">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2 justify-content-center justify-content-lg-start">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-decoration-none">Home</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Reviews</li>
                    </ol>
                </nav>
                <h1 class="display-5 fw-extrabold text-dark mb-2">What Our Customers Say</h1>
                <p class="lead text-muted mb-0">Real feedback from verified Hargeisa families and business owners.</p>
            </div>

            <!-- Average Rating Box -->
            <div class="col-md-6 col-lg-3">
                <div class="rating-card p-4 text-center">
                    <h2 class="display-4 fw-extrabold text-dark mb-0">{{ number_format($stats['average'], 1) }}</h2>
                    <div class="my-2">
                        @php
                            $avg = $stats['average'];
                            $whole = floor($avg);
                            $fraction = $avg - $whole;
                        @endphp
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $whole)
                                <i class="fas fa-star star-gold fs-5"></i>
                            @elseif($i == $whole + 1 && $fraction >= 0.25 && $fraction <= 0.75)
                                <i class="fas fa-star-half-alt star-gold fs-5"></i>
                            @elseif($i == $whole + 1 && $fraction > 0.75)
                                <i class="fas fa-star star-gold fs-5"></i>
                            @else
                                <i class="far fa-star star-muted fs-5"></i>
                            @endif
                        @endfor
                    </div>
                    <p class="text-muted small mb-0">{{ $stats['total'] }} verified reviews</p>
                </div>
            </div>

            <!-- Distribution Progress Bars -->
            <div class="col-md-6 col-lg-3">
                <div class="rating-card p-4">
                    @php
                        $total = $stats['total'] ?: 1;
                        $pctFive = ($stats['five_star'] / $total) * 100;
                        $pctFour = ($stats['four_star'] / $total) * 100;
                        $pctThree = ($stats['three_star'] / $total) * 100;
                        $pctTwo = ($stats['two_star'] / $total) * 100;
                        $pctOne = ($stats['one_star'] / $total) * 100;
                    @endphp

                    <!-- 5 Star -->
                    <div class="d-flex align-items-center mb-2 text-muted small">
                        <span style="width: 50px;" class="fw-semibold">5 Star</span>
                        <div class="progress flex-grow-1 mx-2" style="height: 6px;">
                            <div class="progress-bar progress-bar-custom" role="progressbar" style="width: {{ $pctFive }}%"></div>
                        </div>
                        <span style="width: 30px;" class="text-end fw-semibold">{{ $stats['five_star'] }}</span>
                    </div>
                    <!-- 4 Star -->
                    <div class="d-flex align-items-center mb-2 text-muted small">
                        <span style="width: 50px;" class="fw-semibold">4 Star</span>
                        <div class="progress flex-grow-1 mx-2" style="height: 6px;">
                            <div class="progress-bar progress-bar-custom" role="progressbar" style="width: {{ $pctFour }}%"></div>
                        </div>
                        <span style="width: 30px;" class="text-end fw-semibold">{{ $stats['four_star'] }}</span>
                    </div>
                    <!-- 3 Star -->
                    <div class="d-flex align-items-center mb-2 text-muted small">
                        <span style="width: 50px;" class="fw-semibold">3 Star</span>
                        <div class="progress flex-grow-1 mx-2" style="height: 6px;">
                            <div class="progress-bar progress-bar-custom" role="progressbar" style="width: {{ $pctThree }}%"></div>
                        </div>
                        <span style="width: 30px;" class="text-end fw-semibold">{{ $stats['three_star'] }}</span>
                    </div>
                    <!-- 2 Star -->
                    <div class="d-flex align-items-center mb-2 text-muted small">
                        <span style="width: 50px;" class="fw-semibold">2 Star</span>
                        <div class="progress flex-grow-1 mx-2" style="height: 6px;">
                            <div class="progress-bar progress-bar-custom" role="progressbar" style="width: {{ $pctTwo }}%"></div>
                        </div>
                        <span style="width: 30px;" class="text-end fw-semibold">{{ $stats['two_star'] }}</span>
                    </div>
                    <!-- 1 Star -->
                    <div class="d-flex align-items-center text-muted small">
                        <span style="width: 50px;" class="fw-semibold">1 Star</span>
                        <div class="progress flex-grow-1 mx-2" style="height: 6px;">
                            <div class="progress-bar progress-bar-custom" role="progressbar" style="width: {{ $pctOne }}%"></div>
                        </div>
                        <span style="width: 30px;" class="text-end fw-semibold">{{ $stats['one_star'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Filter and Sorting Area -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="card border-0 shadow-sm rounded-4 mb-5" style="background-color: var(--light);">
            <div class="card-body p-3">
                <form method="GET" action="{{ route('reviews.public') }}" class="row g-3 align-items-center">
                    <!-- Rating Filters -->
                    <div class="col-lg-7 d-flex flex-wrap gap-2 align-items-center">
                        <span class="text-dark small fw-bold me-2">Filter Rating:</span>
                        <a href="{{ route('reviews.public', ['rating' => '', 'sort_by' => request('sort_by')]) }}" class="filter-pill {{ !request('rating') ? 'filter-pill-active' : 'filter-pill-inactive' }}">All</a>
                        <a href="{{ route('reviews.public', ['rating' => 5, 'sort_by' => request('sort_by')]) }}" class="filter-pill {{ request('rating') == 5 ? 'filter-pill-active' : 'filter-pill-inactive' }}">5 ★</a>
                        <a href="{{ route('reviews.public', ['rating' => 4, 'sort_by' => request('sort_by')]) }}" class="filter-pill {{ request('rating') == 4 ? 'filter-pill-active' : 'filter-pill-inactive' }}">4 ★</a>
                        <a href="{{ route('reviews.public', ['rating' => 3, 'sort_by' => request('sort_by')]) }}" class="filter-pill {{ request('rating') == 3 ? 'filter-pill-active' : 'filter-pill-inactive' }}">3 ★</a>
                        <a href="{{ route('reviews.public', ['rating' => 2, 'sort_by' => request('sort_by')]) }}" class="filter-pill {{ request('rating') == 2 ? 'filter-pill-active' : 'filter-pill-inactive' }}">2 ★</a>
                        <a href="{{ route('reviews.public', ['rating' => 1, 'sort_by' => request('sort_by')]) }}" class="filter-pill {{ request('rating') == 1 ? 'filter-pill-active' : 'filter-pill-inactive' }}">1 ★</a>
                    </div>
                    
                    <input type="hidden" name="rating" value="{{ request('rating') }}">
                    
                    <!-- Sorting Dropdown -->
                    <div class="col-lg-5 d-flex align-items-center justify-content-lg-end gap-2">
                        <label for="sort_by" class="text-dark small fw-bold text-nowrap">Sort By:</label>
                        <select name="sort_by" id="sort_by" class="form-select form-select-sm border shadow-sm rounded-3 py-2 px-3" style="width: 180px;" onchange="this.form.submit()">
                            <option value="recent" {{ request('sort_by') == 'recent' ? 'selected' : '' }}>Most Recent</option>
                            <option value="highest" {{ request('sort_by') == 'highest' ? 'selected' : '' }}>Highest Rated</option>
                            <option value="lowest" {{ request('sort_by') == 'lowest' ? 'selected' : '' }}>Lowest Rated</option>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <!-- Reviews Grid -->
        @if($reviews->isEmpty())
            <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-4" style="background-color: var(--light);">
                <div class="card-body">
                    <i class="fas fa-comments text-muted fa-3x mb-3"></i>
                    <h4 class="fw-bold text-dark mb-2">No reviews found.</h4>
                    <p class="text-muted mb-4">No reviews match your selected filter. Be the first to place an order and leave feedback!</p>
                    @auth
                        <a href="{{ route('customer.orders.create') }}" class="btn btn-primary fw-bold px-4 rounded-3">Place an Order</a>
                    @else
                        <a href="{{ route('register') }}" class="btn btn-primary fw-bold px-4 rounded-3">Place an Order</a>
                    @endauth
                </div>
            </div>
        @else
            @php
                $ratingLabels = [
                    1 => 'Poor',
                    2 => 'Fair',
                    3 => 'Good',
                    4 => 'Very Good',
                    5 => 'Excellent'
                ];
            @endphp
            <div class="row g-4 mb-5">
                @foreach($reviews as $review)
                    <div class="col-lg-4 col-md-6">
                        <div class="card review-item-card h-100 p-4 d-flex flex-column justify-content-between">
                            <div>
                                <!-- Card Rating and Date -->
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fa{{ $i <= $review->rating ? 's' : 'r' }} fa-star star-gold"></i>
                                        @endfor
                                    </div>
                                    <span class="text-muted small">{{ $review->created_at->format('F Y') }}</span>
                                </div>
                                
                                <!-- Card Quote Content -->
                                <p class="text-muted small mb-4" style="font-style: italic; line-height: 1.6;">
                                    @if($review->comment)
                                        "{{ $review->comment }}"
                                    @else
                                        "Rated us {{ $ratingLabels[$review->rating] ?? 'Good' }}."
                                    @endif
                                </p>
                            </div>
                            
                            <!-- Card Author/Service Footer -->
                            <div class="pt-3 border-top d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="review-avatar-circle">
                                        {{ strtoupper(substr($review->customer->name ?? 'A', 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 text-dark fw-bold small">
                                            {{ explode(' ', trim($review->customer->name ?? 'Customer'))[0] }}
                                        </h6>
                                        <span class="text-muted" style="font-size:0.75rem;">Verified Customer</span>
                                    </div>
                                </div>
                                @if($review->order)
                                    <span class="badge bg-primary bg-opacity-10 text-primary small py-1 px-3 rounded-pill" style="font-size: 0.7rem;">
                                        {{ $review->order->orderItems->map(fn($item) => $item->service->name ?? 'N/A')->first() }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination Links -->
            <div class="d-flex justify-content-center">
                {{ $reviews->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>
</section>
@endsection
