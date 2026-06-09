@extends('layouts.customer')

@section('content')
<div class="container-fluid px-0">
    <!-- Page Header -->
    <div class="row g-4 mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 fw-bold text-dark mb-1">Leave a Review — {{ $order->order_number }}</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('customer.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('customer.reviews.index') }}">My Reviews</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Leave a Review</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('customer.reviews.index') }}" class="btn btn-outline-secondary btn-sm fw-semibold rounded-3">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to Reviews
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- LEFT COLUMN — Review Form -->
        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-dark mb-1">How was your experience?</h4>
                    <p class="text-muted mb-4">Order #{{ $order->order_number }} — Delivered on {{ $order->updated_at->format('M d, Y') }}</p>

                    <form method="POST" action="{{ route('customer.reviews.store') }}" id="reviewForm" novalidate>
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">

                        <!-- Star Rating Selector -->
                        <div class="mb-4">
                            <label class="form-label d-block fw-semibold text-dark mb-2">Your Rating <span class="text-danger">*</span></label>
                            
                            <div class="star-rating d-inline-flex gap-2" id="starRating">
                                <i class="far fa-star star-btn" data-value="1"></i>
                                <i class="far fa-star star-btn" data-value="2"></i>
                                <i class="far fa-star star-btn" data-value="3"></i>
                                <i class="far fa-star star-btn" data-value="4"></i>
                                <i class="far fa-star star-btn" data-value="5"></i>
                            </div>
                            
                            <input type="hidden" name="rating" id="ratingInput" class="@error('rating') is-invalid @enderror" value="{{ old('rating') }}">
                            
                            <div id="ratingLabel" class="mt-2 text-muted small">
                                Click a star to rate
                            </div>

                            <div class="invalid-feedback mt-2" id="ratingValidationFeedback" style="display: none;">
                                Please select a rating before submitting.
                            </div>

                            @error('rating')
                                <div class="invalid-feedback d-block mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Comment Textarea -->
                        <div class="mb-4">
                            <label for="comment" class="form-label fw-semibold text-dark">Tell us about your experience (optional)</label>
                            <textarea name="comment" 
                                      id="comment" 
                                      rows="5" 
                                      class="form-control rounded-3 @error('comment') is-invalid @enderror" 
                                      placeholder="What did you like? What could be better?"
                                      maxlength="1000">{{ old('comment') }}</textarea>
                            
                            <div class="d-flex justify-content-between mt-1">
                                <div id="charCounter" class="ms-auto text-muted small">0 / 1000 characters</div>
                            </div>
                            @error('comment')
                                <div class="invalid-feedback d-block mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                            <button type="submit" class="btn btn-warning btn-lg fw-bold px-4 rounded-3 text-dark">
                                <i class="fa-solid fa-star me-2"></i>Submit Review
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- RIGHT COLUMN — Order Summary Card -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-4">Your Order</h5>
                    
                    <div class="mb-3">
                        <div class="text-muted small">Order Number</div>
                        <div class="fw-bold text-dark">#{{ $order->order_number }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-muted small">Order Date</div>
                        <div class="text-dark">{{ $order->created_at->format('M d, Y') }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="text-muted small mb-2">Services</div>
                        <ul class="list-unstyled mb-0">
                            @foreach($order->orderItems as $item)
                                <li class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-dark">{{ $item->service->name ?? 'N/A' }}</span>
                                    <span class="badge bg-secondary rounded-pill">x{{ $item->quantity }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    <div class="border-top pt-3 mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Total Price</span>
                            <span class="fw-bold text-primary fs-5">${{ number_format($order->total_price, 2) }}</span>
                        </div>
                    </div>
                    
                    <div>
                        <span class="badge bg-success w-100 py-2 rounded-3 text-uppercase tracking-wider">Delivered</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .star-btn { 
        font-size: 2.5rem; 
        cursor: pointer;
        color: #dee2e6; 
        transition: color 0.15s; 
    }
    .star-btn.hovered, .star-btn.selected { 
        color: #ffc107; 
    }
</style>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const starContainer = document.getElementById('starRating');
        const starBtns = starContainer.querySelectorAll('.star-btn');
        const ratingInput = document.getElementById('ratingInput');
        const ratingLabel = document.getElementById('ratingLabel');
        const form = document.getElementById('reviewForm');
        const validationFeedback = document.getElementById('ratingValidationFeedback');
        const commentTextarea = document.getElementById('comment');
        const charCounter = document.getElementById('charCounter');

        const labels = {
            1: 'Poor',
            2: 'Fair',
            3: 'Good',
            4: 'Very Good',
            5: 'Excellent'
        };

        function highlightStars(count, isHover = false) {
            starBtns.forEach((btn, idx) => {
                const val = idx + 1;
                if (val <= count) {
                    btn.className = 'fas fa-star star-btn';
                    if (isHover) {
                        btn.classList.add('hovered');
                        btn.classList.remove('selected');
                    } else {
                        btn.classList.add('selected');
                        btn.classList.remove('hovered');
                    }
                } else {
                    btn.className = 'far fa-star star-btn';
                    btn.classList.remove('hovered', 'selected');
                }
            });
        }

        starBtns.forEach(btn => {
            btn.addEventListener('mouseover', function() {
                const val = parseInt(this.getAttribute('data-value'));
                highlightStars(val, true);
                ratingLabel.textContent = labels[val];
            });

            btn.addEventListener('mouseout', function() {
                const currentRating = parseInt(ratingInput.value) || 0;
                highlightStars(currentRating, false);
                if (currentRating > 0) {
                    ratingLabel.textContent = labels[currentRating];
                } else {
                    ratingLabel.textContent = 'Click a star to rate';
                }
            });

            btn.addEventListener('click', function() {
                const val = parseInt(this.getAttribute('data-value'));
                ratingInput.value = val;
                highlightStars(val, false);
                ratingLabel.textContent = labels[val];
                ratingLabel.className = 'mt-2 text-warning fw-bold small';
                
                // Clear validation error if any
                if (validationFeedback) {
                    validationFeedback.style.display = 'none';
                }
                ratingInput.classList.remove('is-invalid');
            });
        });

        // Initialize state on validation fail / old value
        const oldRating = parseInt(ratingInput.value);
        if (oldRating) {
            highlightStars(oldRating, false);
            ratingLabel.textContent = labels[oldRating];
            ratingLabel.className = 'mt-2 text-warning fw-bold small';
        }

        form.addEventListener('submit', function(e) {
            if (!ratingInput.value) {
                e.preventDefault();
                ratingInput.classList.add('is-invalid');
                if (validationFeedback) {
                    validationFeedback.style.display = 'block';
                }
            }
        });

        if (commentTextarea && charCounter) {
            commentTextarea.addEventListener('input', function() {
                const len = this.value.length;
                charCounter.textContent = `${len} / 1000 characters`;
                if (len > 900) {
                    charCounter.classList.add('text-danger');
                    charCounter.classList.remove('text-muted');
                } else {
                    charCounter.classList.remove('text-danger');
                    charCounter.classList.add('text-muted');
                }
            });
            
            // Trigger on load for old content
            commentTextarea.dispatchEvent(new Event('input'));
        }
    });
</script>
@endsection
