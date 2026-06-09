@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-dark">Delivery Profile - #{{ $delivery->id }}</h1>
    <a href="{{ route('admin.delivery.index') }}" class="btn btn-outline-secondary fw-semibold">
        <i class="fa-solid fa-arrow-left me-2"></i>Back to Assignments
    </a>
</div>

<div class="row g-4">
    <!-- Left Column: Details -->
    <div class="col-12 col-md-8">
        <!-- Order Context Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Order Information</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <h6 class="fw-bold text-muted small text-uppercase mb-1">Order Number</h6>
                        <p class="mb-0 fw-semibold text-primary">#{{ $delivery->order->order_number ?? 'N/A' }}</p>
                    </div>
                    <div class="col-12 col-sm-6">
                        <h6 class="fw-bold text-muted small text-uppercase mb-1">Total Value</h6>
                        <p class="mb-0 fw-semibold text-dark">${{ number_format($delivery->order->total_price ?? 0.0, 2) }}</p>
                    </div>
                    <div class="col-12 col-sm-6">
                        <h6 class="fw-bold text-muted small text-uppercase mb-1">Pickup Address</h6>
                        <p class="mb-0 text-dark">{{ $delivery->order->pickup_address ?? 'N/A' }}</p>
                    </div>
                    <div class="col-12 col-sm-6">
                        <h6 class="fw-bold text-muted small text-uppercase mb-1">Delivery Address</h6>
                        <p class="mb-0 text-dark">{{ $delivery->order->delivery_address ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Log -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Delivery Timeline</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <ul class="list-group list-group-flush border-start border-primary border-2 ms-3 px-0">
                    <li class="list-group-item border-0 position-relative py-3">
                        <span class="position-absolute start-0 translate-middle bg-primary rounded-circle p-1.5" style="left: -1px !important;"></span>
                        <div class="fw-semibold text-dark">Agent Assigned</div>
                        <div class="small text-muted">
                            {{ $delivery->assigned_at ? $delivery->assigned_at->format('M d, Y \a\t g:i A') : 'N/A' }}
                        </div>
                    </li>
                    <li class="list-group-item border-0 position-relative py-3">
                        <span class="position-absolute start-0 translate-middle {{ $delivery->picked_up_at ? 'bg-primary' : 'bg-secondary' }} rounded-circle p-1.5" style="left: -1px !important;"></span>
                        <div class="fw-semibold {{ $delivery->picked_up_at ? 'text-dark' : 'text-muted' }}">Picked Up</div>
                        <div class="small text-muted">
                            {{ $delivery->picked_up_at ? $delivery->picked_up_at->format('M d, Y \a\t g:i A') : 'Pending' }}
                        </div>
                    </li>
                    <li class="list-group-item border-0 position-relative py-3">
                        <span class="position-absolute start-0 translate-middle {{ $delivery->delivered_at ? 'bg-success' : 'bg-secondary' }} rounded-circle p-1.5" style="left: -1px !important;"></span>
                        <div class="fw-semibold {{ $delivery->delivered_at ? 'text-success' : 'text-muted' }}">Delivered</div>
                        <div class="small text-muted">
                            {{ $delivery->delivered_at ? $delivery->delivered_at->format('M d, Y \a\t g:i A') : 'Pending' }}
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Column: Control & Agents -->
    <div class="col-12 col-md-4">
        <!-- Update Delivery Status -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3">Update Dispatch Status</h5>
                <form method="POST" action="{{ route('admin.delivery.updateStatus', $delivery->id) }}">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-3">
                        <select name="status" id="status" class="form-select bg-light">
                            <option value="assigned" {{ $delivery->status === 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="picked_up" {{ $delivery->status === 'picked_up' ? 'selected' : '' }}>Picked Up</option>
                            <option value="on_the_way" {{ $delivery->status === 'on_the_way' ? 'selected' : '' }}>On The Way</option>
                            <option value="delivered" {{ $delivery->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Update Status</button>
                </form>
            </div>
        </div>

        <!-- Delivery Agent details -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Delivery Agent</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-warning-subtle text-warning p-3">
                        <i class="fa-solid fa-truck fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">{{ $delivery->deliveryAgent->name ?? 'N/A' }}</h6>
                        <span class="badge bg-warning text-dark text-uppercase fs-9">Courier</span>
                    </div>
                </div>
                <div class="small text-muted mb-2">
                    <i class="fa-regular fa-envelope me-2"></i>{{ $delivery->deliveryAgent->email ?? 'N/A' }}
                </div>
                <div class="small text-muted">
                    <i class="fa-solid fa-phone me-2"></i>{{ $delivery->deliveryAgent->phone ?? 'N/A' }}
                </div>
            </div>
        </div>

        <!-- Customer Card -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Customer Details</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-success-subtle text-success p-3">
                        <i class="fa-solid fa-user fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">{{ $delivery->order->customer->name ?? 'N/A' }}</h6>
                        <span class="badge bg-success-subtle text-success text-uppercase fs-9">Customer</span>
                    </div>
                </div>
                <div class="small text-muted mb-2">
                    <i class="fa-regular fa-envelope me-2"></i>{{ $delivery->order->customer->email ?? 'N/A' }}
                </div>
                <div class="small text-muted">
                    <i class="fa-solid fa-phone me-2"></i>{{ $delivery->order->customer->phone ?? 'N/A' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
