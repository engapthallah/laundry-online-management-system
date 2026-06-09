@extends('layouts.customer')

@section('content')
<!-- Welcome Banner -->
<div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
    <div class="card-body p-4 p-md-5 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <span class="text-muted small fw-semibold text-uppercase tracking-wider">{{ $currentDate }}</span>
            <h1 class="fw-bold text-dark mt-1 mb-2">Welcome back, {{ $user->name }}!</h1>
            <p class="text-secondary mb-0 fs-5">{{ $motivationalMessage }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('customer.orders.create') }}" class="btn btn-primary fw-bold px-4 py-2.5 rounded-3">
                <i class="fa-solid fa-plus me-2"></i>Place New Order
            </a>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-5">
    <!-- Total Orders -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-2">Total Orders</h6>
                    <h2 class="fw-bold mb-0 text-dark">{{ $totalOrders }}</h2>
                </div>
                <div class="rounded-4 bg-primary-subtle text-primary p-3">
                    <i class="fa-solid fa-boxes-stacked fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Orders -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-2">Active Orders</h6>
                    <h2 class="fw-bold mb-0 text-warning">{{ $activeOrders }}</h2>
                </div>
                <div class="rounded-4 bg-warning-subtle text-warning p-3">
                    <i class="fa-solid fa-spinner fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Completed Orders -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-2">Completed Orders</h6>
                    <h2 class="fw-bold mb-0 text-success">{{ $completedOrders }}</h2>
                </div>
                <div class="rounded-4 bg-success-subtle text-success p-3">
                    <i class="fa-solid fa-circle-check fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Spent -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-2">Total Spent</h6>
                    <h2 class="fw-bold mb-0 text-dark">${{ number_format($totalSpent, 2) }}</h2>
                </div>
                <div class="rounded-4 bg-secondary-subtle text-secondary p-3">
                    <i class="fa-solid fa-credit-card fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders Table -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold text-dark mb-0">Recent Orders</h5>
                <a href="{{ route('customer.orders.index') }}" class="btn btn-sm btn-light fw-semibold text-primary px-3">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 px-4">Order No</th>
                                <th class="border-0">Services</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Total</th>
                                <th class="border-0">Date</th>
                                <th class="border-0 px-4 text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                                <tr>
                                    <td class="px-4 fw-medium text-primary">#{{ $order->order_number }}</td>
                                    <td>
                                        <span class="text-truncate d-inline-block text-dark fw-medium">
                                            {{ $order->orderItems->map(fn($item) => $item->service->name ?? 'N/A')->implode(', ') }}
                                        </span>
                                    </td>
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
                                    <td class="fw-bold text-dark">${{ number_format($order->total_price, 2) }}</td>
                                    <td class="text-muted small">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 text-end">
                                        <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                                            <i class="fa-solid fa-eye me-1"></i>Track
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="fa-solid fa-box-open fs-2 mb-2 d-block text-secondary"></i>
                                        You haven't placed any orders yet.
                                        <a href="{{ route('customer.orders.create') }}" class="btn btn-sm btn-primary fw-semibold mt-3 d-block mx-auto w-25">Place First Order</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Panel -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Quick Shortcuts</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="d-grid gap-3">
                    <a href="{{ route('customer.orders.create') }}" class="btn btn-light border p-3 rounded-4 d-flex align-items-center text-start text-decoration-none">
                        <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3">
                            <i class="fa-solid fa-plus fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Place New Order</h6>
                            <small class="text-muted">Schedule a laundry collection</small>
                        </div>
                    </a>

                    <a href="{{ route('customer.orders.index') }}" class="btn btn-light border p-3 rounded-4 d-flex align-items-center text-start text-decoration-none">
                        <div class="rounded-circle bg-warning-subtle text-warning p-3 me-3">
                            <i class="fa-solid fa-route fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Track My Orders</h6>
                            <small class="text-muted">View order statuses & receipts</small>
                        </div>
                    </a>

                    <a href="{{ route('customer.support.create') }}" class="btn btn-light border p-3 rounded-4 d-flex align-items-center text-start text-decoration-none">
                        <div class="rounded-circle bg-danger-subtle text-danger p-3 me-3">
                            <i class="fa-solid fa-headset fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">Contact Support</h6>
                            <small class="text-muted">Open a helpdesk ticket</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
