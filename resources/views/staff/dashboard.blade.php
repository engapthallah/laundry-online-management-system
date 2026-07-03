@extends('layouts.staff')

@section('content')
<style>
    /* Premium Hover effect for cards */
    .dashboard-card {
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08) !important;
    }

    /* Custom badge status styles */
    .badge-status {
        padding: 6px 12px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
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
</style>

<!-- Welcome Row -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm p-4 bg-white rounded-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Welcome back, {{ Auth::user()->name }}!</h2>
                    <p class="text-secondary mb-0">Monitor active queues, update laundry processes, and ensure prompt deliveries.</p>
                </div>
                <div class="bg-primary-subtle text-primary px-3 py-2 rounded-3 fw-bold">
                    <i class="fa-solid fa-user-shield me-2"></i>Laundry Specialist
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Card Row (4 Cards) -->
<div class="row g-4 mb-4">
    <!-- Card 1: Total Assigned Orders -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 dashboard-card">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle bg-primary-subtle text-primary p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-clipboard-list fs-3"></i>
                </div>
                <div>
                    <h6 class="card-subtitle text-muted mb-1 fw-semibold">Total Assigned</h6>
                    <h3 class="card-title mb-0 fw-bold text-dark">{{ $totalAssigned }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Active Orders -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 dashboard-card">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle bg-warning-subtle text-warning p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-spinner fa-spin fs-3"></i>
                </div>
                <div>
                    <h6 class="card-subtitle text-muted mb-1 fw-semibold">Active Orders</h6>
                    <h3 class="card-title mb-0 fw-bold text-dark">{{ $activeOrdersCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Ready for Delivery -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 dashboard-card">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle bg-info-subtle text-info p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-box-open fs-3 text-teal"></i>
                </div>
                <div>
                    <h6 class="card-subtitle text-muted mb-1 fw-semibold">Ready for Delivery</h6>
                    <h3 class="card-title mb-0 fw-bold text-dark">{{ $readyForDeliveryCount }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Completed Today -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-3 bg-white h-100 dashboard-card">
            <div class="card-body p-4 d-flex align-items-center">
                <div class="rounded-circle bg-success-subtle text-success p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fa-solid fa-check-circle fs-3"></i>
                </div>
                <div>
                    <h6 class="card-subtitle text-muted mb-1 fw-semibold">Completed Today</h6>
                    <h3 class="card-title mb-0 fw-bold text-dark">{{ $completedTodayCount }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Active Orders Section (Left Col) -->
    <div class="col-12 col-xl-8">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h5 class="fw-bold text-dark mb-0">
                    {{ request()->anyFilled(['search', 'status', 'date_from', 'date_to']) ? 'Filtered Assigned Orders' : 'Recent Active Orders' }}
                </h5>
                @if(!request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('staff.orders.index', ['status' => 'active']) }}" class="btn btn-sm btn-outline-primary fw-semibold">
                        View All Active
                    </a>
                @endif
            </div>

            <!-- Filters Panel -->
            <div class="mb-4 p-3 bg-light rounded-3">
                <form method="GET" action="{{ route('staff.dashboard') }}" class="row g-3">
                    <!-- Order Number Search -->
                    <div class="col-12 col-md-3">
                        <label for="search" class="form-label fw-semibold text-secondary small">Search Order No.</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                            <input type="text" class="form-control bg-white border-start-0" id="search" name="search" placeholder="e.g. LOMS-1002" value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div class="col-12 col-md-3">
                        <label for="status" class="form-label fw-semibold text-secondary small">Filter Status</label>
                        <select class="form-select bg-white" id="status" name="status">
                            <option value="">All Statuses</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Processes (Confirmed-Processing)</option>
                            <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                            <option value="ready_for_delivery" {{ request('status') === 'ready_for_delivery' ? 'selected' : '' }}>Ready for Delivery</option>
                        </select>
                    </div>

                    <!-- Date From Filter -->
                    <div class="col-12 col-md-2">
                        <label for="date_from" class="form-label fw-semibold text-secondary small">Pickup From</label>
                        <input type="date" class="form-control bg-white" id="date_from" name="date_from" value="{{ request('date_from') }}">
                    </div>

                    <!-- Date To Filter -->
                    <div class="col-12 col-md-2">
                        <label for="date_to" class="form-label fw-semibold text-secondary small">Pickup To</label>
                        <input type="date" class="form-control bg-white" id="date_to" name="date_to" value="{{ request('date_to') }}">
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-12 col-md-2 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                            <a href="{{ route('staff.dashboard') }}" class="btn btn-outline-secondary w-100 fw-semibold" title="Clear Filters">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            @if($activeOrders->isEmpty())
                <div class="text-center py-5">
                    @if(request()->anyFilled(['search', 'status', 'date_from', 'date_to']))
                        <div class="rounded-circle bg-light d-inline-flex p-4 mb-3">
                            <i class="fa-solid fa-soap text-muted" style="font-size: 3rem;"></i>
                        </div>
                        <h5 class="text-dark fw-bold mb-1">No Orders Found</h5>
                        <p class="text-secondary mb-0">No orders match your search/filter.</p>
                    @else
                        <i class="fa-solid fa-circle-check text-muted mb-3" style="font-size: 3rem;"></i>
                        <h6 class="text-secondary fw-semibold">No active orders assigned to you at the moment.</h6>
                    @endif
                </div>
            @else
                <div class="row g-3">
                    @foreach($activeOrders as $order)
                        @php
                            $now = now();
                            $pickupTime = $order->pickup_time;
                            $diffInHours = $now->diffInHours($pickupTime, false);
                            
                            // Determine urgency border and indicator badge
                            if ($pickupTime->isPast() || $diffInHours <= 2) {
                                $borderClass = 'border border-danger border-2';
                                $urgencyBadge = '<span class="badge bg-danger text-white"><i class="fa-solid fa-circle-exclamation me-1"></i>Overdue / Near Deadline</span>';
                            } elseif ($diffInHours <= 24) {
                                $borderClass = 'border border-warning border-2';
                                $urgencyBadge = '<span class="badge bg-warning text-dark"><i class="fa-solid fa-triangle-exclamation me-1"></i>Urgent (Under 24h)</span>';
                            } else {
                                $borderClass = 'border border-light';
                                $urgencyBadge = '';
                            }

                            // Gather list of distinct service names
                            $servicesList = $order->orderItems->map(function($item) {
                                return $item->service ? $item->service->name : 'Laundry Service';
                            })->unique()->implode(', ');
                        @endphp
                        
                        <div class="col-12 col-md-6">
                            <div class="card rounded-3 shadow-sm h-100 {{ $borderClass }} dashboard-card">
                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <span class="fw-bold text-primary fs-5">#{{ $order->order_number }}</span>
                                            <span class="badge-status badge-status-{{ $order->status }}">
                                                {{ str_replace('_', ' ', $order->status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            {!! $urgencyBadge !!}
                                        </div>

                                        <p class="mb-1 text-dark">
                                            <strong>Customer:</strong> {{ explode(' ', $order->customer->name)[0] }}
                                        </p>
                                        <p class="mb-2 text-muted small text-truncate">
                                            <strong>Services:</strong> {{ $servicesList ?: 'None listed' }}
                                        </p>
                                    </div>
                                    
                                    <div class="mt-2 pt-2 border-top border-light-subtle d-flex align-items-center justify-content-between">
                                        <span class="small text-secondary">
                                            <i class="fa-regular fa-calendar me-1"></i>
                                            {{ $order->pickup_time->format('M d, h:i A') }}
                                        </span>
                                        <a href="{{ route('staff.orders.show', $order) }}" class="btn btn-xs btn-primary fw-semibold px-3 py-1" style="font-size: 0.85rem;">
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

    <!-- Chart / Right Col -->
    <div class="col-12 col-xl-4">
        <div class="card border-0 shadow-sm rounded-3 bg-white p-4 h-100">
            <h5 class="fw-bold text-dark mb-3">Order Status Overview</h5>
            <p class="text-muted small mb-4">Distribution of your assigned orders by status.</p>
            
            @if(empty($chartLabels))
                <div class="text-center py-5 h-100 d-flex flex-column align-items-center justify-content-center">
                    <i class="fa-solid fa-chart-bar text-muted mb-3" style="font-size: 3rem;"></i>
                    <h6 class="text-secondary fw-semibold">No order data available for charting.</h6>
                </div>
            @else
                <div style="position: relative; height: 320px; width: 100%;">
                    <canvas id="statusChart"></canvas>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(!empty($chartLabels))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartLabels = {!! json_encode($chartLabels) !!};
        const chartData = {!! json_encode($chartData) !!};

        const ctx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    label: 'Orders',
                    data: chartData,
                    backgroundColor: 'rgba(26, 82, 118, 0.85)',
                    borderColor: 'rgba(26, 82, 118, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return ` ${context.parsed.x} Order(s)`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: {
                                family: 'Inter'
                            }
                        },
                        grid: {
                            color: '#f1f5f9'
                        }
                    },
                    y: {
                        ticks: {
                            font: {
                                family: 'Inter',
                                weight: 'bold'
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endif
@endsection
