@extends('layouts.delivery')

@section('content')
<style>
    /* Custom hover transition for table rows */
    .table-hover tbody tr {
        transition: background-color 0.15s ease;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(211, 84, 0, 0.04) !important;
    }
    .phase-header {
        border-left: 4px solid #fd7e14;
        padding-left: 10px;
    }
</style>

<div class="row mb-4">
    <div class="col-12">
        <h3 class="fw-bold text-dark mb-1">My Transit Jobs</h3>
        <p class="text-secondary mb-0">Manage both pickup (from customer) and return (to customer) tasks assigned to you.</p>
    </div>
</div>

<!-- Filters Panel Card -->
<div class="card border-0 shadow-sm rounded-3 mb-4 bg-white p-4">
    <form method="GET" action="{{ route('delivery.orders.index') }}" class="row g-3">
        <!-- Order Number Search -->
        <div class="col-12 col-md-9">
            <label for="search" class="form-label fw-semibold text-secondary small">Search Order No.</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" class="form-control bg-light border-start-0" id="search" name="search" placeholder="e.g. LOMS-1004" value="{{ request('search') }}">
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="col-12 col-md-3 d-flex align-items-end gap-2">
            <button type="submit" class="btn btn-primary w-100 fw-semibold text-white">
                Filter
            </button>
            @if(request()->filled('search'))
                <a href="{{ route('delivery.orders.index') }}" class="btn btn-outline-secondary w-100 fw-semibold" title="Clear Filters">
                    Clear
                </a>
            @endif
        </div>
    </form>
</div>

@php
    $pickupOrders = $orders->filter(fn($o) => in_array($o->status, ['pending_pickup', 'picked_up_from_customer']));
    $returnOrders = $orders->filter(fn($o) => in_array($o->status, ['ready_for_delivery', 'picked_up_from_laundry', 'on_the_way']));
@endphp

<!-- Pickup Phase Section -->
<div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
        <h5 class="fw-bold text-dark mb-0 phase-header">Pickup Phase <span class="badge bg-secondary ms-2">{{ $pickupOrders->count() }}</span></h5>
        <p class="text-muted small mb-0 mt-1">Laundry to collect from customer and bring to the laundry shop.</p>
    </div>
    <div class="card-body p-0">
        @if($pickupOrders->isEmpty())
            <div class="text-center py-4 text-muted">
                <i class="fa-solid fa-box-open fs-3 mb-2 d-block text-secondary"></i>
                No pickup orders assigned at the moment.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3 text-secondary small" style="width: 150px;">Order No</th>
                            <th class="py-3 text-secondary small">Customer</th>
                            <th class="py-3 text-secondary small">Pickup Address</th>
                            <th class="py-3 text-secondary small">Shop Address</th>
                            <th class="py-3 text-secondary small text-center" style="width: 150px;">Status</th>
                            <th class="py-3 text-center text-secondary small" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pickupOrders as $order)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-dark">#{{ $order->order_number }}</span>
                                </td>
                                <td class="fw-semibold text-secondary">
                                    {{ $order->customer->name ?? 'N/A' }}
                                </td>
                                <td class="text-secondary small">
                                    {{ Str::limit($order->pickup_address, 45) }}
                                </td>
                                <td class="text-secondary small">
                                    {{ Str::limit($order->delivery_address, 45) }}
                                </td>
                                <td class="text-center">
                                    @if($order->status === 'pending_pickup')
                                        <span class="badge bg-secondary">Pending Pickup</span>
                                    @else
                                        <span class="badge bg-primary">Picked Up</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('delivery.orders.show', $order) }}" class="btn btn-sm btn-primary text-white fw-semibold px-3">
                                        Manage
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Return Phase Section -->
<div class="card border-0 shadow-sm rounded-3 bg-white mb-4">
    <div class="card-header bg-white border-0 pt-4 px-4 pb-2">
        <h5 class="fw-bold text-dark mb-0 phase-header">Return Delivery Phase <span class="badge bg-secondary ms-2">{{ $returnOrders->count() }}</span></h5>
        <p class="text-muted small mb-0 mt-1">Processed laundry to deliver back to the customer.</p>
    </div>
    <div class="card-body p-0">
        @if($returnOrders->isEmpty())
            <div class="text-center py-4 text-muted">
                <i class="fa-solid fa-truck-ramp-box fs-3 mb-2 d-block text-secondary"></i>
                No return delivery orders assigned at the moment.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3 text-secondary small" style="width: 150px;">Order No</th>
                            <th class="py-3 text-secondary small">Customer</th>
                            <th class="py-3 text-secondary small">Delivery Address</th>
                            <th class="py-3 text-secondary small text-center" style="width: 140px;">Payment Method</th>
                            <th class="py-3 text-secondary small text-center" style="width: 150px;">Status</th>
                            <th class="py-3 text-center text-secondary small" style="width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($returnOrders as $order)
                            <tr>
                                <td class="ps-4">
                                    <span class="fw-bold text-dark">#{{ $order->order_number }}</span>
                                </td>
                                <td class="fw-semibold text-secondary">
                                    {{ $order->customer->name ?? 'N/A' }}
                                </td>
                                <td class="text-secondary small">
                                    {{ Str::limit($order->delivery_address, 55) }}
                                </td>
                                <td class="text-center">
                                    @if($order->payment_method === 'cash')
                                        <span class="badge bg-danger">Collect Cash</span>
                                    @else
                                        <span class="badge bg-success">Paid</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($order->status === 'ready_for_delivery')
                                        <span class="badge bg-info text-dark">Ready for Delivery</span>
                                    @elseif($order->status === 'picked_up_from_laundry')
                                        <span class="badge bg-primary">Picked Up from Laundry</span>
                                    @else
                                        <span class="badge bg-dark text-white">On the Way</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('delivery.orders.show', $order) }}" class="btn btn-sm btn-primary text-white fw-semibold px-3">
                                        Manage
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<!-- Pagination Links -->
@if($orders->hasPages())
    <div class="card border-0 shadow-sm rounded-3 bg-white p-4 d-flex justify-content-between align-items-center">
        <div class="text-muted small">
            Showing {{ $orders->firstItem() }} to {{ $orders->lastItem() }} of {{ $orders->total() }} assignments
        </div>
        <div>
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endif

@endsection
