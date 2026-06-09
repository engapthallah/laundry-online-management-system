@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-dark">Order Management</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <span class="badge bg-primary px-3 py-2 text-uppercase fs-7 fw-semibold">Total: {{ $orders->total() }} Orders</span>
    </div>
</div>

<!-- Search & Advanced Filter Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3">
            <!-- Search field -->
            <div class="col-12 col-md-4">
                <label for="search" class="form-label fw-semibold text-muted small">Search Order No / Customer</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" id="search" class="form-control bg-light border-start-0" placeholder="Type query..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Status filter -->
            <div class="col-12 col-sm-6 col-md-2">
                <label for="status" class="form-label fw-semibold text-muted small">Order Status</label>
                <select name="status" id="status" class="form-select bg-light">
                    <option value="">All Statuses</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="washing" {{ request('status') === 'washing' ? 'selected' : '' }}>Washing</option>
                    <option value="drying" {{ request('status') === 'drying' ? 'selected' : '' }}>Drying</option>
                    <option value="ironing" {{ request('status') === 'ironing' ? 'selected' : '' }}>Ironing</option>
                    <option value="folding" {{ request('status') === 'folding' ? 'selected' : '' }}>Folding</option>
                    <option value="ready_for_delivery" {{ request('status') === 'ready_for_delivery' ? 'selected' : '' }}>Ready for Delivery</option>
                    <option value="out_for_delivery" {{ request('status') === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                    <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>

            <!-- Payment status filter -->
            <div class="col-12 col-sm-6 col-md-2">
                <label for="payment_status" class="form-label fw-semibold text-muted small">Payment Status</label>
                <select name="payment_status" id="payment_status" class="form-select bg-light">
                    <option value="">All Payments</option>
                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                </select>
            </div>

            <!-- Date From filter -->
            <div class="col-12 col-sm-6 col-md-2">
                <label for="date_from" class="form-label fw-semibold text-muted small">Date From</label>
                <input type="date" name="date_from" id="date_from" class="form-control bg-light" value="{{ request('date_from') }}">
            </div>

            <!-- Date To filter -->
            <div class="col-12 col-sm-6 col-md-2">
                <label for="date_to" class="form-label fw-semibold text-muted small">Date To</label>
                <input type="date" name="date_to" id="date_to" class="form-control bg-light" value="{{ request('date_to') }}">
            </div>

            <!-- Filter Buttons -->
            <div class="col-12 d-flex justify-content-end gap-2 mt-3">
                <button type="submit" class="btn btn-primary px-4 fw-bold">Apply Filters</button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary px-4 fw-bold">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Orders Listing Card -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 px-4">#</th>
                        <th class="border-0">Order No</th>
                        <th class="border-0">Customer</th>
                        <th class="border-0">Items Count</th>
                        <th class="border-0">Total</th>
                        <th class="border-0">Order Status</th>
                        <th class="border-0">Payment</th>
                        <th class="border-0">Pickup Time</th>
                        <th class="border-0 px-4 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-4 text-muted">{{ $order->id }}</td>
                            <td class="fw-semibold text-primary">#{{ $order->order_number }}</td>
                            <td>
                                <div class="fw-medium text-dark">{{ $order->customer->name ?? 'N/A' }}</div>
                                <div class="small text-muted">{{ $order->customer->phone ?? 'No phone' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary px-2">
                                    {{ $order->orderItems->count() }} items
                                </span>
                            </td>
                            <td class="fw-bold text-dark">${{ number_format($order->total_price, 2) }}</td>
                            <td>
                                @switch($order->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark text-capitalize px-2.5 py-1.5">{{ $order->status }}</span>
                                        @break
                                    @case('confirmed')
                                        <span class="badge bg-primary text-capitalize px-2.5 py-1.5">{{ $order->status }}</span>
                                        @break
                                    @case('washing')
                                    @case('drying')
                                    @case('ironing')
                                    @case('folding')
                                        <span class="badge bg-info text-dark text-capitalize px-2.5 py-1.5">{{ $order->status }}</span>
                                        @break
                                    @case('ready_for_delivery')
                                        <span class="badge bg-teal text-white text-capitalize px-2.5 py-1.5">Ready for Delivery</span>
                                        @break
                                    @case('out_for_delivery')
                                        <span class="badge bg-orange text-white text-capitalize px-2.5 py-1.5">Out for Delivery</span>
                                        @break
                                    @case('delivered')
                                        <span class="badge bg-success text-capitalize px-2.5 py-1.5">{{ $order->status }}</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger text-capitalize px-2.5 py-1.5">{{ $order->status }}</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @switch($order->payment_status)
                                    @case('pending')
                                        <span class="badge bg-warning-subtle text-warning text-uppercase px-2 py-1">Pending</span>
                                        @break
                                    @case('paid')
                                        <span class="badge bg-success-subtle text-success text-uppercase px-2 py-1">Paid</span>
                                        @break
                                    @case('failed')
                                        <span class="badge bg-danger-subtle text-danger text-uppercase px-2 py-1">Failed</span>
                                        @break
                                    @case('refunded')
                                        <span class="badge bg-info-subtle text-info text-uppercase px-2 py-1">Refunded</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="text-muted small">
                                {{ $order->pickup_time ? $order->pickup_time->format('M d, g:i A') : 'N/A' }}
                            </td>
                            <td class="px-4 text-end">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                    <i class="fa-solid fa-eye me-1"></i>Manage
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-box-open fs-2 mb-2 d-block text-secondary"></i>
                                No orders found matching filter criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
