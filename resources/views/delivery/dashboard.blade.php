@extends('layouts.delivery')

@section('content')
<style>
    /* Styling for pulsing badges */
    @keyframes pulse {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        70% { transform: scale(1.04); box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }
    .pulse-badge {
        animation: pulse 1.6s infinite;
    }

    /* Premium card transitions */
    .dashboard-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
    }

    /* Custom status badge colors */
    .badge-delivery {
        padding: 5px 12px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.78rem;
        text-transform: uppercase;
        display: inline-block;
    }
    .badge-delivery-assigned { background-color: #ffc107; color: #212529; }
    .badge-delivery-picked_up { background-color: #0d6efd; color: #fff; }
    .badge-delivery-on_the_way { background-color: #fd7e14; color: #fff; }
    .badge-delivery-delivered { background-color: #198754; color: #fff; }

    /* Custom trophy/gold color for stats */
    .text-gold {
        color: #d4af37 !important;
    }
    .text-teal {
        color: #20c997 !important;
    }
    .bg-teal-subtle {
        background-color: rgba(32, 201, 151, 0.1) !important;
    }

    /* Timeline schedule style */
    .timeline-schedule {
        position: relative;
        padding-left: 10px;
    }
    .timeline-schedule-item {
        position: relative;
        border-left: 2px solid #dee2e6;
        padding-left: 25px;
        padding-bottom: 25px;
    }
    .timeline-schedule-item:last-child {
        border-left: 2px solid transparent;
        padding-bottom: 0;
    }
    .timeline-schedule-marker {
        position: absolute;
        left: -8px;
        top: 4px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background-color: #d35400;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px rgba(211, 84, 0, 0.25);
    }
    .timeline-schedule-item.completed .timeline-schedule-marker {
        background-color: #198754;
        box-shadow: 0 0 0 2px rgba(25, 135, 84, 0.25);
    }
</style>

<!-- Welcome Banner -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Transit Control Room</h2>
                    <p class="text-secondary mb-0">Optimize active pickup and dropoff assignments, monitor deadlines, and coordinate schedules.</p>
                </div>
                <div class="bg-warning-subtle text-warning-emphasis px-3 py-2 rounded-3 fw-bold">
                    <i class="fa-solid fa-truck-ramp-box me-2"></i>Active Logistics Agent
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Card Row (4 cards) -->
<div class="row g-4 mb-4">
    <!-- Card 1: Total Deliveries (All Time) -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 dashboard-card">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-truck fs-3"></i>
                </div>
                <div>
                    <h6 class="card-subtitle text-muted mb-1 fw-semibold">Total Deliveries</h6>
                    <h3 class="card-title mb-0 fw-bold text-dark">{{ $totalAssigned }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Active Deliveries -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 dashboard-card">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle bg-warning-subtle text-warning p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-spinner fa-spin fs-3 text-warning"></i>
                </div>
                <div>
                    <h6 class="card-subtitle text-muted mb-1 fw-semibold">Active Deliveries</h6>
                    <h3 class="card-title mb-0 fw-bold text-dark">{{ $activeDeliveriesCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Delivered Today -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 dashboard-card">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle bg-success-subtle text-success p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-check-circle fs-3"></i>
                </div>
                <div>
                    <h6 class="card-subtitle text-muted mb-1 fw-semibold">Delivered Today</h6>
                    <h3 class="card-title mb-0 fw-bold text-dark">{{ $deliveredTodayCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Total Deliveries This Month -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 dashboard-card">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle bg-teal-subtle text-teal p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-calendar-check fs-3"></i>
                </div>
                <div>
                    <h6 class="card-subtitle text-muted mb-1 fw-semibold">Deliveries This Month</h6>
                    <h3 class="card-title mb-0 fw-bold text-dark">{{ $deliveriesThisMonthCount }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Active Deliveries cards (Left Col) -->
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="fw-bold text-dark mb-0">Recent Active Deliveries</h5>
                <a href="{{ route('delivery.deliveries.index', ['status' => 'active']) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                    View All Active
                </a>
            </div>

            @if($activeAssignments->isEmpty())
                <div class="text-center py-5">
                    <i class="fa-solid fa-truck text-muted mb-3" style="font-size: 3rem;"></i>
                    <h6 class="text-secondary fw-semibold">No active deliveries currently on your list.</h6>
                </div>
            @else
                <div class="row g-3">
                    @foreach($activeAssignments as $assignment)
                        @php
                            $now = now();
                            $deliveryTime = $assignment->order->delivery_time;
                            $diffInMinutes = $deliveryTime ? $now->diffInMinutes($deliveryTime, false) : null;
                            
                            // Determine urgency border and indicator badge
                            if ($deliveryTime && ($deliveryTime->isPast() || $diffInMinutes <= 60)) {
                                $borderClass = 'border border-danger border-2';
                                $urgencyBadge = '<span class="badge bg-danger text-white pulse-badge"><i class="fa-solid fa-bell me-1"></i>Due under 1 hour</span>';
                            } elseif ($deliveryTime && $diffInMinutes <= 180) {
                                $borderClass = 'border border-warning border-2';
                                $urgencyBadge = '<span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i>Due under 3 hours</span>';
                            } else {
                                $borderClass = 'border border-light';
                                $urgencyBadge = '';
                            }
                        @endphp
                        
                        <div class="col-12 col-md-6">
                            <div class="card rounded-3 shadow-sm h-100 {{ $borderClass }} dashboard-card">
                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-primary fs-5">#{{ $assignment->order->order_number }}</span>
                                            <span class="badge-delivery badge-delivery-{{ $assignment->status }}">
                                                {{ str_replace('_', ' ', $assignment->status) }}
                                            </span>
                                        </div>
                                        
                                        @if($urgencyBadge)
                                            <div class="mb-3">
                                                {!! $urgencyBadge !!}
                                            </div>
                                        @endif

                                        <p class="mb-1 text-dark">
                                            <strong>Customer:</strong> {{ explode(' ', $assignment->order->customer->name)[0] }}
                                        </p>
                                        <p class="mb-1 text-secondary small">
                                            <strong>Address:</strong> {{ Str::limit($assignment->order->delivery_address, 60) }}
                                        </p>
                                        <p class="mb-1 text-secondary small">
                                            <strong>Schedule:</strong> {{ $assignment->order->delivery_time ? $assignment->order->delivery_time->format('M d, Y h:i A') : 'N/A' }}
                                        </p>
                                    </div>
                                    
                                    <div class="mt-3 pt-2 border-top border-light-subtle d-flex flex-column gap-2">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <span class="small text-secondary fw-semibold">
                                                Total: ${{ number_format($assignment->order->total_price, 2) }}
                                            </span>
                                            @if($assignment->order->payment_method === 'cash')
                                                <span class="badge bg-danger">Collect Cash</span>
                                            @else
                                                <span class="badge bg-success">Paid</span>
                                            @endif
                                        </div>
                                        <a href="{{ route('delivery.deliveries.show', $assignment) }}" class="btn btn-primary w-100 fw-bold py-2 mt-2">
                                            Update Status <i class="fa-solid fa-arrow-right ms-1"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Doughnut Chart (Right Col) -->
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100 d-flex flex-column justify-content-between">
            <div>
                <h5 class="fw-bold text-dark mb-1">Performance Summary</h5>
                <p class="text-muted small mb-4">Breakdown of assignments by status this month.</p>
            </div>
            
            @if(!$hasData)
                <div class="text-center py-5 my-auto">
                    <i class="fa-solid fa-chart-pie text-muted mb-3" style="font-size: 3rem;"></i>
                    <h6 class="text-secondary fw-semibold">No performance metrics recorded for this month.</h6>
                </div>
            @else
                <div class="my-auto d-flex flex-column align-items-center">
                    <div style="position: relative; height: 180px; width: 180px; margin-bottom: 20px;">
                        <canvas id="performanceChart"></canvas>
                        <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; pointer-events: none;">
                            <span class="d-block fw-bold fs-4 text-dark">{{ array_sum($chartData) }}</span>
                            <span class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">Total</span>
                        </div>
                    </div>
                    
                    <!-- Legends list with counts next to chart -->
                    <div class="w-100 border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fa-solid fa-circle text-warning me-2" style="font-size: 0.8rem;"></i>Assigned</span>
                            <span class="fw-bold">{{ $chartData['assigned'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fa-solid fa-circle text-primary me-2" style="font-size: 0.8rem;"></i>Picked Up</span>
                            <span class="fw-bold">{{ $chartData['picked_up'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fa-solid fa-circle text-orange me-2" style="font-size: 0.8rem;"></i>On The Way</span>
                            <span class="fw-bold">{{ $chartData['on_the_way'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-circle text-success me-2" style="font-size: 0.8rem;"></i>Delivered</span>
                            <span class="fw-bold">{{ $chartData['delivered'] }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Today's Schedule Section -->
<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4">
            <h5 class="fw-bold text-dark mb-4"><i class="fa-regular fa-calendar-days text-primary me-2"></i>Today's Transit Schedule</h5>
            
            @if($todaysSchedule->isEmpty())
                <div class="text-center py-4">
                    <i class="fa-regular fa-calendar-xmark text-muted mb-2 fs-2"></i>
                    <p class="text-secondary mb-0">No deliveries are scheduled/assigned for today.</p>
                </div>
            @else
                <div class="timeline-schedule">
                    @foreach($todaysSchedule as $assignment)
                        @php
                            $isDelivered = ($assignment->status === 'delivered');
                        @endphp
                        <div class="timeline-schedule-item d-flex flex-wrap align-items-start {{ $isDelivered ? 'completed' : '' }}">
                            <div class="timeline-schedule-marker"></div>
                            
                            <!-- Content -->
                            <div class="row w-100 g-2">
                                <div class="col-12 col-md-3">
                                    <div class="fw-bold text-dark">
                                        <i class="fa-regular fa-clock me-1 text-muted"></i>
                                        {{ $assignment->order->delivery_time ? $assignment->order->delivery_time->format('h:i A') : 'N/A' }}
                                    </div>
                                    <span class="badge-delivery badge-delivery-{{ $assignment->status }} mt-1">
                                        {{ str_replace('_', ' ', $assignment->status) }}
                                    </span>
                                </div>
                                <div class="col-12 col-md-3">
                                    <span class="fw-bold text-primary">#{{ $assignment->order->order_number }}</span>
                                    <div class="small text-secondary mt-1">
                                        Customer: <strong>{{ explode(' ', $assignment->order->customer->name)[0] }}</strong>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 text-muted small text-truncate-custom">
                                    <i class="fa-solid fa-map-marker-alt text-danger me-1"></i>
                                    {{ $assignment->order->delivery_address }}
                                </div>
                                <div class="col-12 col-md-2 text-md-end">
                                    <a href="{{ route('delivery.deliveries.show', $assignment) }}" class="btn btn-sm btn-outline-primary fw-semibold px-3">
                                        Open Assignment
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($hasData)
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartData = {!! json_encode(array_values($chartData)) !!};

        const ctx = document.getElementById('performanceChart').getContext('2d');
        const performanceChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Assigned', 'Picked Up', 'On The Way', 'Delivered'],
                datasets: [{
                    data: chartData,
                    backgroundColor: [
                        '#ffc107', // Assigned (warning)
                        '#0d6efd', // Picked Up (primary)
                        '#fd7e14', // On The Way (orange)
                        '#198754'  // Delivered (success)
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ` ${context.label}: ${context.raw} Job(s)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endsection
