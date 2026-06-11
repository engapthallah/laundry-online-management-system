@extends('layouts.customer-portal')

@section('content')
@php
    $ratingLabels = [
        1 => 'Poor',
        2 => 'Fair',
        3 => 'Good',
        4 => 'Very Good',
        5 => 'Excellent'
    ];
@endphp

<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">My Reviews</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">My Reviews</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Section 1 — Reviewable Orders Banner -->
    @if($reviewableOrders->isNotEmpty())
        <div class="alert alert-info border-0 shadow-sm rounded-4 p-4 mb-4">
            <div class="d-flex align-items-center gap-2 mb-3">
                <i class="fa-solid fa-circle-info fs-4 text-info"></i>
                <h5 class="alert-heading fw-bold mb-0">You have {{ $reviewableOrders->count() }} delivered order(s) awaiting your review!</h5>
            </div>
            
            <div class="row g-3">
                @foreach($reviewableOrders as $order)
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm rounded-3 bg-white h-100">
                            <div class="card-body p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold text-dark">Order #{{ $order->order_number }}</span>
                                        <span class="badge bg-success-subtle text-success small">Delivered</span>
                                    </div>
                                    <div class="text-muted small mb-1">
                                        <strong>Delivery Date:</strong> {{ $order->updated_at->format('M d, Y') }}
                                    </div>
                                    <div class="text-muted small mb-3">
                                        <strong>Services:</strong> 
                                        {{ $order->orderItems->map(fn($item) => $item->service->name ?? 'N/A')->implode(', ') }}
                                    </div>
                                </div>
                                <a href="{{ route('customer.reviews.create', $order) }}" class="btn btn-warning btn-sm fw-bold w-100 text-dark rounded-3">
                                    <i class="fa-solid fa-star me-1"></i>Leave Review
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Section 2 — My Submitted Reviews -->
    <h4 class="fw-bold text-dark mb-3">My Submitted Reviews</h4>

    @if($reviews->isEmpty())
        <!-- Empty State -->
        <div class="card border-0 shadow-sm rounded-4 bg-white text-center py-5 px-4 mb-4">
            <div class="card-body">
                <div class="text-warning mb-3">
                    <i class="fa-solid fa-star fa-3x"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1">You haven't submitted any reviews yet.</h5>
                <p class="text-muted mb-0">Your feedback helps us improve our service.</p>
            </div>
        </div>
    @else
        <!-- Grid of Review Cards -->
        <div class="row g-4 mb-4">
            @foreach($reviews as $review)
                <div class="col-12 col-md-6">
                    <div class="card border-0 shadow-sm rounded-4 bg-white h-100 d-flex flex-column justify-content-between">
                        <!-- Card Header -->
                        <div class="card-header bg-white border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
                            <span class="fw-bold text-primary">Order #{{ $review->order->order_number ?? 'N/A' }}</span>
                            <span class="text-muted small">{{ $review->created_at->format('M d, Y') }}</span>
                        </div>
                        
                        <!-- Card Body -->
                        <div class="card-body px-4 py-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <div class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <i class="fa-solid fa-star" style="color:#ffc107"></i>
                                        @else
                                            <i class="fa-regular fa-star" style="color:#dee2e6"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="badge bg-warning-subtle text-warning fw-bold small text-dark">
                                    {{ $ratingLabels[$review->rating] ?? 'Good' }}
                                </span>
                            </div>
                            
                            <p class="mb-0 text-dark">
                                @if($review->comment)
                                    "{{ $review->comment }}"
                                @else
                                    <span class="text-muted italic">No comment provided</span>
                                @endif
                            </p>
                        </div>
                        
                        <!-- Card Footer -->
                        <div class="card-footer bg-light border-0 px-4 py-3 rounded-bottom-4">
                            <div class="text-muted small mb-1">
                                <strong>Services:</strong> 
                                {{ $review->order ? $review->order->orderItems->map(fn($item) => $item->service->name ?? 'N/A')->implode(', ') : 'N/A' }}
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">
                                    <strong>Total:</strong> ${{ number_format($review->order->total_price ?? 0, 2) }}
                                </span>
                                <span class="text-muted small italic">
                                    <i class="fa-solid fa-lock me-1"></i>Cannot edit submitted reviews
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $reviews->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
