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
        <!-- Status Update Control -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-3">Update Order Status</h5>
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}">
                    @csrf
                    @method('PATCH')
                    
                    <div class="mb-3">
                        <select name="status" id="status" class="form-select bg-light">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="washing" {{ $order->status === 'washing' ? 'selected' : '' }}>Washing</option>
                            <option value="drying" {{ $order->status === 'drying' ? 'selected' : '' }}>Drying</option>
                            <option value="ironing" {{ $order->status === 'ironing' ? 'selected' : '' }}>Ironing</option>
                            <option value="folding" {{ $order->status === 'folding' ? 'selected' : '' }}>Folding</option>
                            <option value="ready_for_delivery" {{ $order->status === 'ready_for_delivery' ? 'selected' : '' }}>Ready for Delivery</option>
                            <option value="out_for_delivery" {{ $order->status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 fw-bold">Update Status</button>
                </form>
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
                @if($order->deliveryAssignment)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Agent:</span>
                        <span class="fw-semibold text-dark">{{ $order->deliveryAssignment->deliveryAgent->name ?? 'N/A' }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Delivery Status:</span>
                        <span class="badge bg-info text-uppercase">{{ $order->deliveryAssignment->status }}</span>
                    </div>
                    @if($order->deliveryAssignment->assigned_at)
                        <div class="d-flex justify-content-between small text-muted">
                            <span>Assigned:</span>
                            <span>{{ $order->deliveryAssignment->assigned_at->format('M d, g:i A') }}</span>
                        </div>
                    @endif
                @else
                    <div class="text-center py-3 text-muted">
                        <i class="fa-solid fa-truck fs-3 mb-2 d-block text-secondary"></i>
                        No delivery agent assigned yet.
                        @if($order->status === 'ready_for_delivery')
                            <a href="{{ route('admin.delivery.create') }}" class="btn btn-sm btn-outline-primary fw-semibold mt-2 d-block mx-auto w-75">Assign Agent</a>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
