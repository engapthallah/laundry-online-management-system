@extends('layouts.admin')

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
    <!-- Header Navigation -->
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div>
            <h1 class="h2 fw-bold text-dark mb-1">Review Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Reviews</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Details</li>
                </ol>
            </nav>
        </div>
        <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold rounded-3">
            <i class="fa-solid fa-arrow-left me-2"></i>Back to List
        </a>
    </div>

    <!-- Section 1 — Review Header Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-body p-4 text-center">
            <div class="text-warning mb-2">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= $review->rating)
                        <i class="fa-solid fa-star fa-2x" style="color:#ffc107"></i>
                    @else
                        <i class="far fa-star fa-2x text-muted"></i>
                    @endif
                @endfor
            </div>
            <h3 class="fw-bold mb-1">{{ $review->rating }} out of 5</h3>
            <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold mb-3">
                {{ $ratingLabels[$review->rating] ?? 'Good' }}
            </span>
            <div class="text-muted small">
                Submitted on {{ $review->created_at->format('M d, Y') }} at {{ $review->created_at->format('h:i A') }}
            </div>
        </div>
    </div>

    <!-- Section 2 — Two Column Layout -->
    <div class="row g-4 mb-4">
        <!-- Left Column — Customer Info -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="fa-solid fa-user me-2 text-primary"></i>Customer Information
                    </h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <span class="text-muted small d-block">Customer Name</span>
                            @if($review->customer)
                                <a href="{{ route('admin.users.show', $review->customer->id) }}" class="fw-bold text-primary text-decoration-none">
                                    {{ $review->customer->name }}
                                </a>
                            @else
                                <span class="fw-bold text-dark">N/A</span>
                            @endif
                        </li>
                        <li class="mb-3">
                            <span class="text-muted small d-block">Email Address</span>
                            <span class="text-dark">{{ $review->customer->email ?? 'N/A' }}</span>
                        </li>
                        <li class="mb-3">
                            <span class="text-muted small d-block">Phone Number</span>
                            <span class="text-dark">{{ $review->customer->phone ?? 'N/A' }}</span>
                        </li>
                        <li>
                            <span class="text-muted small d-block">Member Since</span>
                            <span class="text-dark">
                                {{ $review->customer ? $review->customer->created_at->format('M d, Y') : 'N/A' }}
                            </span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Column — Order Info -->
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="fa-solid fa-box-open me-2 text-primary"></i>Order Summary
                    </h5>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <span class="text-muted small d-block">Order Number</span>
                            @if($review->order)
                                <a href="{{ route('admin.orders.show', $review->order->id) }}" class="fw-bold text-primary text-decoration-none">
                                    #{{ $review->order->order_number }}
                                </a>
                            @else
                                <span class="fw-bold text-dark">N/A</span>
                            @endif
                        </li>
                        <li class="mb-3">
                            <span class="text-muted small d-block">Order Date</span>
                            <span class="text-dark">
                                {{ $review->order ? $review->order->created_at->format('M d, Y') : 'N/A' }}
                            </span>
                        </li>
                        <li class="mb-3">
                            <span class="text-muted small d-block">Delivery Date</span>
                            <span class="text-dark">
                                {{ $review->order ? $review->order->updated_at->format('M d, Y') : 'N/A' }}
                            </span>
                        </li>
                        <li class="mb-3">
                            <span class="text-muted small d-block">Order Total</span>
                            <span class="fw-bold text-dark">${{ number_format($review->order->total_price ?? 0, 2) }}</span>
                        </li>
                        <li>
                            <span class="text-muted small d-block mb-1">Services Ordered</span>
                            <div>
                                @if($review->order)
                                    @foreach($review->order->orderItems as $item)
                                        <span class="badge bg-secondary me-1 mb-1">
                                            {{ $item->service->name ?? 'N/A' }} (x{{ $item->quantity }})
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-dark">N/A</span>
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3 — Review Content -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-3">Customer Feedback</h5>
            <div class="bg-light p-4 rounded-3 border-start border-warning border-4">
                @if($review->comment)
                    <p class="mb-0 fs-5 text-dark italic">"{{ $review->comment }}"</p>
                @else
                    <p class="mb-0 text-muted italic">Customer did not leave a written comment.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Section 4 — Admin Actions -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold text-dark mb-1">Administrative Actions</h5>
                <p class="text-muted small mb-0">Delete this review if it violates policies or is spam. Reviews cannot be edited.</p>
            </div>
            <button type="button" 
                    class="btn btn-danger fw-bold rounded-3" 
                    data-bs-toggle="modal" 
                    data-bs-target="#deleteModal">
                <i class="fa-solid fa-trash me-2"></i>Delete Review
            </button>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark" id="deleteModalLabel">Delete Review</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <p>Are you sure you want to delete this review by {{ $review->customer->name ?? 'this customer' }}? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light border fw-semibold rounded-3" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteReviewForm" method="POST" action="{{ route('admin.reviews.destroy', $review->id) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger fw-bold rounded-3">Delete Review</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
