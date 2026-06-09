<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Receipt - REC-{{ $payment->id }}-{{ $payment->created_at->format('Y') }}</title>
    
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome 6.5.0 CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #333;
        }
        .receipt-container {
            max-width: 800px;
            margin: 40px auto;
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }
        .business-logo {
            font-size: 28px;
            font-weight: 800;
            color: #0d6efd;
            letter-spacing: -0.5px;
        }
        .receipt-title {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #2c3e50;
        }
        .invoice-details-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }
        .info-title {
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #888;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .table th {
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.5px;
            background-color: #f8f9fa !important;
        }
        .table td {
            padding: 12px 8px;
        }
        .total-amount-box {
            border-top: 2px solid #333;
            padding-top: 15px;
        }
        @media print {
            body {
                background-color: #fff !important;
                color: #000 !important;
            }
            .receipt-container {
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
                max-width: 100% !important;
                border-radius: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .invoice-details-box {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .table th {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Action buttons (Hidden on print) -->
    <div class="row no-print mt-4">
        <div class="col-12 text-center">
            <button onclick="window.print();" class="btn btn-primary btn-lg rounded-pill px-4 me-2 shadow-sm">
                <i class="fa-solid fa-print me-1"></i> Print Receipt
            </button>
            <button onclick="window.close();" class="btn btn-outline-secondary btn-lg rounded-pill px-4 shadow-sm">
                <i class="fa-solid fa-xmark me-1"></i> Close Window
            </button>
        </div>
    </div>

    <!-- Official Receipt Area -->
    <div class="receipt-container my-4">
        <!-- Header -->
        <div class="row align-items-center mb-4">
            <div class="col-sm-6 text-center text-sm-start mb-3 mb-sm-0">
                <div class="business-logo"><i class="fa-solid fa-soap me-2"></i>LOMS</div>
                <h5 class="receipt-title mt-1 mb-0">Official Payment Receipt</h5>
            </div>
            <div class="col-sm-6 text-center text-sm-end">
                <div class="text-muted small">Receipt Number</div>
                <h5 class="fw-bold text-dark mb-1">REC-{{ $payment->id }}-{{ $payment->created_at->format('Y') }}</h5>
                <div class="small text-muted">Date Issued: {{ now()->format('M d, Y') }}</div>
            </div>
        </div>

        <hr class="my-4">

        <!-- Info Section -->
        <div class="row g-4 mb-4">
            <!-- Business Info -->
            <div class="col-sm-6">
                <div class="info-title">Issued By</div>
                <h6 class="fw-bold text-dark mb-1">Iimaan Dry Cleaner</h6>
                <p class="text-muted small mb-0">
                    Hargeisa, Somaliland<br>
                    Email: info@loms.com<br>
                    Phone: +252-61-4700000
                </p>
            </div>
            <!-- Customer Info -->
            <div class="col-sm-6">
                <div class="info-title">Billed To</div>
                <h6 class="fw-bold text-dark mb-1">{{ $payment->order->customer->name ?? '—' }}</h6>
                <p class="text-muted small mb-0">
                    Email: {{ $payment->order->customer->email ?? '—' }}<br>
                    Phone: {{ $payment->order->customer->phone ?? '—' }}<br>
                    Address: {{ $payment->order->customer->address ?? '—' }}
                </p>
            </div>
        </div>

        <!-- Payment Metadata block -->
        <div class="invoice-details-box mb-4">
            <div class="row g-3">
                <div class="col-6 col-sm-3">
                    <span class="info-title d-block">Order Number</span>
                    <strong class="text-dark">{{ $payment->order->order_number ?? '—' }}</strong>
                </div>
                <div class="col-6 col-sm-3">
                    <span class="info-title d-block">Payment Method</span>
                    <strong class="text-dark text-uppercase">{{ $payment->payment_method }}</strong>
                </div>
                <div class="col-6 col-sm-3">
                    <span class="info-title d-block">Transaction Ref</span>
                    <code class="text-dark fw-bold">{{ $payment->transaction_reference ?? '—' }}</code>
                </div>
                <div class="col-6 col-sm-3 text-sm-end">
                    <span class="info-title d-block">Payment Status</span>
                    <span class="badge bg-success text-uppercase">{{ $payment->status }}</span>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-4">
            <div class="info-title mb-2">Order Summary Details</div>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th class="ps-3">Service Description</th>
                            <th class="text-center" style="width: 120px;">Quantity</th>
                            <th class="text-end" style="width: 150px;">Unit Price</th>
                            <th class="text-end pe-3" style="width: 150px;">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payment->order->orderItems as $item)
                            @php
                                $subtotal = 0;
                                if ($item->quantity > 0) {
                                    $subtotal = $item->quantity * $item->price;
                                } elseif ($payment->order->weight > 0) {
                                    $subtotal = $payment->order->weight * $item->price;
                                }
                            @endphp
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-semibold text-dark">{{ $item->service->name ?? 'N/A' }}</div>
                                    @if($item->notes)
                                        <div class="text-muted small italic">{{ $item->notes }}</div>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($item->quantity > 0)
                                        {{ $item->quantity }} pcs
                                    @else
                                        {{ $payment->order->weight ?? '0.00' }} kg
                                    @endif
                                </td>
                                <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                <td class="text-end pe-3 fw-semibold text-dark">${{ number_format($subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Totals block -->
        <div class="row justify-content-end">
            <div class="col-md-5">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Subtotal:</span>
                    <span class="text-dark fw-semibold">${{ number_format($payment->amount, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Tax / Service Fee:</span>
                    <span class="text-dark fw-semibold">$0.00</span>
                </div>
                <div class="total-amount-box d-flex justify-content-between mb-2 align-items-center pt-3">
                    <span class="fw-bold text-dark fs-5">Total Paid:</span>
                    <span class="fw-extrabold text-primary fs-3">${{ number_format($payment->amount, 2) }}</span>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <!-- Footer -->
        <div class="text-center text-muted small mt-4">
            <p class="mb-1 fw-bold">Thank you for choosing Iimaan Dry Cleaner!</p>
            <p class="mb-0">For queries or support, contact us at <strong>support@loms.com</strong></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
