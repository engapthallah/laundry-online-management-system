@extends('layouts.customer')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-dark">My Orders</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <span class="badge bg-primary px-3 py-2 text-uppercase fs-7 fw-semibold">History Log</span>
    </div>
</div>

<!-- Search & Status Filter Card -->
<div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('customer.orders.index') }}" class="row g-3">
            <!-- Search field -->
            <div class="col-12 col-md-6">
                <label for="search" class="form-label fw-semibold text-muted small">Search Order Number</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" id="search" class="form-control bg-light border-start-0" placeholder="Type LOMS-..." value="{{ request('search') }}">
                </div>
            </div>

            <!-- Status filter -->
            <div class="col-12 col-sm-8 col-md-4">
                <label for="status" class="form-label fw-semibold text-muted small">Filter by Order Status</label>
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

            <!-- Filter Buttons -->
            <div class="col-12 col-sm-4 col-md-2 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold">Filter</button>
                <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-secondary w-100 fw-bold">Clear</a>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table Card -->
<div class="card border-0 shadow-sm rounded-4 bg-white">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0 px-4">Order No</th>
                        <th class="border-0">Services</th>
                        <th class="border-0">Total Price</th>
                        <th class="border-0">Order Status</th>
                        <th class="border-0">Payment</th>
                        <th class="border-0">Pickup Date</th>
                        <th class="border-0">Placed At</th>
                        <th class="border-0 px-4 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="px-4 fw-medium text-primary">#{{ $order->order_number }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary px-2">
                                    {{ $order->orderItems->count() }} services
                                </span>
                            </td>
                            <td class="fw-bold text-dark">${{ number_format($order->total_price, 2) }}</td>
                            <td>
                                @switch($order->status)
                                    @case('pending')
                                        <span class="badge bg-warning text-dark text-capitalize">{{ $order->status }}</span>
                                        @break
                                    @case('confirmed')
                                        <span class="badge bg-primary text-capitalize">{{ $order->status }}</span>
                                        @break
                                    @case('washing')
                                    @case('drying')
                                    @case('ironing')
                                    @case('folding')
                                        <span class="badge bg-info text-dark text-capitalize">{{ $order->status }}</span>
                                        @break
                                    @case('ready_for_delivery')
                                        <span class="badge bg-teal text-white text-capitalize">Ready</span>
                                        @break
                                    @case('out_for_delivery')
                                        <span class="badge bg-orange text-white text-capitalize">Out</span>
                                        @break
                                    @case('delivered')
                                        <span class="badge bg-success text-capitalize">{{ $order->status }}</span>
                                        @break
                                    @case('cancelled')
                                        <span class="badge bg-danger text-capitalize">{{ $order->status }}</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                @switch($order->payment_status)
                                    @case('pending')
                                        <span class="badge bg-warning-subtle text-warning text-uppercase">Pending</span>
                                        @break
                                    @case('paid')
                                        <span class="badge bg-success-subtle text-success text-uppercase">Paid</span>
                                        @break
                                    @case('failed')
                                        <span class="badge bg-danger-subtle text-danger text-uppercase">Failed</span>
                                        @break
                                    @case('refunded')
                                        <span class="badge bg-info-subtle text-info text-uppercase">Refunded</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="text-muted small">
                                {{ $order->pickup_time ? $order->pickup_time->format('M d, Y') : 'N/A' }}
                            </td>
                            <td class="text-muted small">
                                {{ $order->created_at->format('M d, Y h:i A') }}
                            </td>
                            <td class="px-4 text-end">
                                <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                    <i class="fa-solid fa-eye me-1"></i>Track Order
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="fa-solid fa-box-open fs-2 mb-2 d-block text-secondary"></i>
                                You have not placed any orders yet.
                                <a href="{{ route('customer.orders.create') }}" class="btn btn-primary fw-semibold mt-3 d-block mx-auto w-25">Place First Order</a>
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
