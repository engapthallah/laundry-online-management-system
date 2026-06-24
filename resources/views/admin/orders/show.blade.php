@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-dark">Order Profile - #{{ $order->order_number }}</h1>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary fw-semibold">
        <i class="fa-solid fa-arrow-left me-2"></i>Back to Orders
    </a>
</div>

<div class="row g-4">
    <!-- Left Column: Details & Items -->
    <div class="col-12 col-lg-8">
        <!-- Order Items Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Order Items & Summary</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 px-4">Service</th>
                                <th class="border-0">Rate Type</th>
                                <th class="border-0">Rate</th>
                                <th class="border-0">Qty/Weight</th>
                                <th class="border-0 px-4 text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $calcTotal = 0; @endphp
                            @forelse($order->orderItems as $item)
                                @php
                                    $subtotal = 0;
                                    if ($item->quantity > 0) {
                                        $subtotal = $item->quantity * $item->price;
                                    } elseif ($order->weight > 0) {
                                        $subtotal = $order->weight * $item->price;
                                    }
                                    $calcTotal += $subtotal;
                                @endphp
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-semibold text-dark">{{ $item->service->name ?? 'N/A' }}</div>
                                        @if($item->notes)
                                            <div class="small text-muted">Notes: {{ $item->notes }}</div>
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
                                    <td class="px-4 text-end fw-semibold text-dark">${{ number_format($subtotal, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No items in this order.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="4" class="text-end fw-bold px-4 py-3">Total Cost:</td>
                                <td class="text-end fw-bold text-primary px-4 py-3 fs-5">${{ number_format($order->total_price, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Special Instructions & Addresses -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Delivery & Instructions</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <h6 class="fw-bold text-muted small text-uppercase">Pickup Address</h6>
                        <p class="mb-0 text-dark">{{ $order->pickup_address }}</p>
                    </div>
                    <div class="col-12 col-md-6">
                        <h6 class="fw-bold text-muted small text-uppercase">Delivery Address</h6>
                        <p class="mb-0 text-dark">{{ $order->delivery_address }}</p>
                    </div>
                    <div class="col-12">
                        <hr class="text-secondary opacity-25">
                        <h6 class="fw-bold text-muted small text-uppercase">Special Instructions</h6>
                        <p class="mb-0 text-dark">{{ $order->special_instructions ?? 'No instructions provided.' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Log -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Order Timeline</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <ul class="list-group list-group-flush border-start border-primary border-2 ms-3 px-0">
                    <li class="list-group-item border-0 position-relative py-3">
                        <span class="position-absolute start-0 translate-middle bg-primary rounded-circle p-1.5" style="left: -1px !important;"></span>
                        <div class="fw-semibold text-dark">Order Created</div>
                        <div class="small text-muted">{{ $order->created_at->format('M d, Y \a\t g:i A') }}</div>
                    </li>
                    @if($order->pickup_time)
                        <li class="list-group-item border-0 position-relative py-3">
                            <span class="position-absolute start-0 translate-middle bg-primary rounded-circle p-1.5" style="left: -1px !important;"></span>
                            <div class="fw-semibold text-dark">Pickup Scheduled</div>
                            <div class="small text-muted">{{ $order->pickup_time->format('M d, Y \a\t g:i A') }}</div>
                        </li>
                    @endif
                    @if($order->status === 'delivered' && $order->updated_at)
                        <li class="list-group-item border-0 position-relative py-3">
                            <span class="position-absolute start-0 translate-middle bg-success rounded-circle p-1.5" style="left: -1px !important;"></span>
                            <div class="fw-semibold text-success">Order Delivered</div>
                            <div class="small text-muted">{{ $order->updated_at->format('M d, Y \a\t g:i A') }}</div>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <!-- Right Column: Controls & Context -->
    <div class="col-12 col-lg-4">
        <!-- Order Status Display (Read-only) -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3">Order Status</h5>
                @switch($order->status)
                    @case('pending_pickup')
                        <span class="badge bg-secondary text-capitalize px-3 py-2 fs-6">Pending Pickup</span>
                        @break
                    @case('picked_up_from_customer')
                        <span class="badge bg-primary text-capitalize px-3 py-2 fs-6">Picked Up</span>
                        @break
                    @case('delivered_to_laundry')
                        <span class="badge bg-info text-dark text-capitalize px-3 py-2 fs-6">At Laundry</span>
                        @break
                    @case('processing')
                        <span class="badge bg-warning text-dark text-capitalize px-3 py-2 fs-6">Processing</span>
                        @break
                    @case('ready_for_delivery')
                        <span class="badge bg-teal text-capitalize px-3 py-2 fs-6">Ready for Delivery</span>
                        @break
                    @case('picked_up_from_laundry')
                        <span class="badge bg-primary text-capitalize px-3 py-2 fs-6">Picked Up from Laundry</span>
                        @break
                    @case('on_the_way')
                        <span class="badge bg-dark text-capitalize px-3 py-2 fs-6">On the Way</span>
                        @break
                    @case('delivered')
                        <span class="badge bg-success text-capitalize px-3 py-2 fs-6">Delivered</span>
                        @break
                    @case('cancelled')
                        <span class="badge bg-danger text-capitalize px-3 py-2 fs-6">Cancelled</span>
                        @break
                @endswitch
            </div>
        </div>

        <!-- Staff Assignment Control (Read-only) -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3">Assigned Laundry Operator</h5>
                <p class="form-control-plaintext text-dark fw-semibold fs-6 mb-0">
                    {{ $order->staff->name ?? 'Not yet assigned' }}
                </p>
            </div>
        </div>

        <!-- Customer Profile Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Customer Details</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-secondary-subtle text-secondary p-3">
                        <i class="fa-solid fa-user fs-4"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold text-dark mb-0">{{ $order->customer->name ?? 'N/A' }}</h6>
                        <span class="badge bg-success-subtle text-success text-uppercase fs-9">Verified</span>
                    </div>
                </div>
                <div class="small text-muted mb-2">
                    <i class="fa-regular fa-envelope me-2"></i>{{ $order->customer->email ?? 'N/A' }}
                </div>
                <div class="small text-muted">
                    <i class="fa-solid fa-phone me-2"></i>{{ $order->customer->phone ?? 'N/A' }}
                </div>
            </div>
        </div>

        <!-- Payment Info Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Payment Summary</h5>
            </div>
            <div class="card-body p-4 pt-0">
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Payment status:</span>
                    @switch($order->payment_status)
                        @case('pending')
                            <span class="badge bg-warning text-dark text-uppercase">Pending</span>
                            @break
                        @case('paid')
                            <span class="badge bg-success text-uppercase">Paid</span>
                            @break
                        @case('failed')
                            <span class="badge bg-danger text-uppercase">Failed</span>
                            @break
                        @case('refunded')
                            <span class="badge bg-info text-uppercase">Refunded</span>
                            @break
                    @endswitch
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Method:</span>
                    <span class="fw-semibold text-uppercase text-dark">{{ $order->payment_method ?? 'N/A' }}</span>
                </div>
                @if($order->payment && $order->payment->transaction_reference)
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Ref:</span>
                        <code class="text-dark">{{ $order->payment->transaction_reference }}</code>
                    </div>
                @endif
            </div>
        </div>

        <!-- Delivery Assignment Card -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Delivery Details</h5>
            </div>
            <div class="card-body p-4 pt-0">
                @if($order->deliveryAgent)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Agent:</span>
                        <span class="fw-semibold text-dark">{{ $order->deliveryAgent->name ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Delivery Status:</span>
                        <span class="badge bg-info text-uppercase">{{ str_replace('_', ' ', $order->status) }}</span>
                    </div>
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="fa-solid fa-truck fs-3 mb-2 d-block text-secondary"></i>
                        No delivery agent assigned yet.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
