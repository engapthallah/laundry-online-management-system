@extends('layouts.customer')

@section('content')
<style>
    .bg-zaad { background-color: #016837; }
    .text-zaad { color: #016837; }
    .border-zaad { border-color: #016837 !important; }
    .btn-zaad { background-color: #016837; color: white; border: none; }
    .btn-zaad:hover { background-color: #01502b; color: white; }

    .bg-edahab { background-color: #d9534f; }
    .text-edahab { color: #d9534f; }
    .border-edahab { border-color: #d9534f !important; }
    .btn-edahab { background-color: #d9534f; color: white; border: none; }
    .btn-edahab:hover { background-color: #c9302c; color: white; }

    .pulse-animation {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.08); }
        100% { transform: scale(1); }
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            <!-- Header navigation -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Order Detail
                </a>
                <span class="text-muted small">Order Reference: <strong>{{ $order->order_number }}</strong></span>
            </div>

            <!-- Card wrapper -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                {{-- CONDITION 1: Cash on Delivery --}}
                @if ($payment->payment_method === 'cash')
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <i class="fa-solid fa-money-bill-wave fa-5x text-success"></i>
                        </div>
                        <h2 class="fw-bold mb-2">Cash on Delivery</h2>
                        <h1 class="display-5 fw-extrabold text-dark mb-3">${{ number_format($payment->amount, 2) }}</h1>
                        
                        <div class="mb-4">
                            @if ($payment->status === 'pending')
                                <span class="badge bg-warning text-dark px-4 py-2 fs-6 rounded-pill">Payment Status: Pending</span>
                            @elseif ($payment->status === 'completed')
                                <span class="badge bg-success px-4 py-2 fs-6 rounded-pill">Payment Status: Completed</span>
                            @elseif ($payment->status === 'failed')
                                <span class="badge bg-danger px-4 py-2 fs-6 rounded-pill">Payment Status: Failed</span>
                            @elseif ($payment->status === 'refunded')
                                <span class="badge bg-info text-dark px-4 py-2 fs-6 rounded-pill">Payment Status: Refunded</span>
                            @endif
                        </div>

                        <div class="alert alert-info border-0 rounded-3 mb-4 text-start">
                            <div class="d-flex gap-2">
                                <i class="fa-solid fa-circle-info mt-1 fs-5"></i>
                                <div>
                                    Our delivery agent will collect <strong>${{ number_format($payment->amount, 2) }}</strong> when your order arrives at your doorstep. Please have the exact amount ready.
                                </div>
                            </div>
                        </div>

                        <!-- Order summary table -->
                        <div class="text-start mb-4">
                            <h5 class="fw-bold mb-3">Order Items Summary</h5>
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
                                        @foreach ($order->orderItems as $item)
                                            <tr>
                                                <td>
                                                    <span class="fw-semibold">{{ $item->service->name }}</span>
                                                    @if($item->notes)
                                                        <br><small class="text-muted">{{ $item->notes }}</small>
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $item->quantity }}</td>
                                                <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                                <td class="text-end">${{ number_format($item->price * $item->quantity, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr class="table-light fw-bold border-top-2">
                                            <td colspan="3" class="text-end">Total Amount:</td>
                                            <td class="text-end text-primary">${{ number_format($order->total_price, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="d-grid">
                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-primary btn-lg rounded-pill">
                                View Order
                            </a>
                        </div>
                    </div>

                {{-- CONDITION 2: Zaad Pending --}}
                @elseif ($payment->payment_method === 'zaad' && $payment->status === 'pending')
                    <div class="card-header bg-zaad text-white py-3 px-4 d-flex align-items-center">
                        <i class="fa-solid fa-mobile-screen-button fs-4 me-2"></i>
                        <span class="fw-bold">Zaad Mobile Money Payment</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <span class="text-muted d-block mb-1">Amount to Transfer</span>
                            <div class="border border-2 border-zaad rounded-3 py-3 px-4 d-inline-block">
                                <h2 class="fw-bold text-zaad mb-0">${{ number_format($payment->amount, 2) }}</h2>
                            </div>
                        </div>

                        <h5 class="fw-bold mb-3">Step-by-Step Payment Instructions</h5>
                        <ol class="list-group list-group-numbered mb-4 rounded-3 overflow-hidden">
                            <li class="list-group-item d-flex justify-content-between align-items-start py-3">
                                <div class="ms-2 me-auto">
                                    Open your Zaad app on your phone
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start py-3">
                                <div class="ms-2 me-auto">
                                    Select 'Send Money' or dial <strong class="font-monospace text-dark">*681#</strong>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div class="ms-2 me-auto">
                                    Enter Merchant Number: <strong id="zaad-merchant" class="font-monospace text-dark fs-5">252-61-4700000</strong>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="copy-merchant-btn" onclick="copyToClipboard('252-61-4700000', 'copy-merchant-btn')">
                                    <i class="fa-regular fa-copy"></i> Copy
                                </button>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start py-3">
                                <div class="ms-2 me-auto">
                                    Enter the exact amount: <strong class="text-dark">${{ number_format($payment->amount, 2) }}</strong>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div class="ms-2 me-auto">
                                    Enter Reference/Note: <strong id="zaad-ref" class="font-monospace text-dark fs-5">{{ $order->order_number }}</strong>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="copy-ref-btn" onclick="copyToClipboard('{{ $order->order_number }}', 'copy-ref-btn')">
                                    <i class="fa-regular fa-copy"></i> Copy
                                </button>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start py-3">
                                <div class="ms-2 me-auto">
                                    Confirm the transaction in your Zaad app
                                </div>
                            </li>
                        </ol>

                        <div class="alert alert-warning border-0 rounded-3 mb-4">
                            <div class="d-flex gap-2">
                                <i class="fa-solid fa-triangle-exclamation mt-1 fs-5 text-warning"></i>
                                <div>
                                    Only click the button below <strong>AFTER</strong> you have successfully completed the payment in your Zaad app. This cannot be undone.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 border p-3 rounded-3 bg-light">
                            <span class="text-muted"><i class="fa-regular fa-clock me-1"></i> Complete payment within:</span>
                            <span id="countdown-timer" class="fw-bold text-danger fs-5 font-monospace">30:00</span>
                        </div>

                        <form action="{{ route('customer.payments.confirm', $payment->id) }}" method="POST" onsubmit="return confirmZaadPayment('{{ number_format($payment->amount, 2) }}', '{{ $order->order_number }}')">
                            @csrf
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg rounded-pill fw-bold shadow-sm">
                                    ✓ I Have Completed the Zaad Payment
                                </button>
                            </div>
                        </form>
                    </div>

                {{-- CONDITION 3: Edahab Pending --}}
                @elseif ($payment->payment_method === 'edahab' && $payment->status === 'pending')
                    <div class="card-header bg-edahab text-white py-3 px-4 d-flex align-items-center">
                        <i class="fa-solid fa-mobile-screen-button fs-4 me-2"></i>
                        <span class="fw-bold">Edahab Mobile Money Payment</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <span class="text-muted d-block mb-1">Amount to Transfer</span>
                            <div class="border border-2 border-edahab rounded-3 py-3 px-4 d-inline-block">
                                <h2 class="fw-bold text-edahab mb-0">${{ number_format($payment->amount, 2) }}</h2>
                            </div>
                        </div>

                        <h5 class="fw-bold mb-3">Step-by-Step Payment Instructions</h5>
                        <ol class="list-group list-group-numbered mb-4 rounded-3 overflow-hidden">
                            <li class="list-group-item d-flex justify-content-between align-items-start py-3">
                                <div class="ms-2 me-auto">
                                    Open your Edahab app or dial <strong class="font-monospace text-dark">*326#</strong>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start py-3">
                                <div class="ms-2 me-auto">
                                    Select option: <strong class="text-dark">"Pay Bill"</strong> or <strong class="text-dark">"Merchant Payment"</strong>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div class="ms-2 me-auto">
                                    Enter Merchant Number: <strong id="edahab-merchant" class="font-monospace text-dark fs-5">252-63-4700000</strong>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="copy-merchant-btn" onclick="copyToClipboard('252-63-4700000', 'copy-merchant-btn')">
                                    <i class="fa-regular fa-copy"></i> Copy
                                </button>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start py-3">
                                <div class="ms-2 me-auto">
                                    Enter the exact amount: <strong class="text-dark">${{ number_format($payment->amount, 2) }}</strong>
                                </div>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                <div class="ms-2 me-auto">
                                    Enter Reference/Note: <strong id="edahab-ref" class="font-monospace text-dark fs-5">{{ $order->order_number }}</strong>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="copy-ref-btn" onclick="copyToClipboard('{{ $order->order_number }}', 'copy-ref-btn')">
                                    <i class="fa-regular fa-copy"></i> Copy
                                </button>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-start py-3">
                                <div class="ms-2 me-auto">
                                    Confirm the transaction in your Edahab app
                                </div>
                            </li>
                        </ol>

                        <div class="alert alert-warning border-0 rounded-3 mb-4">
                            <div class="d-flex gap-2">
                                <i class="fa-solid fa-triangle-exclamation mt-1 fs-5 text-warning"></i>
                                <div>
                                    Only click the button below <strong>AFTER</strong> you have successfully completed the payment in your Edahab app. This cannot be undone.
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4 border p-3 rounded-3 bg-light">
                            <span class="text-muted"><i class="fa-regular fa-clock me-1"></i> Complete payment within:</span>
                            <span id="countdown-timer" class="fw-bold text-danger fs-5 font-monospace">30:00</span>
                        </div>

                        <form action="{{ route('customer.payments.confirm', $payment->id) }}" method="POST" onsubmit="return confirmEdahabPayment('{{ number_format($payment->amount, 2) }}', '{{ $order->order_number }}')">
                            @csrf
                            <div class="d-grid">
                                <button type="submit" class="btn btn-danger btn-lg rounded-pill fw-bold shadow-sm">
                                    ✓ I Have Completed the Edahab Payment
                                </button>
                            </div>
                        </form>
                    </div>

                {{-- CONDITION 4: Status Completed --}}
                @elseif ($payment->status === 'completed')
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <i class="fa-solid fa-circle-check fa-5x text-success pulse-animation"></i>
                        </div>
                        <h2 class="fw-bold text-success mb-2">Payment Confirmed!</h2>
                        <h1 class="display-5 fw-extrabold text-dark mb-4">${{ number_format($payment->amount, 2) }}</h1>

                        <div class="row justify-content-center mb-4">
                            <div class="col-md-8">
                                <div class="table-responsive rounded-3 border text-start bg-light p-3">
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-muted">Payment Method:</span>
                                        <span class="badge bg-success px-3 py-1">{{ strtoupper($payment->payment_method) }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between py-2 border-bottom">
                                        <span class="text-muted">Transaction Ref:</span>
                                        <code class="fw-bold text-dark fs-6">{{ $payment->transaction_reference ?? '—' }}</code>
                                    </div>
                                    <div class="d-flex justify-content-between py-2">
                                        <span class="text-muted">Paid At:</span>
                                        <span class="fw-semibold">{{ $payment->paid_at ? $payment->paid_at->format('M d, Y h:i A') : '—' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center gap-3">
                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                                <i class="fa-solid fa-receipt me-1"></i> View Order Status
                            </a>
                            <a href="{{ route('customer.payments.receipt', $payment->id) }}" target="_blank" class="btn btn-success btn-lg rounded-pill px-4">
                                <i class="fa-solid fa-download me-1"></i> Download Receipt
                            </a>
                        </div>
                    </div>

                {{-- CONDITION 5: Status Failed --}}
                @elseif ($payment->status === 'failed')
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <i class="fa-solid fa-circle-xmark fa-5x text-danger"></i>
                        </div>
                        <h2 class="fw-bold text-danger mb-2">Payment Failed</h2>
                        <p class="text-muted mb-4 fs-5">
                            We could not process your payment. This may be due to insufficient balance or a network issue. Please try again.
                        </p>

                        <div class="d-flex justify-content-center gap-3">
                            <form action="{{ route('customer.payments.retry', $payment->id) }}" method="POST" onsubmit="return confirmRetry()">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-lg rounded-pill px-4">
                                    <i class="fa-solid fa-rotate-right me-1"></i> Retry Payment
                                </button>
                            </form>
                            <a href="{{ route('customer.support.create') }}" class="btn btn-outline-secondary btn-lg rounded-pill px-4">
                                <i class="fa-solid fa-headset me-1"></i> Contact Support
                            </a>
                        </div>
                    </div>

                {{-- CONDITION 6: Status Refunded --}}
                @elseif ($payment->status === 'refunded')
                    <div class="card-body p-5 text-center">
                        <div class="mb-4">
                            <i class="fa-solid fa-rotate-left fa-5x text-info"></i>
                        </div>
                        <h2 class="fw-bold text-info mb-2">Payment Refunded</h2>
                        <h4 class="fw-bold text-dark mb-3">Amount Refunded: ${{ number_format($payment->amount, 2) }}</h4>
                        <p class="text-muted mb-4 px-lg-5">
                            Your payment has been refunded by our admin team. Please allow 3-5 business days for the amount to reflect in your mobile wallet or account.
                        </p>

                        <div class="d-grid col-md-6 mx-auto">
                            <a href="{{ route('customer.orders.show', $order->id) }}" class="btn btn-primary btn-lg rounded-pill">
                                View Order
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Copy to clipboard helper using Navigators API
    function copyToClipboard(text, buttonId) {
        if (navigator.clipboard) {
            navigator.clipboard.writeText(text).then(function() {
                let btn = document.getElementById(buttonId);
                let originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Copied!';
                btn.className = "btn btn-sm btn-success text-white";
                setTimeout(function() {
                    btn.innerHTML = originalHTML;
                    btn.className = "btn btn-sm btn-outline-secondary";
                }, 2000);
            }).catch(function(err) {
                console.error('Failed to copy: ', err);
            });
        }
    }

    // Countdown Timer logic
    document.addEventListener('DOMContentLoaded', function () {
        let timerDisplay = document.getElementById('countdown-timer');
        if (timerDisplay) {
            let duration = 30 * 60; // 30 minutes in seconds
            let timer = duration, minutes, seconds;
            let interval = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                timerDisplay.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(interval);
                    timerDisplay.textContent = "00:00";
                }
            }, 1000);
        }
    });

    // Mobile Money Confirmations
    function confirmZaadPayment(amount, orderNumber) {
        return confirm("Confirm Payment: Have you transferred $" + amount + " to merchant 252-61-4700000 with reference " + orderNumber + "? This action will mark your payment as confirmed.");
    }

    function confirmEdahabPayment(amount, orderNumber) {
        return confirm("Confirm Payment: Have you transferred $" + amount + " to merchant 252-63-4700000 with reference " + orderNumber + "? This action will mark your payment as confirmed.");
    }

    // Retry Confirmation
    function confirmRetry() {
        return confirm("Are you sure you want to retry this payment? This will reset the status to pending.");
    }
</script>
@endsection
