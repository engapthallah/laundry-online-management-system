@extends('layouts.customer')

@section('content')
<div class="container py-4">
    <!-- Page Header & Stats Chip -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h1 class="fw-bold mb-1"><i class="fa-solid fa-receipt text-primary me-2"></i>My Payments</h1>
            <p class="text-muted mb-0">Track and manage your mobile and cash laundry payments.</p>
        </div>
        <div>
            <span class="badge bg-primary-subtle text-primary fs-5 px-4 py-2 rounded-pill shadow-sm">
                Total Spent: <strong class="text-primary">${{ number_format($totalSpent, 2) }}</strong>
            </span>
        </div>
    </div>

    <!-- Summary Stat Cards -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Payments -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-secondary-subtle p-3 text-secondary me-3">
                        <i class="fa-solid fa-credit-card fa-lg"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block small fw-medium">Total Payments</span>
                        <h4 class="fw-bold mb-0 text-dark">{{ $stats['total_count'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Completed Payments -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-success-subtle p-3 text-success me-3">
                        <i class="fa-solid fa-circle-check fa-lg"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block small fw-medium">Completed</span>
                        <h4 class="fw-bold mb-0 text-success">
                            {{ $stats['completed_count'] }} <span class="fs-6 fw-normal">(${{ number_format($stats['completed_amount'], 2) }})</span>
                        </h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Pending Payments -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-warning-subtle p-3 text-warning me-3">
                        <i class="fa-solid fa-clock fa-lg"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block small fw-medium">Pending Collection</span>
                        <h4 class="fw-bold mb-0 text-warning">{{ $stats['pending_count'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Failed/Refunded -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-danger-subtle p-3 text-danger me-3">
                        <i class="fa-solid fa-circle-exclamation fa-lg"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block small fw-medium">Failed/Refunded</span>
                        <h4 class="fw-bold mb-0 text-danger">{{ $stats['failed_refunded_count'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('customer.payments.index') }}" method="GET" class="row g-2 align-items-center">
                <!-- Filter by method -->
                <div class="col-md-4">
                    <label for="method" class="form-label small fw-semibold text-secondary mb-1">Payment Method</label>
                    <select name="method" id="method" class="form-select select-sm rounded-3">
                        <option value="all" {{ request('method') === 'all' || !request('method') ? 'selected' : '' }}>All Methods</option>
                        <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="zaad" {{ request('method') === 'zaad' ? 'selected' : '' }}>Zaad</option>
                        <option value="edahab" {{ request('method') === 'edahab' ? 'selected' : '' }}>Edahab</option>
                    </select>
                </div>

                <!-- Filter by status -->
                <div class="col-md-4">
                    <label for="status" class="form-label small fw-semibold text-secondary mb-1">Payment Status</label>
                    <select name="status" id="status" class="form-select select-sm rounded-3">
                        <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <!-- Action buttons -->
                <div class="col-md-4 d-flex gap-2 align-self-end mt-md-0 mt-3 pt-md-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3">
                        <i class="fa-solid fa-filter me-1"></i> Apply Filters
                    </button>
                    @if (request()->hasAny(['method', 'status']))
                        <a href="{{ route('customer.payments.index') }}" class="btn btn-outline-secondary w-100 rounded-3">
                            Clear Filters
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table Card -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">
            @if ($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Order No.</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Transaction Ref</th>
                                <th>Date</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="ps-4 text-muted fw-semibold">{{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('customer.orders.show', $payment->order_id) }}" class="fw-bold text-decoration-none">
                                            {{ $payment->order->order_number }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($payment->payment_method === 'cash')
                                            <span class="badge bg-secondary px-2.5 py-1.5"><i class="fa-solid fa-money-bill-wave me-1"></i>Cash</span>
                                        @elseif ($payment->payment_method === 'zaad')
                                            <span class="badge bg-success px-2.5 py-1.5"><i class="fa-solid fa-mobile-screen me-1"></i>Zaad</span>
                                        @elseif ($payment->payment_method === 'edahab')
                                            <span class="badge bg-danger px-2.5 py-1.5"><i class="fa-solid fa-mobile-screen me-1"></i>Edahab</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold text-dark">${{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        @if ($payment->status === 'pending')
                                            <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i>Pending</span>
                                        @elseif ($payment->status === 'completed')
                                            <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Completed</span>
                                        @elseif ($payment->status === 'failed')
                                            <span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i>Failed</span>
                                        @elseif ($payment->status === 'refunded')
                                            <span class="badge bg-info text-dark"><i class="fa-solid fa-rotate-left me-1"></i>Refunded</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment->transaction_reference)
                                            <code>{{ Str::limit($payment->transaction_reference, 25) }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        {{ $payment->created_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-2">
                                            @if ($payment->status === 'pending' && in_array($payment->payment_method, ['zaad', 'edahab']))
                                                <a href="{{ route('customer.payments.show', $payment->id) }}" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold text-dark">
                                                    Complete Payment
                                                </a>
                                            @else
                                                <a href="{{ route('customer.payments.show', $payment->id) }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                                    View Details
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Links -->
                <div class="card-footer bg-white border-0 py-3 px-4">
                    {{ $payments->links('pagination::bootstrap-5') }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5 my-3">
                    <div class="mb-3">
                        <i class="fa-solid fa-receipt fa-3x text-muted opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-secondary mb-1">No payment records found.</h5>
                    <p class="text-muted small mb-0">Try clearing filters or checking your order list.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
