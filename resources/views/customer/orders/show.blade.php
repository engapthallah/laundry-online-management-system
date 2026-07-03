@extends('layouts.customer-portal')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-dark">Track Order - #{{ $order->order_number }}</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-secondary fw-semibold">
            <i class="fa-solid fa-arrow-left me-2"></i>Back to My Orders
        </a>
        
        <!-- Cancel Order Button (Section 7: Only visible if status is pending_pickup) -->
        @if($order->status === 'pending_pickup')
            <form method="POST" action="{{ route('customer.orders.cancel', $order->id) }}" id="cancel-order-form" class="m-0">
                @csrf
                <button type="submit" class="btn btn-danger fw-semibold">
                    <i class="fa-solid fa-ban me-2"></i>Cancel Order
                </button>
            </form>
        @endif
    </div>
</div>

<!-- Section 1: Order Header -->
<div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
    <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <span class="text-muted small fw-semibold text-uppercase">Placed on: {{ $order->created_at->format('M d, Y h:i A') }}</span>
            <h4 class="fw-bold text-dark mb-0 mt-1">Order #{{ $order->order_number }}</h4>
        </div>
        <div class="text-md-end">
            <span class="text-muted small d-block mb-1">Total Amount</span>
            <h3 class="fw-bold text-primary mb-0">${{ number_format($order->total_price, 2) }}</h3>
        </div>
        <div>
            <span class="text-muted small d-block mb-1">Payment Status</span>
            @switch($order->payment_status)
                @case('pending')
                    <span class="badge bg-warning text-dark text-uppercase px-2.5 py-1.5">Pending</span>
                    @break
                @case('pending_verification')
                    <span class="badge bg-warning text-dark text-uppercase px-2.5 py-1.5">Pending Verification</span>
                    @break
                @case('awaiting_staff_review')
                    <span class="badge bg-info text-uppercase px-2.5 py-1.5">Awaiting Review</span>
                    @break
                @case('verified')
                    <span class="badge bg-success text-uppercase px-2.5 py-1.5">Verified</span>
                    @break
                @case('rejected')
                    <span class="badge bg-danger text-uppercase px-2.5 py-1.5">Rejected</span>
                    @break
                @case('paid')
                    <span class="badge bg-success text-uppercase px-2.5 py-1.5">Paid</span>
                    @break
                @case('failed')
                    <span class="badge bg-danger text-uppercase px-2.5 py-1.5">Failed</span>
                    @break
                @case('refunded')
                    <span class="badge bg-info text-uppercase px-2.5 py-1.5">Refunded</span>
                    @break
            @endswitch
        </div>
    </div>
</div>

{{-- STEP 1: Customer has placed mobile payment order but not yet confirmed payment --}}
@if(in_array($order->payment_method, ['zaad','edahab']) 
    && $order->payment_status === 'pending_verification')
<div class="alert alert-warning mt-3">
    <h6 class="fw-bold">💳 Confirm Your Payment</h6>
    <p class="mb-1">
        Please transfer <strong>${{ number_format($order->total_price, 2) }}</strong> 
        to the merchant number:
    </p>
    <p class="fs-5 fw-bold text-primary mb-3">
        @if($order->payment_method === 'zaad') 252-61-4700000 
        @else 252-63-4700000 @endif
    </p>
    <p class="mb-3">After sending the payment, click the button below to notify our staff.</p>

    <form method="POST" 
          action="{{ route('customer.orders.confirmPayment', $order->id) }}">
        @csrf
        @method('PATCH')
        <div class="d-flex gap-3 flex-wrap">
            <button type="submit" class="btn btn-success px-4 fw-semibold"
                    onclick="return confirm('Confirm that you have sent the payment?')">
                ✅ I Have Paid
            </button>
            <a href="{{ route('customer.support.create', ['order_id' => $order->id]) }}"
               class="btn btn-outline-secondary">
                💬 Need Help?
            </a>
        </div>
    </form>
</div>
@endif

{{-- STEP 2: Customer confirmed, waiting for staff review --}}
@if(in_array($order->payment_method, ['zaad','edahab']) 
    && $order->payment_status === 'awaiting_staff_review')
