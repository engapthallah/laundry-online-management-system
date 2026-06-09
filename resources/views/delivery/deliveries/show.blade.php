@extends('layouts.delivery')

@section('content')
<style>
    /* Status Badge styling */
    .badge-delivery {
        padding: 6px 14px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        display: inline-block;
    }
    .badge-delivery-assigned { background-color: #ffc107; color: #212529; }
    .badge-delivery-picked_up { background-color: #0d6efd; color: #fff; }
    .badge-delivery-on_the_way { background-color: #fd7e14; color: #fff; }
    .badge-delivery-delivered { background-color: #198754; color: #fff; }

    /* Pulsing Alert */
    @keyframes danger-pulse {
        0% { opacity: 1; }
        50% { opacity: 0.75; }
        100% { opacity: 1; }
    }
    .pulse-danger-alert {
        animation: danger-pulse 2s infinite ease-in-out;
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
    $now = now();
    $deliveryTime = $assignment->order->delivery_time;
    $diffInHours = $deliveryTime ? $now->diffInHours($deliveryTime, false) : null;
    
    // Pulse warning if scheduled delivery is within 3 hours
    $isUrgent = ($deliveryTime && $diffInHours !== null && $diffInHours >= 0 && $diffInHours <= 3);
    
    $customerFirstName = explode(' ', $assignment->order->customer->name)[0];
    $staffFirstName = $assignment->order->staff ? explode(' ', $assignment->order->staff->name)[0] : 'Laundry Staff';
@endphp

<!-- Urgency Danger Alert -->
@if($isUrgent && $assignment->status !== 'delivered')
    <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center gap-3 mb-4 pulse-danger-alert" role="alert">
        <div class="rounded-circle bg-danger bg-opacity-25 text-danger p-2 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
            <i class="fa-solid fa-triangle-exclamation fs-4"></i>
        </div>
        <div>
            <strong class="fw-bold">⚠ Urgent Delivery:</strong> This delivery is due by <span class="fw-bold">{{ $deliveryTime->format('M d, Y h:i A') }}</span> ({{ $deliveryTime->diffForHumans() }}).
        </div>
    </div>
@endif

<!-- Delivery Header -->
<div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div>
            <a href="{{ route('delivery.deliveries.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to My Deliveries
            </a>
            <h3 class="fw-bold text-dark mb-1">Order #{{ $assignment->order->order_number }}</h3>
            <p class="text-secondary mb-0">Delivery Assignment Details</p>
        </div>
        <div class="text-lg-end">
            <span class="badge-delivery badge-delivery-{{ $assignment->status }} fs-6">
                {{ str_replace('_', ' ', $assignment->status) }}
            </span>
            <div class="text-secondary small mt-2">
                <strong>Assigned:</strong> {{ $assignment->assigned_at ? $assignment->assigned_at->format('M d, Y h:i A') : 'N/A' }}
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Customer and Delivery Info -->
    <div class="col-12 col-xl-8">
        
        <!-- Delivery Address Card -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
            <div class="d-flex align-items-start gap-4">
                <div class="rounded-circle bg-danger-subtle text-danger p-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                    <i class="fa-solid fa-map-marker-alt fa-2x"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="fw-bold text-dark mb-2">Delivery Address</h5>
                    <p class="fs-5 text-dark fw-semibold mb-2" id="delivery-address-text">{{ $assignment->order->delivery_address }}</p>
                    
                    <button class="btn btn-sm btn-outline-secondary fw-semibold mb-3" onclick="copyAddressText()">
                        <i class="fa-regular fa-copy me-1"></i> Copy Address
                    </button>
                    
                    <hr class="opacity-10 my-3">
                    
                    <div class="row g-3">
                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Deliver to:</span>
                            <span class="fw-bold fs-6 text-dark">{{ $assignment->order->customer->name }}</span>
                        </div>
                        <div class="col-12 col-sm-6">
                            <span class="text-muted small d-block">Phone:</span>
                            <a href="tel:{{ $assignment->order->customer->phone }}" class="fw-bold text-decoration-none fs-6">
                                <i class="fa-solid fa-phone me-1"></i>{{ $assignment->order->customer->phone }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Contents -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
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
                        @foreach($assignment->order->orderItems as $index => $item)
                            <tr>
                                <td class="text-center fw-medium text-secondary">{{ $index + 1 }}</td>
                                <td class="fw-bold text-dark">{{ $item->service ? $item->service->name : 'Laundry Service' }}</td>
                                <td class="text-center fw-bold text-dark fs-6">{{ $item->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($assignment->order->special_instructions)
                <div class="p-3 bg-warning-subtle text-warning-emphasis border border-warning-subtle rounded-3 mt-4">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-circle-exclamation me-1"></i>Special Delivery Instructions:</h6>
                    <p class="mb-0 small text-justify">{{ $assignment->order->special_instructions }}</p>
                </div>
            @endif
        </div>

        <!-- Pickup & Payment details -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4">Pickup & Preparation Information</h5>
            <div class="row g-4">
                <div class="col-12 col-md-6">
                    <span class="text-muted small d-block mb-1">Pickup Address (Laundry Hub):</span>
                    <p class="fw-semibold text-dark mb-3"><i class="fa-solid fa-house-laptop me-2 text-secondary"></i>{{ $assignment->order->pickup_address }}</p>

                    <span class="text-muted small d-block mb-1">Prepared By:</span>
                    <p class="fw-bold text-dark mb-0"><i class="fa-solid fa-user-circle me-2 text-secondary"></i>{{ $staffFirstName }} (Laundry Staff)</p>
                </div>

                <div class="col-12 col-md-6">
                    <span class="text-muted small d-block mb-1">Payment Method:</span>
                    <p class="fw-bold text-uppercase fs-6 mb-3">
                        <i class="fa-solid fa-wallet me-2 text-secondary"></i>{{ $assignment->order->payment_method }}
                    </p>

                    <span class="text-muted small d-block mb-1">Total Amount:</span>
                    <p class="fw-bold fs-5 text-primary mb-0">${{ number_format($assignment->order->total_price, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Operations Panel -->
    <div class="col-12 col-xl-4">
        
        <!-- Delivery Status Update Panel -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 mb-4">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4">Update Delivery Status</h5>
            
            <div class="text-center py-3 bg-light rounded-3 mb-4">
                <span class="text-muted small">Current Transit State:</span>
                <div class="d-block mt-2">
                    <span class="badge-delivery badge-delivery-{{ $assignment->status }} fs-6 py-2 px-4 shadow-sm">
                        {{ str_replace('_', ' ', $assignment->status) }}
                    </span>
                </div>
            </div>

            @php
                $nextActionMap = [
                    'assigned'   => ['status' => 'picked_up', 'label' => 'Mark as Picked Up', 'btn_class' => 'btn-primary', 'icon' => 'fa-box'],
                    'picked_up'  => ['status' => 'on_the_way', 'label' => 'Mark as On The Way', 'btn_class' => 'btn-warning text-dark', 'icon' => 'fa-truck'],
                    'on_the_way' => ['status' => 'delivered', 'label' => 'Mark as Delivered', 'btn_class' => 'btn-success', 'icon' => 'fa-check-double'],
                ];

                $next = $nextActionMap[$assignment->status] ?? null;
                $isCash = ($assignment->order->payment_method === 'cash');
            @endphp

            @if($next)
                <div class="mb-4">
                    @if($next['status'] === 'delivered')
                        <div class="p-3 bg-info-subtle text-info-emphasis border border-info-subtle rounded-3 mb-3 text-center">
                            @if($isCash)
                                <h6 class="fw-bold mb-1"><i class="fa-solid fa-coins me-1"></i>Collect cash on delivery:</h6>
                                <p class="mb-0 fw-bold fs-5 text-danger">${{ number_format($assignment->order->total_price, 2) }}</p>
                            @else
                                <h6 class="fw-bold mb-1"><i class="fa-solid fa-circle-check text-success me-1"></i>Online Payment:</h6>
                                <p class="mb-0 text-success fw-bold">ALREADY PAID</p>
                            @endif
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('delivery.deliveries.updateStatus', $assignment) }}" id="status-update-form">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $next['status'] }}">
                        
                        <button type="submit" class="btn {{ $next['btn_class'] }} btn-lg w-100 py-3 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" id="btn-update-status">
                            <i class="fa-solid {{ $next['icon'] }} fs-4"></i>
                            <span>{{ $next['label'] }}</span>
                        </button>
                    </form>
                </div>
            @else
                <div class="alert alert-success border-0 shadow-sm text-center py-4 rounded-3" role="alert">
                    <i class="fa-solid fa-circle-check fs-2 text-success mb-2 d-block"></i>
                    <strong class="d-block mb-1">Transit Complete</strong>
                    <span class="small text-secondary">This order has been marked as delivered to the customer.</span>
                </div>
            @endif
        </div>

        <!-- Vertical Timeline -->
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
            <h5 class="fw-bold text-dark border-bottom pb-3 mb-4">Transit History Timeline</h5>
            
            <div class="timeline-container">
                @php
                    $timelineSteps = [
                        'assigned'   => 'Job Assigned',
                        'picked_up'  => 'Laundry Picked Up',
                        'on_the_way' => 'On the Way',
                        'delivered'  => 'Delivered',
                    ];

                    $states = array_keys($timelineSteps);
                    $currentIndex = array_search($assignment->status, $states);
                @endphp

                @foreach($timelineSteps as $state => $title)
                    @php
                        $stateIndex = array_search($state, $states);
                        $statusClass = '';
                        $timeInfo = '';

                        if ($stateIndex === $currentIndex) {
                            $statusClass = 'active';
                            if ($state === 'assigned' && $assignment->assigned_at) {
                                $timeInfo = $assignment->assigned_at->format('M d, Y h:i A');
                            } elseif ($state === 'picked_up' && $assignment->picked_up_at) {
                                $timeInfo = $assignment->picked_up_at->format('M d, Y h:i A');
                            } elseif ($state === 'on_the_way' && $assignment->updated_at) {
                                $timeInfo = $assignment->updated_at->format('M d, Y h:i A');
                            } elseif ($state === 'delivered' && $assignment->delivered_at) {
                                $timeInfo = $assignment->delivered_at->format('M d, Y h:i A');
                            }
                        } elseif ($stateIndex < $currentIndex) {
                            $statusClass = 'completed';
                            if ($state === 'assigned' && $assignment->assigned_at) {
                                $timeInfo = $assignment->assigned_at->format('M d, Y h:i A');
                            } elseif ($state === 'picked_up' && $assignment->picked_up_at) {
                                $timeInfo = $assignment->picked_up_at->format('M d, Y h:i A');
                            } elseif ($state === 'on_the_way' && $assignment->picked_up_at) {
                                // approximate or use assignment update timestamp
                                $timeInfo = $assignment->updated_at->format('M d, Y h:i A');
                            }
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
    function copyAddressText() {
        const addressText = document.getElementById('delivery-address-text').textContent;
        navigator.clipboard.writeText(addressText).then(() => {
            alert('Delivery address copied to clipboard!');
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
                
                const nextStatus = "{{ $next['status'] ?? '' }}";
                const orderNumber = "{{ $assignment->order->order_number }}";
                const totalPrice = "{{ number_format($assignment->order->total_price, 2) }}";
                const isCash = "{{ $assignment->order->payment_method === 'cash' ? '1' : '0' }}";
                
                let confirmMsg = '';
                
                if (nextStatus === 'picked_up') {
                    confirmMsg = `Confirm: Have you picked up order ${orderNumber} from the laundry?`;
                } else if (nextStatus === 'on_the_way') {
                    confirmMsg = `Confirm: Are you now on the way to deliver order ${orderNumber}?`;
                } else if (nextStatus === 'delivered') {
                    confirmMsg = `Confirm: Has order ${orderNumber} been successfully delivered to the customer?`;
                    if (isCash === '1') {
                        confirmMsg += `\n\n⚠ IMPORTANT: Have you collected $${totalPrice} in cash?`;
                    }
                }
                
                if (confirm(confirmMsg)) {
                    // Disable the button to prevent multiple clicks
                    btnUpdate.disabled = true;
                    btnUpdate.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...`;
                    
                    formUpdate.submit();
                }
            });
        }
    });
</script>
@endsection
