@extends('layouts.staff')

@section('content')
<style>
    /* Status Badge styling */
    .badge-status {
        padding: 6px 14px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        display: inline-block;
    }
    .badge-status-confirmed { background-color: #0d6efd; color: #fff; }
    .badge-status-washing { background-color: #6610f2; color: #fff; }
    .badge-status-drying { background-color: #6f42c1; color: #fff; }
    .badge-status-ironing { background-color: #8f00ff; color: #fff; }
    .badge-status-folding { background-color: #20c997; color: #fff; }
    .badge-status-ready_for_delivery { background-color: #198754; color: #fff; }
    .badge-status-out_for_delivery { background-color: #fd7e14; color: #fff; }
    .badge-status-delivered { background-color: #198754; color: #fff; }
    .badge-status-cancelled { background-color: #dc3545; color: #fff; }

    /* Custom colored buttons */
    .btn-indigo {
        background-color: #6610f2;
        border-color: #6610f2;
        color: #fff;
    }
    .btn-indigo:hover {
        background-color: #520dc2;
        border-color: #4e0cba;
        color: #fff;
    }
    .btn-purple {
        background-color: #6f42c1;
        border-color: #6f42c1;
        color: #fff;
    }
    .btn-purple:hover {
        background-color: #59359a;
        border-color: #533190;
        color: #fff;
    }
    .btn-violet {
        background-color: #8f00ff;
        border-color: #8f00ff;
        color: #fff;
    }
    .btn-violet:hover {
        background-color: #7300cc;
        border-color: #6c00bf;
        color: #fff;
    }
    .btn-teal {
        background-color: #20c997;
        border-color: #20c997;
        color: #fff;
    }
    .btn-teal:hover {
        background-color: #1aa179;
        border-color: #199772;
        color: #fff;
    }

    /* Vertical Timeline styles */
    .timeline-container {
        position: relative;
        padding-left: 30px;
        margin-top: 20px;
    }
    .timeline-container::before {
        content: '';
        position: absolute;
        left: 9px;
        top: 0;
        bottom: 0;
        width: 2px;
        background-color: #dee2e6;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 24px;
    }
    .timeline-item:last-child {
        padding-bottom: 0;
    }
    .timeline-dot {
        position: absolute;
        left: -30px;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background-color: #fff;
        border: 3px solid #ced4da;
        z-index: 1;
    }
    .timeline-item.completed .timeline-dot {
        border-color: #198754;
        background-color: #198754;
    }
    .timeline-item.active .timeline-dot {
        border-color: #0d6efd;
        background-color: #fff;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.25);
    }
    .timeline-content {
        padding-left: 10px;
    }
    .timeline-title {
        font-size: 0.95rem;
        font-weight: 700;
        margin-bottom: 2px;
    }
    .timeline-item.completed .timeline-title {
        color: #198754;
    }
    .timeline-item.active .timeline-title {
        color: #0d6efd;
    }
    .timeline-time {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>

<!-- Urgency Alert Check -->
@php
    $now = now();
    $pickupTime = $order->pickup_time;
    $diffInHours = $now->diffInHours($pickupTime, false);
    $isUrgent = ($diffInHours <= 24);
    
    // Privacy Masking for addresses (exposing city/sector last value only)
    $pickupParts = explode(',', $order->pickup_address);
    $pickupDisplay = count($pickupParts) > 1 ? trim(end($pickupParts)) : $order->pickup_address;
    
    $deliveryParts = explode(',', $order->delivery_address);
    $deliveryDisplay = count($deliveryParts) > 1 ? trim(end($deliveryParts)) : $order->delivery_address;
@endphp

@if($isUrgent && !in_array($order->status, ['ready_for_delivery', 'out_for_delivery', 'delivered', 'cancelled']))
    <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center gap-3 mb-4" role="alert">
        <div class="rounded-circle bg-warning-subtle text-warning p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
            <i class="fa-solid fa-triangle-exclamation fs-4"></i>
        </div>
        <div>
            <strong class="fw-bold">⚠ Urgency Alert:</strong> This order must be ready by <span class="fw-bold">{{ $order->pickup_time->format('M d, Y h:i A') }}</span> ({{ $order->pickup_time->diffForHumans() }}).
        </div>
    </div>
@endif

<!-- Order Header Bar -->
<div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <a href="{{ route('staff.orders.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Orders
            </a>
            <h3 class="fw-bold text-dark mb-1">Order #{{ $order->order_number }}</h3>
            <p class="text-secondary mb-0">Assigned Laundry processing details</p>
        </div>
        <div class="text-lg-end">
            <span class="badge-status badge-status-{{ $order->status }} fs-6">
                {{ str_replace('_', ' ', $order->status) }}
            </span>
            <div class="text-secondary small mt-2">
                <strong>Assigned:</strong> {{ $order->created_at->format('M d, Y h:i A') }}
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Main Info Col (Left) -->
    <div class="col-12 col-xl-8">
        
        <!-- Two-column info panel -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4">Order Specifications</h5>
            <div class="row g-4">
                
                <!-- Left Column: Customer & Delivery Info -->
                <div class="col-12 col-md-6">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fa-solid fa-user me-2"></i>Customer Information
                    </h6>
                    <p class="mb-2"><strong>Name:</strong> {{ $order->customer->name }}</p>
                    <p class="mb-2"><strong>Pickup Area:</strong> {{ $pickupDisplay }}</p>
                    <p class="mb-4"><strong>Delivery Area:</strong> {{ $deliveryDisplay }}</p>

                    @if($order->special_instructions)
                        <div class="p-3 bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-3">
                            <h6 class="fw-bold mb-2"><i class="fa-solid fa-circle-exclamation me-1"></i>Special Instructions:</h6>
                            <p class="mb-0 small text-justify">{{ $order->special_instructions }}</p>
                        </div>
                    @endif
                </div>

                <!-- Right Column: Financial & Summary Info -->
                <div class="col-12 col-md-6">
                    <h6 class="fw-bold text-primary mb-3">
                        <i class="fa-solid fa-receipt me-2"></i>Order Summary
                    </h6>
                    <p class="mb-2">
                        <strong>Total Price:</strong> 
                        <span class="fw-bold text-dark">${{ number_format($order->total_price, 2) }}</span>
                    </p>
                    
                    <p class="mb-2">
                        <strong>Weight:</strong> 
                        <span class="badge bg-secondary px-2 py-1">
                            {{ $order->weight ? number_format($order->weight, 2) . ' kg' : 'Not weighed yet' }}
                        </span>
                    </p>

                    <p class="mb-2">
                        <strong>Payment Method:</strong> 
                        <span class="badge bg-light text-dark border text-uppercase">
                            {{ $order->payment_method }}
                        </span>
                    </p>

                    <p class="mb-2">
                        <strong>Payment Status:</strong> 
                        @if($order->payment_status === 'paid')
                            <span class="badge bg-success">Paid</span>
                        @elseif($order->payment_status === 'failed')
                            <span class="badge bg-danger">Failed</span>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </p>

                    <p class="mb-0">
                        <strong>Scheduled Pickup:</strong> 
                        <span class="text-secondary d-block mt-1">
                            <i class="fa-regular fa-clock me-1"></i>{{ $order->pickup_time->format('M d, Y h:i A') }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Order Items Table -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4">Itemized Laundry Services</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>Service Name</th>
                            <th class="text-center">Quantity</th>
                            <th class="text-center">Weight Reference</th>
                            <th class="text-end">Unit Price</th>
                            <th class="text-end">Subtotal</th>
                            <th>Care Instructions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalQuantity = 0;
                        @endphp
                        @foreach($order->orderItems as $idx => $item)
                            @php
                                $totalQuantity += $item->quantity;
                                $subtotal = $item->quantity * $item->price;
                            @endphp
                            <tr>
                                <td class="text-center fw-medium text-secondary">{{ $idx + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $item->service ? $item->service->name : 'Laundry Service' }}</td>
                                <td class="text-center">{{ $item->quantity }}</td>
                                <td class="text-center text-muted">
                                    {{ $item->service && $item->service->price_per_kg > 0 ? ($order->weight ? number_format($order->weight, 2) . ' kg' : 'N/A') : 'N/A' }}
                                </td>
                                <td class="text-end">${{ number_format($item->price, 2) }}</td>
                                <td class="text-end fw-bold text-dark">${{ number_format($subtotal, 2) }}</td>
                                <td class="text-secondary small">{{ $item->notes ?: 'None' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light fw-bold border-top-2">
                        <tr>
                            <td colspan="2" class="text-end">Totals:</td>
                            <td class="text-center">{{ $totalQuantity }}</td>
                            <td class="text-center">{{ $order->weight ? number_format($order->weight, 2) . ' kg' : 'N/A' }}</td>
                            <td class="text-end">&mdash;</td>
                            <td class="text-end text-primary">${{ number_format($order->total_price, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Operations Col (Right) -->
    <div class="col-12 col-xl-4">
        
        <!-- Status Update Panel -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4">Update Processing Status</h5>
            
            <div class="mb-4">
                <label class="form-label text-secondary small fw-semibold">Current Process State:</label>
                <div class="d-block mt-1">
                    <span class="badge-status badge-status-{{ $order->status }} w-100 text-center py-2 fs-6 shadow-sm">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </div>
            </div>

            @php
                $nextStatusMap = [
                    'confirmed'          => ['status' => 'washing', 'label' => 'Washing', 'btn_class' => 'btn-indigo', 'icon' => 'fa-soap'],
                    'washing'            => ['status' => 'drying', 'label' => 'Drying', 'btn_class' => 'btn-purple', 'icon' => 'fa-wind'],
                    'drying'             => ['status' => 'ironing', 'label' => 'Ironing', 'btn_class' => 'btn-violet', 'icon' => 'fa-shirt'],
                    'ironing'            => ['status' => 'folding', 'label' => 'Folding', 'btn_class' => 'btn-teal', 'icon' => 'fa-box-archive'],
                    'folding'            => ['status' => 'ready_for_delivery', 'label' => 'Ready for Delivery', 'btn_class' => 'btn-success', 'icon' => 'fa-circle-check'],
                ];

                $next = $nextStatusMap[$order->status] ?? null;
            @endphp

            @if($next)
                <div class="text-center py-3 bg-light rounded-3 mb-4">
                    <span class="text-muted small">Immediate Next Step:</span>
                    <h5 class="fw-bold text-dark mt-1">{{ $next['label'] }}</h5>
                </div>

                <form method="POST" action="{{ route('staff.orders.updateStatus', $order) }}" id="status-update-form">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="{{ $next['status'] }}">
                    
                    <button type="submit" class="btn {{ $next['btn_class'] }} btn-lg w-100 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" id="btn-update-status">
                        <i class="fa-solid {{ $next['icon'] }} fs-4"></i>
                        <span>Start {{ $next['label'] }}</span>
                    </button>
                </form>
            @else
                <div class="alert alert-success border-0 shadow-sm text-center py-4 rounded-3" role="alert">
                    <i class="fa-solid fa-circle-check fs-2 text-success mb-2 d-block"></i>
                    <strong class="d-block mb-1">Awaiting Delivery</strong>
                    <span class="small text-secondary">The order has been completed and is queued for pickup by the delivery team.</span>
                </div>
            @endif
        </div>

        <!-- Status History Timeline Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4">Processing History</h5>
            
            <div class="timeline-container">
                @php
                    $timelineSteps = [
                        'confirmed'          => 'Order Confirmed',
                        'washing'            => 'Washing Process',
                        'drying'             => 'Drying Process',
                        'ironing'            => 'Ironing Process',
                        'folding'            => 'Folding Process',
                        'ready_for_delivery' => 'Ready for Delivery',
                    ];

                    $orderStates = array_keys($timelineSteps);
                    $currentIndex = array_search($order->status, $orderStates);
                @endphp

                @foreach($timelineSteps as $state => $title)
                    @php
                        $stateIndex = array_search($state, $orderStates);
                        $statusClass = '';
                        $timeInfo = '';

                        if ($stateIndex === $currentIndex) {
                            $statusClass = 'active';
                            $timeInfo = 'Status last changed: ' . $order->updated_at->format('M d, Y h:i A');
                        } elseif ($stateIndex < $currentIndex) {
                            $statusClass = 'completed';
                            $timeInfo = 'Completed Step';
                        }
                    @endphp

                    <div class="timeline-item {{ $statusClass }}">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div class="timeline-title">{{ $title }}</div>
                            @if($timeInfo)
                                <div class="timeline-time">{{ $timeInfo }}</div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const btnUpdate = document.getElementById('btn-update-status');
        const formUpdate = document.getElementById('status-update-form');

        if (btnUpdate && formUpdate) {
            formUpdate.addEventListener('submit', function (e) {
                e.preventDefault();
                
                const nextStatusLabel = "{{ $next['label'] ?? '' }}";
                const confirmMsg = `Are you sure you want to mark this order as ${nextStatusLabel}?\nThis action cannot be undone.`;

                if (confirm(confirmMsg)) {
                    // Disable the button to prevent multiple submissions
                    btnUpdate.disabled = true;
                    btnUpdate.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...`;
                    
                    formUpdate.submit();
                }
            });
        }
    });
</script>
@endsection
