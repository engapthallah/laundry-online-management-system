@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">
    <!-- Page Title -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
        <div>
            <h1 class="fw-bold mb-1"><i class="fa-solid fa-wallet text-primary me-2"></i>Payments Management</h1>
            <p class="text-muted mb-0">Monitor, complete, fail, and refund laundry orders payments.</p>
        </div>
        <div>
            <a href="{{ route('admin.payments.export', request()->all()) }}" class="btn btn-success rounded-3 shadow-sm px-3 py-2 fw-semibold">
                <i class="fa-solid fa-file-csv me-1"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Summary Statistics Cards -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Revenue -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-success-subtle p-3 text-success me-3">
                        <i class="fa-solid fa-dollar-sign fa-lg"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block small fw-medium">Total Revenue</span>
                        <h4 class="fw-bold mb-0 text-success">${{ number_format($stats['total_revenue'], 2) }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Today's Revenue -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-primary-subtle p-3 text-primary me-3">
                        <i class="fa-solid fa-calendar-day fa-lg"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block small fw-medium">Today's Revenue</span>
                        <h4 class="fw-bold mb-0 text-primary">${{ number_format($stats['today_revenue'], 2) }}</h4>
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
                        <span class="text-muted d-block small fw-medium">Pending Payments</span>
                        <h4 class="fw-bold mb-0 text-warning">{{ $stats['pending_count'] }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Failed Payments -->
        <div class="col-xl-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body p-3 d-flex align-items-center">
                    <div class="rounded-circle bg-danger-subtle p-3 text-danger me-3">
                        <i class="fa-solid fa-circle-xmark fa-lg"></i>
                    </div>
                    <div>
                        <span class="text-muted d-block small fw-medium">Failed Payments</span>
                        <h4 class="fw-bold mb-0 text-danger">{{ $stats['failed_count'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filter Bar -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('admin.payments.index') }}" method="GET" class="row g-2 align-items-end">
                <!-- Search term -->
                <div class="col-md-3">
                    <label for="search" class="form-label small fw-semibold text-secondary mb-1">Search Keywords</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-control border-start-0 select-sm rounded-3" placeholder="Order No / Name / Ref...">
                    </div>
                </div>

                <!-- Filter by method -->
                <div class="col-md-2">
                    <label for="method" class="form-label small fw-semibold text-secondary mb-1">Method</label>
                    <select name="method" id="method" class="form-select select-sm rounded-3">
                        <option value="all" {{ request('method') === 'all' || !request('method') ? 'selected' : '' }}>All Methods</option>
                        <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Cash</option>
                        <option value="zaad" {{ request('method') === 'zaad' ? 'selected' : '' }}>Zaad</option>
                        <option value="edahab" {{ request('method') === 'edahab' ? 'selected' : '' }}>Edahab</option>
                    </select>
                </div>

                <!-- Filter by status -->
                <div class="col-md-2">
                    <label for="status" class="form-label small fw-semibold text-secondary mb-1">Status</label>
                    <select name="status" id="status" class="form-select select-sm rounded-3">
                        <option value="all" {{ request('status') === 'all' || !request('status') ? 'selected' : '' }}>All Statuses</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                    </select>
                </div>

                <!-- Date Range From -->
                <div class="col-md-2">
                    <label for="date_from" class="form-label small fw-semibold text-secondary mb-1">From Date</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control select-sm rounded-3">
                </div>

                <!-- Date Range To -->
                <div class="col-md-2">
                    <label for="date_to" class="form-label small fw-semibold text-secondary mb-1">To Date</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control select-sm rounded-3">
                </div>

                <!-- Action buttons -->
                <div class="col-md-1 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100 rounded-3">
                        <i class="fa-solid fa-filter"></i>
                    </button>
                    @if (request()->hasAny(['search', 'method', 'status', 'date_from', 'date_to']))
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary rounded-3">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Payments List Table -->
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-body p-0">
            @if ($payments->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">#</th>
                                <th>Order Number</th>
                                <th>Customer Name</th>
                                <th>Method</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Transaction Reference</th>
                                <th>Paid At</th>
                                <th class="text-end pe-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($payments as $payment)
                                <tr>
                                    <td class="ps-4 text-muted fw-semibold">{{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}</td>
                                    <td>
                                        <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="fw-bold text-decoration-none">
                                            {{ $payment->order->order_number ?? '—' }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.users.show', $payment->order->customer_id ?? '') }}" class="text-dark fw-semibold text-decoration-none">
                                            {{ $payment->order->customer->name ?? '—' }}
                                        </a>
                                    </td>
                                    <td>
                                        @if ($payment->payment_method === 'cash')
                                            <span class="badge bg-secondary px-2 py-1"><i class="fa-solid fa-money-bill-wave me-1"></i>CASH</span>
                                        @elseif ($payment->payment_method === 'zaad')
                                            <span class="badge bg-success px-2 py-1"><i class="fa-solid fa-mobile-screen me-1"></i>ZAAD</span>
                                        @elseif ($payment->payment_method === 'edahab')
                                            <span class="badge bg-danger px-2 py-1"><i class="fa-solid fa-mobile-screen me-1"></i>EDAHAB</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">${{ number_format($payment->amount, 2) }}</td>
                                    <td>
                                        @if ($payment->status === 'pending')
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @elseif ($payment->status === 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif ($payment->status === 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @elseif ($payment->status === 'refunded')
                                            <span class="badge bg-info text-dark">Refunded</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($payment->transaction_reference)
                                            <code>{{ $payment->transaction_reference }}</code>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        {{ $payment->paid_at ? $payment->paid_at->format('M d, Y h:i A') : '—' }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('admin.payments.show', $payment->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2.5" title="View Detail">
                                                <i class="fa-solid fa-eye"></i> View
                                            </a>

                                            @if ($payment->status === 'pending')
                                                <!-- Complete Button Trigger Modal -->
                                                <button type="button" class="btn btn-sm btn-success rounded-pill px-2.5" data-bs-toggle="modal" data-bs-target="#completeModal-{{ $payment->id }}" title="Complete Payment">
                                                    <i class="fa-solid fa-circle-check"></i> Complete
                                                </button>
                                                <!-- Fail Button Trigger Modal -->
                                                <button type="button" class="btn btn-sm btn-danger rounded-pill px-2.5" data-bs-toggle="modal" data-bs-target="#failModal-{{ $payment->id }}" title="Fail Payment">
                                                    <i class="fa-solid fa-circle-xmark"></i> Fail
                                                </button>
                                            @endif

                                            @if ($payment->status === 'completed' && $payment->order && $payment->order->status !== 'delivered')
                                                <!-- Refund Button Trigger Modal -->
                                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-2.5" data-bs-toggle="modal" data-bs-target="#refundModal-{{ $payment->id }}" title="Process Refund">
                                                    <i class="fa-solid fa-rotate-left"></i> Refund
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                {{-- MODALS FOR ROW ACTIONS --}}
                                @if ($payment->status === 'pending')
                                    <!-- Mark as Completed Modal -->
                                    <div class="modal fade" id="completeModal-{{ $payment->id }}" tabindex="-1" aria-labelledby="completeModalLabel-{{ $payment->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-3">
                                                <form action="{{ route('admin.payments.complete', $payment->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-success text-white border-0 py-3">
                                                        <h5 class="modal-title fw-bold" id="completeModalLabel-{{ $payment->id }}">Mark Payment as Completed</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <p class="text-secondary mb-3">
                                                            Are you sure you want to mark this payment as completed? This will update the order payment status to <strong>'paid'</strong>.
                                                        </p>
                                                        <div class="mb-0">
                                                            <label for="transaction_reference-{{ $payment->id }}" class="form-label fw-semibold text-secondary small">Transaction Reference (Optional)</label>
                                                            <input type="text" name="transaction_reference" id="transaction_reference-{{ $payment->id }}" class="form-control rounded-3" placeholder="Enter Reference (Auto-generated if empty)">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 p-3 bg-light d-flex gap-2 justify-content-end">
                                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-success rounded-pill px-4">Confirm Completion</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mark as Failed Modal -->
                                    <div class="modal fade" id="failModal-{{ $payment->id }}" tabindex="-1" aria-labelledby="failModalLabel-{{ $payment->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-3">
                                                <form action="{{ route('admin.payments.fail', $payment->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-danger text-white border-0 py-3">
                                                        <h5 class="modal-title fw-bold" id="failModalLabel-{{ $payment->id }}">Mark Payment as Failed</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4 text-center">
                                                        <div class="mb-3">
                                                            <i class="fa-solid fa-circle-xmark fa-4x text-danger opacity-75"></i>
                                                        </div>
                                                        <p class="text-secondary fs-6 mb-0">
                                                            Mark this payment as failed? The customer will be notified and can retry payment.
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer border-0 p-3 bg-light d-flex gap-2 justify-content-end">
                                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger rounded-pill px-4">Confirm Fail</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if ($payment->status === 'completed' && $payment->order && $payment->order->status !== 'delivered')
                                    <!-- Process Refund Modal -->
                                    <div class="modal fade" id="refundModal-{{ $payment->id }}" tabindex="-1" aria-labelledby="refundModalLabel-{{ $payment->id }}" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-3">
                                                <form action="{{ route('admin.payments.refund', $payment->id) }}" method="POST">
                                                    @csrf
                                                    <div class="modal-header bg-danger text-white border-0 py-3">
                                                        <h5 class="modal-title fw-bold" id="refundModalLabel-{{ $payment->id }}">Process Payment Refund</h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body p-4">
                                                        <p class="text-secondary mb-3">
                                                            Process a refund for <strong>${{ number_format($payment->amount, 2) }}</strong>? This will mark the payment as refunded and notify the customer.
                                                        </p>
                                                        <div class="alert alert-warning border-0 rounded-3 small mb-3">
                                                            <i class="fa-solid fa-triangle-exclamation text-warning me-1"></i>
                                                            This operation requires administrative validation.
                                                        </div>
                                                        <div class="mb-0">
                                                            <label for="refund-validation-{{ $payment->id }}" class="form-label fw-semibold text-secondary small mb-1">Type <strong>CONFIRM</strong> to enable button</label>
                                                            <input type="text" id="refund-validation-{{ $payment->id }}" class="form-control rounded-3" placeholder="Type CONFIRM here" oninput="document.getElementById('refund-btn-{{ $payment->id }}').disabled = (this.value !== 'CONFIRM')">
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 p-3 bg-light d-flex gap-2 justify-content-end">
                                                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" id="refund-btn-{{ $payment->id }}" class="btn btn-danger rounded-pill px-4" disabled>Process Refund</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="card-footer bg-white border-0 py-3 px-4">
                    {{ $payments->links('pagination::bootstrap-5') }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5 my-3">
                    <div class="mb-3">
                        <i class="fa-solid fa-receipt fa-4x text-muted opacity-50"></i>
                    </div>
                    <h5 class="fw-bold text-secondary mb-1">No payments found.</h5>
                    <p class="text-muted small">Try refining your search terms or filters.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
