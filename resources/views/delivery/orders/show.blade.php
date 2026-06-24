@extends('layouts.delivery')

@section('content')
<style>
    /* Custom status badge styling */
    .badge-status {
        padding: 6px 14px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        display: inline-block;
    }
    .badge-status-pending_pickup { background-color: #6c757d; color: #fff; }
    .badge-status-picked_up_from_customer { background-color: #0d6efd; color: #fff; }
    .badge-status-delivered_to_laundry { background-color: #0dcaf0; color: #212529; }
    .badge-status-processing { background-color: #ffc107; color: #212529; }
    .badge-status-ready_for_delivery { background-color: #20c997; color: #fff; }
    .badge-status-picked_up_from_laundry { background-color: #0d6efd; color: #fff; }
    .badge-status-on_the_way { background-color: #212529; color: #fff; }
    .badge-status-delivered { background-color: #198754; color: #fff; }
    .badge-status-cancelled { background-color: #dc3545; color: #fff; }

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
        border-color: #fd7e14;
        background-color: #fff;
        box-shadow: 0 0 0 3px rgba(253, 126, 20, 0.25);
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
        color: #fd7e14;
    }
    .timeline-time {
        font-size: 0.8rem;
        color: #6c757d;
    }
</style>

@php
    $nextActionMap = [
        'pending_pickup'          => ['status' => 'picked_up_from_customer', 'label' => 'Picked Up from Customer', 'btn_class' => 'btn-primary', 'icon' => 'fa-box', 'confirm' => 'Confirm: Have you collected the laundry from the customer?'],
        'picked_up_from_customer' => ['status' => 'delivered_to_laundry', 'label' => 'Delivered to Laundry', 'btn_class' => 'btn-info text-dark', 'icon' => 'fa-store', 'confirm' => 'Confirm: Have you delivered this order to the laundry shop? It will auto-advance to processing.'],
        'ready_for_delivery'      => ['status' => 'picked_up_from_laundry', 'label' => 'Picked Up from Laundry', 'btn_class' => 'btn-primary', 'icon' => 'fa-dolly', 'confirm' => 'Confirm: Have you picked up this order from the laundry shop?'],
        'picked_up_from_laundry'  => ['status' => 'on_the_way', 'label' => 'On the Way', 'btn_class' => 'btn-dark', 'icon' => 'fa-truck', 'confirm' => 'Confirm: Are you now on the way to deliver this order?'],
        'on_the_way'              => ['status' => 'delivered', 'label' => 'Mark Delivered', 'btn_class' => 'btn-success', 'icon' => 'fa-check-double', 'confirm' => 'Confirm: Has this order been successfully delivered to the customer?'],
    ];

    $next = $nextActionMap[$order->status] ?? null;
    $isCash = ($order->payment_method === 'cash');
@endphp

<!-- Delivery Header -->
<div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <a href="{{ route('delivery.orders.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to My Jobs
            </a>
            <h3 class="fw-bold text-dark mb-1">Order #{{ $order->order_number }}</h3>
            <p class="text-secondary mb-0">Delivery Details & Progression</p>
        </div>
        <div class="text-lg-end">
            <span class="badge-status badge-status-{{ $order->status }} fs-6">
                {{ str_replace('_', ' ', $order->status) }}
            </span>
            <div class="text-secondary small mt-2">
                <strong>Placed at:</strong> {{ $order->created_at->format('M d, Y h:i A') }}
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Customer and Addresses -->
    <div class="col-12 col-xl-8">
        
        <!-- Pickup Address Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
            <div class="d-flex align-items-start gap-4">
                <div class="rounded-circle bg-light text-secondary p-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-house-user fa-2x"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="fw-bold text-dark mb-2">Customer Pickup Address</h5>
                    <p class="fs-5 text-dark fw-semibold mb-2" id="pickup-address-text">{{ $order->pickup_address }}</p>
                    <button class="btn btn-sm btn-outline-secondary fw-semibold" onclick="copyAddressText('pickup-address-text')">
                        <i class="fa-regular fa-copy me-1"></i> Copy Pickup Address
                    </button>
                </div>
            </div>
        </div>

        <!-- Delivery Address Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
            <div class="d-flex align-items-start gap-4">
                <div class="rounded-circle bg-light text-primary p-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-map-marker-alt fa-2x"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="fw-bold text-dark mb-2">Customer Delivery Address</h5>
                    <p class="fs-5 text-dark fw-semibold mb-2" id="delivery-address-text">{{ $order->delivery_address }}</p>
                    <button class="btn btn-sm btn-outline-secondary fw-semibold" onclick="copyAddressText('delivery-address-text')">
                        <i class="fa-regular fa-copy me-1"></i> Copy Delivery Address
                    </button>
                </div>
            </div>
        </div>

        <!-- Customer Profile Summary -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4">Customer Details</h5>
            <div class="row g-3">
                <div class="col-12 col-sm-6">
                    <span class="text-muted small d-block">Customer Name:</span>
                    <span class="fw-bold fs-6 text-dark">{{ $order->customer->name ?? 'N/A' }}</span>
                </div>
                <div class="col-12 col-sm-6">
                    <span class="text-muted small d-block">Phone Number:</span>
                    <a href="tel:{{ $order->customer->phone ?? '' }}" class="fw-bold text-decoration-none fs-6">
                        <i class="fa-solid fa-phone me-1"></i>{{ $order->customer->phone ?? 'N/A' }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4">Order Contents</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;" class="text-center">#</th>
                            <th>Service Name</th>
                            <th class="text-center" style="width: 150px;">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $index => $item)
                            <tr>
                                <td class="text-center fw-medium text-secondary">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $item->service ? $item->service->name : 'Laundry Service' }}</td>
                                <td class="text-center fw-bold text-dark fs-6">{{ $item->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($order->special_instructions)
                <div class="p-3 bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-3 mt-4">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-circle-exclamation me-1"></i>Special Instructions:</h6>
                    <p class="mb-0 small">{{ $order->special_instructions }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Actions and Timeline -->
    <div class="col-12 col-xl-4">
        
        <!-- Actions Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4">Workflow Progress</h5>
            
            <div class="text-center py-3 bg-light rounded-3 mb-4">
                <span class="text-muted small">Current State:</span>
                <div class="d-block mt-2">
                    <span class="badge-status badge-status-{{ $order->status }} fs-6 py-2 px-4 shadow-sm">
                        {{ str_replace('_', ' ', $order->status) }}
                    </span>
                </div>
            </div>

            @if($next)
                <div class="mb-4">
                    @if($next['status'] === 'delivered')
                        <div class="p-3 bg-info-subtle text-info-emphasis border border-info-subtle rounded-3 mb-3 text-center">
                            @if($isCash)
                                <h6 class="fw-bold mb-1"><i class="fa-solid fa-coins me-1"></i>Collect cash on delivery:</h6>
                                <p class="mb-0 fw-bold fs-5 text-danger">${{ number_format($order->total_price, 2) }}</p>
                            @else
                                <h6 class="fw-bold mb-1"><i class="fa-solid fa-circle-check text-success me-1"></i>Online Payment:</h6>
                                <p class="mb-0 text-success fw-bold">ALREADY PAID</p>
                            @endif
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('delivery.orders.updateStatus', $order) }}" id="status-update-form">
                        @csrf
                        @method('PATCH')
                        
                        <button type="submit" class="btn {{ $next['btn_class'] }} btn-lg w-100 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" id="btn-update-status">
                            <i class="fa-solid {{ $next['icon'] }} fs-4"></i>
                            <span>{{ $next['label'] }}</span>
                        </button>
                    </form>
                </div>
            @elseif($order->status === 'processing')
                <div class="alert alert-warning border-0 shadow-sm text-center py-4 rounded-3" role="alert">
                    <i class="fa-solid fa-spinner fa-spin fs-2 text-warning mb-2 d-block"></i>
                    <strong class="d-block mb-1">Processing in Shop</strong>
                    <span class="small text-secondary">Laundry is currently being processed by the laundry staff. Wait until they finish and mark it ready.</span>
                </div>
            @elseif($order->status === 'delivered')
                <div class="alert alert-success border-0 shadow-sm text-center py-4 rounded-3" role="alert">
                    <i class="fa-solid fa-circle-check fs-2 text-success mb-2 d-block"></i>
                    <strong class="d-block mb-1">Transit Complete</strong>
                    <span class="small text-secondary">This order has been marked as delivered to the customer.</span>
                </div>
            @else
                <div class="alert alert-info border-0 shadow-sm text-center py-4 rounded-3" role="alert">
                    <i class="fa-solid fa-circle-info fs-2 text-info mb-2 d-block"></i>
                    <strong class="d-block mb-1">Awaiting shop progression</strong>
                    <span class="small text-secondary">Status: {{ str_replace('_', ' ', $order->status) }}</span>
                </div>
            @endif
        </div>

        <!-- Vertical Timeline -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4">Complete Lifecycle</h5>
            
            <div class="timeline-container">
                @php
                    $timelineSteps = [
                        'pending_pickup'          => 'Waiting for Pickup',
                        'picked_up_from_customer' => 'Collected from Customer',
                        'delivered_to_laundry'    => 'Delivered to Shop',
                        'processing'              => 'Washing & Processing',
                        'ready_for_delivery'      => 'Ready to Return',
                        'picked_up_from_laundry'  => 'Collected from Shop',
                        'on_the_way'              => 'On the Way',
                        'delivered'               => 'Delivered to Customer',
                    ];

                    $states = array_keys($timelineSteps);
                    $currentIndex = array_search($order->status, $states);
                @endphp

                @foreach($timelineSteps as $state => $title)
                    @php
                        $stateIndex = array_search($state, $states);
                        $statusClass = '';
                        $timeInfo = '';

                        if ($stateIndex === $currentIndex) {
                            $statusClass = 'active';
                            $timeInfo = 'Current Stage';
                        } elseif ($stateIndex < $currentIndex) {
                            $statusClass = 'completed';
                            $timeInfo = 'Completed';
                        } else {
                            $timeInfo = 'Pending';
                        }
                    @endphp

                    <div class="timeline-item {{ $statusClass }}">
                        <div class="timeline-dot"></div>
                        <div class="timeline-content">
                            <div class="timeline-title">{{ $title }}</div>
                            <div class="timeline-time">{{ $timeInfo }}</div>
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
    function copyAddressText(id) {
        const addressText = document.getElementById(id).textContent;
        navigator.clipboard.writeText(addressText).then(() => {
            alert('Address copied to clipboard!');
        }).catch(err => {
            console.error('Failed to copy: ', err);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const btnUpdate = document.getElementById('btn-update-status');
        const formUpdate = document.getElementById('status-update-form');

        if (btnUpdate && formUpdate) {
            formUpdate.addEventListener('submit', function (e) {
                e.preventDefault();
                
                const confirmMsg = "{{ $next['confirm'] ?? 'Confirm status update?' }}";
                
                if (confirm(confirmMsg)) {
                    btnUpdate.disabled = true;
                    btnUpdate.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...`;
                    formUpdate.submit();
                }
            });
        }
    });
</script>
@endsection
