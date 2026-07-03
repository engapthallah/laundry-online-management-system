@extends('layouts.admin')

@section('content')
<div class="container-fluid px-0">
    <!-- Header -->
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <div>
            <h1 class="h2 fw-bold text-dark mb-1">Customer Reviews</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.analytics.index') }}">Analytics</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Reviews</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Summary Analytics Bar -->
    <div class="row g-4 mb-4">
        <!-- Average Rating -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-semibold small mb-2">Average Rating</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ number_format($averageRating, 1) }} ★</h2>
                    </div>
                    <div class="rounded-4 bg-primary-subtle text-primary p-3">
                        <i class="fa-solid fa-star-half-stroke fs-3 text-primary"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Reviews -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-semibold small mb-2">Total Reviews</h6>
                        <h2 class="fw-bold mb-0 text-dark">{{ $totalReviews }}</h2>
                    </div>
                    <div class="rounded-4 bg-secondary-subtle text-secondary p-3">
                        <i class="fa-solid fa-comments fs-3 text-secondary"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 5-Star Reviews -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-semibold small mb-2">5-Star Reviews</h6>
                        <h2 class="fw-bold mb-0 text-success">{{ $fiveStarCount }}</h2>
                    </div>
                    <div class="rounded-4 bg-success-subtle text-success p-3">
                        <i class="fa-solid fa-face-smile fs-3 text-success"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- 1-Star Reviews -->
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-semibold small mb-2">1-Star Reviews</h6>
                        <h2 class="fw-bold mb-0 text-danger">{{ $oneStarCount }}</h2>
                    </div>
                    <div class="rounded-4 bg-danger-subtle text-danger p-3">
                        <i class="fa-solid fa-triangle-exclamation fs-3 text-danger"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('admin.reviews.index') }}" class="row g-3 align-items-end">
                <!-- Search Box -->
                <div class="col-12 col-md-3">
                    <label for="search" class="form-label small fw-bold">Search</label>
                    <input type="text" name="search" id="search" class="form-control rounded-3" placeholder="Order no or customer name" value="{{ request('search') }}">
                </div>
                
                <!-- Rating Selector -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="rating" class="form-label small fw-bold">Rating</label>
                    <select name="rating" id="rating" class="form-select rounded-3">
                        <option value="">All Ratings</option>
                        <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>5 Stars (★★★★★)</option>
                        <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>4 Stars (★★★★☆)</option>
                        <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>3 Stars (★★★☆☆)</option>
                        <option value="2" {{ request('rating') == '2' ? 'selected' : '' }}>2 Stars (★★☆☆☆)</option>
                        <option value="1" {{ request('rating') == '1' ? 'selected' : '' }}>1 Star (★☆☆☆☆)</option>
                    </select>
                </div>
                
                <!-- Date Filters -->
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="from_date" class="form-label small fw-bold">From Date</label>
                    <input type="date" name="from_date" id="from_date" class="form-control rounded-3" value="{{ request('from_date') }}">
                </div>
                
                <div class="col-12 col-sm-6 col-md-2">
                    <label for="to_date" class="form-label small fw-bold">To Date</label>
                    <input type="date" name="to_date" id="to_date" class="form-control rounded-3" value="{{ request('to_date') }}">
                </div>
                
                <!-- Filter Buttons -->
                <div class="col-12 col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary fw-semibold w-100 rounded-3">
                        <i class="fa-solid fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-light border fw-semibold w-100 rounded-3">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Reviews Table Card -->
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0 px-4" style="width: 5%;">#</th>
                            <th class="border-0" style="width: 20%;">Customer Name</th>
                            <th class="border-0" style="width: 15%;">Order No</th>
                            <th class="border-0" style="width: 15%;">Rating</th>
                            <th class="border-0" style="width: 25%;">Comment</th>
                            <th class="border-0" style="width: 10%;">Submitted At</th>
                            <th class="border-0 px-4 text-end" style="width: 10%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td class="px-4 text-muted small">{{ $loop->iteration + ($reviews->currentPage() - 1) * $reviews->perPage() }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $review->customer->name ?? 'N/A' }}</div>
                                    <div class="small text-muted">{{ $review->customer->email ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    @if($review->order)
                                        <a href="{{ route('admin.orders.show', $review->order->id) }}" class="fw-bold text-primary text-decoration-none">
                                            #{{ $review->order->order_number }}
                                        </a>
                                    @else
                                        <span class="text-muted">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-warning">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= $review->rating)
                                                <i class="fa-solid fa-star" style="color:#ffc107"></i>
                                            @else
                                                <i class="far fa-star text-muted"></i>
                                            @endif
                                        @endfor
                                    </div>
                                </td>
                                <td>
                                    @if($review->comment)
                                        <span class="text-dark small d-inline-block text-truncate" style="max-width: 250px;">
                                            {{ Str::limit($review->comment, 60) }}
                                        </span>
                                    @else
                                        <span class="text-muted small italic">No comment</span>
                                    @endif
                                </td>
                                <td class="text-muted small">
                                    {{ $review->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-4 text-end">
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('admin.reviews.show', $review->id) }}" class="btn btn-sm btn-outline-primary fw-semibold rounded-3">
                                            <i class="fa-solid fa-eye me-1"></i>View
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger fw-semibold rounded-3 delete-review-btn" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal" 
                                                data-review-id="{{ $review->id }}" 
                                                data-customer-name="{{ $review->customer->name ?? 'Customer' }}">
                                            <i class="fa-solid fa-trash me-1"></i>Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-box-open fs-2 mb-2 d-block text-secondary"></i>
                                    No reviews match the active filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($reviews->hasPages())
            <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
                {{ $reviews->links('pagination::bootstrap-5') }}
            </div>
        @endif
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
                <p id="deleteModalBodyText">Are you sure you want to delete this review? This action cannot be undone.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light border fw-semibold rounded-3" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteReviewForm" method="POST" action="" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger fw-bold rounded-3">Delete Review</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const deleteButtons = document.querySelectorAll('.delete-review-btn');
        const deleteForm = document.getElementById('deleteReviewForm');
        const deleteModalBodyText = document.getElementById('deleteModalBodyText');

        deleteButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const reviewId = this.getAttribute('data-review-id');
                const customerName = this.getAttribute('data-customer-name');
                const deleteUrl = `{{ url('/admin/reviews') }}/${reviewId}`;
                
                deleteForm.setAttribute('action', deleteUrl);
                deleteModalBodyText.textContent = `Are you sure you want to delete this review by ${customerName}? This action cannot be undone.`;
            });
        });
    });
</script>
@endsection
