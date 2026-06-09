@extends('layouts.admin')

@section('content')
<div class="container-fluid px-3">
    <!-- Navigation back & Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <a href="{{ route('admin.payments.index') }}" class="btn btn-outline-secondary rounded-pill px-3 btn-sm">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to Payments
        </a>
        <a href="{{ route('admin.payments.receipt', $payment->id) }}" target="_blank" class="btn btn-outline-success rounded-pill px-3 btn-sm">
            <i class="fa-solid fa-print me-1"></i> View Receipt
        </a>
    </div>

    <!-- Section 1: Payment Header Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div>
                <span class="text-muted small">Payment ID: #{{ $payment->id }}</span>
                <h3 class="fw-bold text-dark mb-1">Payment for Order {{ $payment->order->order_number ?? '—' }}</h3>
                <span class="text-muted small">Created on {{ $payment->created_at->format('M d, Y \a\t h:i A') }}</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <!-- Large Status Badge -->
                @if ($payment->status === 'pending')
                    <span class="badge bg-warning text-dark px-3 py-2 fs-6 rounded-pill">Pending</span>
                @elseif ($payment->status === 'completed')
                    <span class="badge bg-success px-3 py-2 fs-6 rounded-pill">Completed</span>
                @elseif ($payment->status === 'failed')
                    <span class="badge bg-danger px-3 py-2 fs-6 rounded-pill">Failed</span>
                @elseif ($payment->status === 'refunded')
                    <span class="badge bg-info text-dark px-3 py-2 fs-6 rounded-pill">Refunded</span>
                @endif

                <!-- Quick Actions in Header (Conditional) -->
                @if ($payment->status === 'pending')
                    <button type="button" class="btn btn-success rounded-pill btn-sm px-3" data-bs-toggle="modal" data-bs-target="#headerCompleteModal">
                        Complete
                    </button>
                    <button type="button" class="btn btn-danger rounded-pill btn-sm px-3" data-bs-toggle="modal" data-bs-target="#headerFailModal">
                        Fail
                    </button>
                @endif
                @if ($payment->status === 'completed' && $payment->order && $payment->order->status !== 'delivered')
                    <button type="button" class="btn btn-outline-danger rounded-pill btn-sm px-3" data-bs-toggle="modal" data-bs-target="#headerRefundModal">
                        Refund
                    </button>
                @endif
            </div>
        </div>
    </div>

    <!-- Section 2: Two Column Layout (Payment Info & Related Order Info) -->
    <div class="row g-4 mb-4">
        <!-- Left Column: Payment Info -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-credit-card text-primary me-2"></i>Payment Details</h5>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div class="mb-3">
                        <span class="text-muted d-block small mb-1">Amount</span>
                        <h2 class="fw-bold text-dark mb-0">${{ number_format($payment->amount, 2) }}</h2>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6">
                            <span class="text-muted d-block small mb-1">Payment Method</span>
                            <div class="d-flex align-items-center">
                                @if ($payment->payment_method === 'cash')
                                    <span class="badge bg-secondary px-3 py-1.5 fs-6"><i class="fa-solid fa-money-bill-wave me-1"></i>Cash</span>
                                @elseif ($payment->payment_method === 'zaad')
                                    <span class="badge bg-success px-3 py-1.5 fs-6"><i class="fa-solid fa-mobile-screen me-1"></i>Zaad</span>
                                @elseif ($payment->payment_method === 'edahab')
                                    <span class="badge bg-danger px-3 py-1.5 fs-6"><i class="fa-solid fa-mobile-screen me-1"></i>Edahab</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <span class="text-muted d-block small mb-1">Paid At</span>
                            <span class="fw-semibold text-dark fs-6">
                                {{ $payment->paid_at ? $payment->paid_at->format('M d, Y h:i A') : '—' }}
                            </span>
                        </div>

                        <div class="col-12">
                            <span class="text-muted d-block small mb-1">Transaction Reference</span>
                            @if ($payment->transaction_reference)
                                <div class="d-flex align-items-center gap-2">
                                    <code class="fs-5 text-dark fw-bold border bg-light px-3 py-1.5 rounded-3">{{ $payment->transaction_reference }}</code>
                                    <button class="btn btn-sm btn-outline-secondary rounded-3" id="copy-ref-btn" onclick="copyRefToClipboard('{{ $payment->transaction_reference }}')">
                                        <i class="fa-regular fa-copy"></i>
                                    </button>
                                </div>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </div>

                        <div class="col-12">
                            <span class="text-muted d-block small mb-1">Status Notes</span>
                            @if ($payment->status === 'pending')
                                <p class="text-secondary small mb-0">This payment record was generated when the customer checked out. The customer has not finalized mobile money confirmation or this is cash pending delivery collection.</p>
                            @elseif ($payment->status === 'completed')
                                <p class="text-success small mb-0">The payment has been fully confirmed and funds are marked as received. The customer has full access to download their printable invoice receipt.</p>
                            @elseif ($payment->status === 'failed')
                                <p class="text-danger small mb-0">This payment transaction failed validation, was marked invalid by support, or is set to fail. The customer will be able to retry this payment.</p>
                            @elseif ($payment->status === 'refunded')
                                <p class="text-info text-dark small mb-0">This payment has been refunded by the administration team. The associated order was also marked as refunded.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Related Order Info -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm rounded-3 h-100">
                <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-cart-shopping text-primary me-2"></i>Order Information</h5>
                </div>
                <div class="card-body px-4 pb-4 pt-3">
                    <div class="table-responsive">
                        <table class="table table-sm table-borderless align-middle mb-0">
                            <tbody>
                                <tr>
                                    <td class="text-muted ps-0 py-2">Order Number:</td>
                                    <td class="py-2">
                                        <a href="{{ route('admin.orders.show', $payment->order_id) }}" class="fw-bold text-decoration-none">
                                            {{ $payment->order->order_number ?? '—' }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0 py-2">Customer Name:</td>
                                    <td class="py-2">
                                        <a href="{{ route('admin.users.show', $payment->order->customer_id ?? '') }}" class="fw-semibold text-dark text-decoration-none">
                                            {{ $payment->order->customer->name ?? '—' }}
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0 py-2">Order Status:</td>
                                    <td class="py-2">
                                        @php
                                            $status = $payment->order->status ?? '';
                                            $badgeClass = 'bg-secondary';
                                            if (in_array($status, ['pending', 'confirmed'])) $badgeClass = 'bg-warning text-dark';
                                            elseif (in_array($status, ['washing', 'drying', 'ironing', 'folding'])) $badgeClass = 'bg-primary';
                                            elseif ($status === 'ready_for_delivery') $badgeClass = 'bg-info text-dark';
                                            elseif ($status === 'out_for_delivery') $badgeClass = 'bg-info';
                                            elseif ($status === 'delivered') $badgeClass = 'bg-success';
                                            elseif ($status === 'cancelled') $badgeClass = 'bg-danger';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} text-capitalize px-2.5 py-1">{{ $status }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0 py-2">Order Total:</td>
                                    <td class="py-2 fw-bold text-primary">${{ number_format($payment->order->total_price ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0 py-2">Order Items Count:</td>
                                    <td class="py-2 fw-semibold text-dark">{{ $payment->order->orderItems->count() ?? 0 }} item(s)</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0 py-2">Customer Phone:</td>
                                    <td class="py-2 text-dark">{{ $payment->order->customer->phone ?? '—' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted ps-0 py-2">Customer Address:</td>
                                    <td class="py-2 text-dark small text-truncate" style="max-width: 250px;">{{ $payment->order->customer->address ?? '—' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Order Items Table -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-list-check text-primary me-2"></i>Purchased Items</h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive rounded-3 border">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Service</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payment->order->orderItems as $item)
                            <tr>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $item->service->name }}</span>
                                    @if($item->notes)
                                        <br><small class="text-muted">{{ $item->notes }}</small>
                                    @endif
                                </td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                <td class="text-end fw-semibold text-dark">${{ number_format($item->price * $item->quantity, 2) }}</td>
                            </tr>
                        @endforeach
                        <tr class="table-light fw-bold border-top-2">
                            <td colspan="3" class="text-end">Total Summary Price:</td>
                            <td class="text-end text-primary">${{ number_format($payment->order->total_price, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 4: Admin Action Panel Card -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white border-0 pt-4 px-4 pb-0">
            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-sliders text-primary me-2"></i>Payment Actions</h5>
        </div>
        <div class="card-body p-4">
            @if ($payment->status === 'pending')
                <div class="d-flex flex-wrap gap-3">
                    <div>
                        <button type="button" class="btn btn-success rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#headerCompleteModal">
                            <i class="fa-solid fa-check me-1"></i> Mark as Completed
                        </button>
                        <p class="text-muted small mt-2 mb-0">Confirming funds have cleared into mobile wallets or cash delivery collection completed.</p>
                    </div>

                    <div>
                        <button type="button" class="btn btn-danger rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#headerFailModal">
                            <i class="fa-solid fa-xmark me-1"></i> Mark as Failed
                        </button>
                        <p class="text-muted small mt-2 mb-0">Marking payment as void or unsuccessful, which requests customer retrial.</p>
                    </div>
                </div>

            @elseif ($payment->status === 'completed' && $payment->order && $payment->order->status !== 'delivered')
                <div>
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4 py-2" data-bs-toggle="modal" data-bs-target="#headerRefundModal">
                        <i class="fa-solid fa-rotate-left me-1"></i> Process Refund
                    </button>
                    <p class="text-muted small mt-2 mb-0">Triggering state refund will update payment and order statuses and alert the customer.</p>
                </div>

            @else
                <div class="alert alert-secondary border-0 mb-0 d-flex align-items-center gap-2">
                    <i class="fa-solid fa-circle-info fs-5"></i>
                    <span>No administrative actions available for this payment. It is finalized (failed, refunded, or completed for a delivered order).</span>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- MODALS FOR ADMIN QUICK ACTIONS --}}
@if ($payment->status === 'pending')
    <!-- Mark Completed Modal -->
    <div class="modal fade" id="headerCompleteModal" tabindex="-1" aria-labelledby="headerCompleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <form action="{{ route('admin.payments.complete', $payment->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-success text-white border-0 py-3">
                        <h5 class="modal-title fw-bold" id="headerCompleteModalLabel">Mark Payment as Completed</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-secondary mb-3">
                            Are you sure you want to mark this payment as completed? This will update the order payment status to <strong>'paid'</strong>.
                        </p>
                        <div class="mb-0">
                            <label for="header_transaction_reference" class="form-label fw-semibold text-secondary small">Transaction Reference (Optional)</label>
                            <input type="text" name="transaction_reference" id="header_transaction_reference" class="form-control rounded-3" placeholder="Enter Reference (Auto-generated if empty)">
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

    <!-- Mark Failed Modal -->
    <div class="modal fade" id="headerFailModal" tabindex="-1" aria-labelledby="headerFailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <form action="{{ route('admin.payments.fail', $payment->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white border-0 py-3">
                        <h5 class="modal-title fw-bold" id="headerFailModalLabel">Mark Payment as Failed</h5>
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
    <div class="modal fade" id="headerRefundModal" tabindex="-1" aria-labelledby="headerRefundModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-3">
                <form action="{{ route('admin.payments.refund', $payment->id) }}" method="POST">
                    @csrf
                    <div class="modal-header bg-danger text-white border-0 py-3">
                        <h5 class="modal-title fw-bold" id="headerRefundModalLabel">Process Payment Refund</h5>
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
                            <label for="header-refund-validation" class="form-label fw-semibold text-secondary small mb-1">Type <strong>CONFIRM</strong> to enable button</label>
                            <input type="text" id="header-refund-validation" class="form-control rounded-3" placeholder="Type CONFIRM here" oninput="document.getElementById('header-refund-btn').disabled = (this.value !== 'CONFIRM')">
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-3 bg-light d-flex gap-2 justify-content-end">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="header-refund-btn" class="btn btn-danger rounded-pill px-4" disabled>Process Refund</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection

@section('scripts')
<script>
    // Copy to clipboard helper
    function copyRefToClipboard(text) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                let btn = document.getElementById('copy-ref-btn');
                let originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check text-success"></i>';
                setTimeout(function() {
                    btn.innerHTML = originalHTML;
                }, 2000);
            });
        }
    }
</script>
@endsection