<div class="alert alert-info mt-3">
    <h6 class="fw-bold">⏳ Payment Under Review</h6>
    <p class="mb-0">
        Our staff is reviewing your payment. You will be notified once it is verified.
    </p>
</div>
@endif

{{-- STEP 3a: Payment verified --}}
@if($order->payment_status === 'verified')
<div class="alert alert-success mt-3">
    <h6 class="fw-bold">✅ Payment Verified</h6>
    <p class="mb-0">Your payment has been verified. Your order is being processed.</p>
</div>
@endif

{{-- STEP 3b: Payment rejected --}}
@if($order->payment_status === 'rejected')
<div class="alert alert-danger mt-3">
    <h6 class="fw-bold">❌ Payment Rejected</h6>
    <p class="mb-3">
        Your payment could not be verified and the order has been cancelled. If you have questions, please contact support.
    </p>
    <div class="d-flex gap-3 flex-wrap">
        <a href="{{ route('customer.support.create', ['order_id' => $order->id]) }}"
           class="btn btn-outline-danger">
            💬 Contact Support
        </a>
    </div>
</div>
@endif

<!-- Section 2: Visual Status Tracker -->
<div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="fw-bold text-dark mb-0">Order Tracking Status</h5>
    </div>
    <div class="card-body p-4 pt-0">
        @php
            $statusLabels = [
                'pending_pickup'          => 'Waiting for Pickup',
                'picked_up_from_customer' => 'Your laundry has been collected',
                'delivered_to_laundry'    => 'Laundry arrived at our shop',
                'processing'              => 'We are cleaning your laundry',
                'ready_for_delivery'      => 'Your laundry is ready',
                'picked_up_from_laundry'  => 'Out for delivery',
                'on_the_way'              => 'Almost there!',
                'delivered'               => 'Delivered successfully',
            ];
            $allStatuses = array_keys($statusLabels);
            $currentStatusIndex = array_search($order->status, $allStatuses);
        @endphp

        @if($order->status === 'cancelled')
            <div class="alert alert-danger border-0 rounded-4 p-4 mb-0">
                <h5 class="fw-bold"><i class="fa-solid fa-ban me-2"></i>Order Cancelled</h5>
                <p class="mb-0 small text-danger-emphasis">This order has been cancelled and will not be processed. If you believe this was an error, please open a support ticket.</p>
            </div>
        @else
            <!-- Desktop Stepper (Horizontal) -->
            <div class="d-none d-lg-block py-4">
                <div class="position-relative mb-4">
                    <!-- Progress Bar Background Line -->
                    <div class="progress position-absolute top-50 start-0 end-0 translate-middle-y bg-light" style="height: 6px; z-index: 0;">
                        @php
                            $percent = $currentStatusIndex !== false ? ($currentStatusIndex / (count($allStatuses) - 1)) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" role="progressbar" id="order-progress-bar" style="width: 0%; z-index: 0;" data-percent="{{ $percent }}"></div>
                    </div>

                    <!-- Steps Circles -->
                    <div class="d-flex justify-content-between position-relative" style="z-index: 1;">
                        @foreach($allStatuses as $index => $status)
                            <div class="d-flex flex-column align-items-center">
                                @if($index < $currentStatusIndex)
                                    <!-- Completed Step -->
                                    <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px;">
                                        <i class="fa-solid fa-check"></i>
                                    </div>
                                @elseif($index === $currentStatusIndex)
                                    <!-- Current Step -->
                                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px;">
                                        <i class="fa-solid fa-circle-dot"></i>
                                    </div>
                                @else
                                    <!-- Future Step -->
                                    <div class="rounded-circle bg-white text-muted border border-secondary-subtle d-flex align-items-center justify-content-center fw-bold shadow-sm" style="width: 38px; height: 38px;">
                                        {{ $index + 1 }}
                                    </div>
                                @endif
                                <span class="small fw-semibold text-dark mt-2 text-center" style="font-size: 0.65rem; max-width: 100px; line-height: 1.2;">
                                    {{ $statusLabels[$status] }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Mobile Stepper (Vertical) -->
            <div class="d-flex d-lg-none flex-column gap-3 ps-3 border-start border-2 border-primary-subtle py-2">
                @foreach($allStatuses as $index => $status)
                    <div class="d-flex align-items-center gap-3">
                        @if($index < $currentStatusIndex)
                            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; min-width: 28px;">
                                <i class="fa-solid fa-check small"></i>
                            </div>
                            <span class="text-success small fw-semibold">{{ $statusLabels[$status] }} (Completed)</span>
                        @elseif($index === $currentStatusIndex)
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; min-width: 28px;">
                                <i class="fa-solid fa-circle-dot small"></i>
                            </div>
                            <span class="text-primary small fw-bold">{{ $statusLabels[$status] }} (Current Status)</span>
                        @else
                            <div class="rounded-circle bg-white text-muted border border-secondary-subtle d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; min-width: 28px;">
                                {{ $index + 1 }}
                            </div>
                            <span class="text-muted small">{{ $statusLabels[$status] }}</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Items details -->
    <div class="col-12 col-lg-8">
        <!-- Section 3: Order Items Table -->
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Order Summary</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 px-4">Service</th>
                                <th class="border-0">Rate Type</th>
                                <th class="border-0">Rate</th>
                                <th class="border-0">Qty / Weight</th>
                                <th class="border-0 px-4 text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->orderItems as $item)
                                @php
                                    $subtotal = 0;
                                    if ($item->quantity > 0) {
                                        $subtotal = $item->quantity * $item->price;
                                    } elseif ($order->weight > 0) {
                                        $subtotal = $order->weight * $item->price;
                                    }
                                @endphp
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-bold text-dark">{{ $item->service->name ?? 'N/A' }}</div>
                                        @if($item->notes)
                                            <div class="small text-muted"><i class="fa-regular fa-comment-dots me-1"></i>Care Instructions: {{ $item->notes }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->quantity > 0)
                                            <span class="badge bg-light text-dark">Per Item</span>
                                        @else
                                            <span class="badge bg-light text-dark">Per KG</span>
                                        @endif
                                    </td>
                                    <td>${{ number_format($item->price, 2) }}</td>
                                    <td>
                                        @if($item->quantity > 0)
                                            {{ $item->quantity }} pcs
                                        @else
                                            {{ $order->weight ?? '0.00' }} kg
                                        @endif
                                    </td>
                                    <td class="px-4 text-end fw-bold text-dark">${{ number_format($subtotal, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold px-4 py-3">Total Amount:</td>
                                <td class="text-end fw-bold text-primary px-4 py-3 fs-5">${{ number_format($order->total_price, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Section 4: Addresses -->
        <div class="card border-0 shadow-sm rounded-4 bg-white">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Delivery Details</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <h6 class="fw-bold text-muted small text-uppercase mb-2"><i class="fa-solid fa-map-location-dot text-primary me-1"></i> Pickup Address</h6>
                        <p class="mb-0 text-dark">{{ $order->pickup_address }}</p>
                        @if($order->pickup_time)
                            <div class="small text-muted mt-2">
                                <i class="fa-regular fa-calendar me-1"></i>Scheduled: {{ $order->pickup_time->format('M d, Y h:i A') }}
                            </div>
                        @endif
                    </div>
                    <div class="col-12 col-md-6">
                        <h6 class="fw-bold text-muted small text-uppercase mb-2"><i class="fa-solid fa-map-location-dot text-primary me-1"></i> Delivery Address</h6>
                        <p class="mb-0 text-dark">{{ $order->delivery_address }}</p>
                        @if($order->delivery_time)
                            <div class="small text-muted mt-2">
                                <i class="fa-regular fa-calendar me-1"></i>Scheduled: {{ $order->delivery_time->format('M d, Y h:i A') }}
                            </div>
                        @endif
                    </div>
                    @if($order->special_instructions)
                        <div class="col-12">
                            <hr class="text-secondary opacity-25">
                            <h6 class="fw-bold text-muted small text-uppercase mb-2">Special Instructions</h6>
                            <p class="mb-0 text-dark">{{ $order->special_instructions }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Context Information -->
    <div class="col-12 col-lg-4">
        <!-- Section 5: Payment Information -->
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold text-dark mb-0">Payment Details</h5>
                @if($order->payment_status === 'pending' && ($order->payment_method === 'zaad' || $order->payment_method === 'edahab'))
                    <a href="{{ route('customer.payments.show', $order->payment->id ?? '') }}" class="btn btn-sm btn-primary fw-bold">Pay Now</a>
                @endif
            </div>
            <div class="card-body p-4 pt-0">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Payment Channel:</span>
                    <span class="fw-bold text-uppercase text-dark">{{ $order->payment_method }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Amount:</span>
                    <span class="fw-bold text-dark">${{ number_format($order->total_price, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Status:</span>
                    @switch($order->payment_status)
                        @case('pending')
                            <span class="badge bg-warning-subtle text-warning text-uppercase">Pending</span>
                            @break
                        @case('paid')
                            <span class="badge bg-success-subtle text-success text-uppercase">Paid</span>
                            @break
                        @default
                            <span class="badge bg-secondary-subtle text-secondary text-uppercase">{{ $order->payment_status }}</span>
                    @endswitch
                </div>
                @if($order->payment && $order->payment->transaction_reference)
                    <hr class="text-secondary opacity-25">
                    <div class="d-flex justify-content-between small align-items-center">
                        <span class="text-muted">Ref:</span>
                        <code class="text-dark">{{ $order->payment->transaction_reference }}</code>
                    </div>
                @endif

                @if($order->payment_status === 'paid' && $order->payment)
                    <hr class="text-secondary opacity-25">
                    <div class="d-grid mt-2">
                        <a href="{{ route('customer.payments.receipt', $order->payment->id) }}" target="_blank" class="btn btn-success rounded-pill fw-bold text-white btn-sm">
                            <i class="fa-solid fa-download me-1"></i> Download Receipt
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Section 6: Delivery Courier Information -->
        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Courier Information</h5>
            </div>
            <div class="card-body p-4 pt-0">
                @if($order->deliveryAgent)
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="rounded-circle bg-warning-subtle text-warning p-3">
                            <i class="fa-solid fa-truck fs-4"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold text-dark mb-0">{{ $order->deliveryAgent->name ?? 'N/A' }}</h6>
                            <span class="badge bg-warning text-dark text-uppercase fs-9">Courier</span>
                        </div>
                    </div>
                    <div class="small text-muted mb-2">
                        <i class="fa-regular fa-envelope me-2"></i>{{ $order->deliveryAgent->email ?? 'N/A' }}
                    </div>
                    <div class="small text-muted mb-2">
                        <i class="fa-solid fa-phone me-2"></i>{{ $order->deliveryAgent->phone ?? 'N/A' }}
                    </div>
                    <div class="d-flex justify-content-between small mt-3">
                        <span class="text-muted">Delivery Status:</span>
                        <span class="badge bg-info text-capitalize">{{ str_replace('_', ' ', $order->status) }}</span>
                    </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="fa-solid fa-truck fs-3 mb-2 d-block text-secondary"></i>
                        A courier agent will be assigned shortly.
                    </div>
                @endif
            </div>
        </div>

        <!-- Reviews Quick Access Card (if order is delivered) -->
        @if($order->status === 'delivered')
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-4 text-center">
                    <h5 class="fw-bold text-dark mb-2">How was your service?</h5>
                    <p class="text-muted small">Help us improve by leaving a review for this order.</p>
                    @if($order->review)
                        <span class="badge bg-light text-success fw-bold"><i class="fa-solid fa-check me-1"></i>Feedback Submitted</span>
                    @else
                        <a href="{{ route('customer.reviews.create', ['order_id' => $order->id]) }}" class="btn btn-primary w-100 fw-bold">Leave a Review</a>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Desktop progress bar animation
        const progressBar = document.getElementById('order-progress-bar');
        if (progressBar) {
            const percent = progressBar.dataset.percent;
            setTimeout(function () {
                progressBar.style.width = percent + '%';
            }, 100);
        }

        // Cancel order form confirmation
        const cancelForm = document.getElementById('cancel-order-form');
        if (cancelForm) {
            cancelForm.addEventListener('submit', function (event) {
                event.preventDefault();
                if (confirm('Are you sure you want to cancel this order? This action cannot be undone.')) {
                    this.submit();
                }
            });
        }
    });
</script>
@endsection
