@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
    <h1 class="h2 fw-bold text-dark">Analytics Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0 gap-2">
        <a href="{{ route('reviews.public') }}" target="_blank" class="btn btn-sm btn-outline-primary fw-semibold d-flex align-items-center">
            <i class="fa-solid fa-arrow-up-right-from-square me-2"></i>View Public Reviews
        </a>
        <span class="badge bg-primary px-3 py-2 text-uppercase fs-7 fw-semibold d-flex align-items-center">Real-Time Stats</span>
    </div>
</div>

<!-- Row 1: Key Performance Indicators -->
<div class="row g-4 mb-4">
    <!-- Total Orders -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-2">Total Orders</h6>
                    <h2 class="fw-bold mb-0 text-dark">{{ $totalOrders }}</h2>
                </div>
                <div class="rounded-4 bg-primary-subtle text-primary p-3">
                    <i class="fa-solid fa-shopping-bag fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-2">Total Revenue</h6>
                    <h2 class="fw-bold mb-0 text-success">${{ number_format($totalRevenue, 2) }}</h2>
                </div>
                <div class="rounded-4 bg-success-subtle text-success p-3">
                    <i class="fa-solid fa-dollar-sign fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Customers -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-2">Active Customers</h6>
                    <h2 class="fw-bold mb-0 text-info">{{ $activeCustomers }}</h2>
                </div>
                <div class="rounded-4 bg-info-subtle text-info p-3">
                    <i class="fa-solid fa-users fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Orders -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-2">Pending Orders</h6>
                    <h2 class="fw-bold mb-0 text-warning">{{ $pendingOrders }}</h2>
                </div>
                <div class="rounded-4 bg-warning-subtle text-warning p-3">
                    <i class="fa-solid fa-clock fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Secondary Indicators -->
<div class="row g-4 mb-4">
    <!-- Orders Today -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-2">Orders Today</h6>
                    <h2 class="fw-bold mb-0 text-dark">{{ $ordersToday }}</h2>
                </div>
                <div class="rounded-4 bg-secondary-subtle text-secondary p-3">
                    <i class="fa-solid fa-calendar-day fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue This Month -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-2">This Month's Rev</h6>
                    <h2 class="fw-bold mb-0 text-success">${{ number_format($revenueThisMonth, 2) }}</h2>
                </div>
                <div class="rounded-4 bg-success-subtle text-success p-3">
                    <i class="fa-solid fa-chart-line fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Open Support Messages -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-2">Open Messages</h6>
                    <h2 class="fw-bold mb-0 text-danger">{{ $openSupportMessages }}</h2>
                </div>
                <div class="rounded-4 bg-danger-subtle text-danger p-3">
                    <i class="fa-solid fa-envelope-open-text fs-3"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Average Rating -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body p-4 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="text-muted text-uppercase fw-semibold small mb-2">Average Rating</h6>
                    <h2 class="fw-bold mb-0 text-warning">{{ $averageRating }} <i class="fa-solid fa-star fs-6 text-warning"></i></h2>
                </div>
                <div class="rounded-4 bg-warning-subtle text-warning p-3">
                    <i class="fa-solid fa-star fs-3"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="row g-4 mb-4">
    <!-- Orders by Status (Horizontal Bar Chart) -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Orders by Status</h5>
            </div>
            <div class="card-body p-4">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Revenue Last 7 Days (Line Chart) -->
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold text-dark mb-0">Revenue (Last 7 Days)</h5>
            </div>
            <div class="card-body p-4">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Section -->
<div class="row g-4">
    <!-- Latest 5 Orders -->
    <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold text-dark mb-0">Latest Orders</h5>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-light fw-semibold text-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 px-4">Order No</th>
                                <th class="border-0">Customer</th>
                                <th class="border-0">Status</th>
                                <th class="border-0">Total</th>
                                <th class="border-0 px-4">Created At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestOrders as $order)
                                <tr>
                                    <td class="px-4 fw-medium text-primary">#{{ $order->order_number }}</td>
                                    <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                    <td>
                                        @switch($order->status)
                                            @case('pending')
                                                <span class="badge bg-warning text-dark text-capitalize">{{ $order->status }}</span>
                                                @break
                                            @case('confirmed')
                                                <span class="badge bg-primary text-capitalize">{{ $order->status }}</span>
                                                @break
                                            @case('washing')
                                            @case('drying')
                                            @case('ironing')
                                            @case('folding')
                                                <span class="badge bg-info text-dark text-capitalize">{{ $order->status }}</span>
                                                @break
                                            @case('ready_for_delivery')
                                                <span class="badge bg-teal text-white text-capitalize">Ready</span>
                                                @break
                                            @case('out_for_delivery')
                                                <span class="badge bg-orange text-white text-capitalize">Out</span>
                                                @break
                                            @case('delivered')
                                                <span class="badge bg-success text-capitalize">{{ $order->status }}</span>
                                                @break
                                            @case('cancelled')
                                                <span class="badge bg-danger text-capitalize">{{ $order->status }}</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary text-capitalize">{{ $order->status }}</span>
                                        @endswitch
                                    </td>
                                    <td class="fw-semibold">${{ number_format($order->total_price, 2) }}</td>
                                    <td class="text-muted px-4 small">{{ $order->created_at->format('M d, g:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-box-open fs-3 mb-2 d-block text-secondary"></i>
                                        No recent orders found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Latest 5 Support Messages -->
    <div class="col-12 col-xl-6">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                <h5 class="fw-bold text-dark mb-0">Latest Support Tickets</h5>
                <a href="{{ route('admin.support.index') }}" class="btn btn-sm btn-light fw-semibold text-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="border-0 px-4">From</th>
                                <th class="border-0">Subject</th>
                                <th class="border-0">Status</th>
                                <th class="border-0 px-4">Submitted At</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestSupportMessages as $message)
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-medium text-dark">{{ $message->name }}</div>
                                        <div class="small text-muted">{{ $message->email }}</div>
                                    </td>
                                    <td class="text-truncate">{{ Str::limit($message->subject, 30) }}</td>
                                    <td>
                                        @if($message->status === 'pending')
                                            <span class="badge bg-danger">Pending</span>
                                        @elseif($message->status === 'resolved')
                                            <span class="badge bg-success">Resolved</span>
                                        @else
                                            <span class="badge bg-secondary">Ignored</span>
                                        @endif
                                    </td>
                                    <td class="text-muted px-4 small">{{ $message->created_at->format('M d, g:i A') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="fa-solid fa-envelope-open fs-3 mb-2 d-block text-secondary"></i>
                                        No recent support messages.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data passed from controller
        const ordersByStatusData = @json($ordersByStatusData);
        const revenueLast7DaysData = @json($revenueLast7DaysData);
        
        // Status Bar Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(ordersByStatusData).map(s => s.replace(/_/g, ' ').toUpperCase()),
                datasets: [{
                    label: 'Total Orders',
                    data: Object.values(ordersByStatusData),
                    backgroundColor: 'rgba(13, 110, 253, 0.25)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1.5,
                    borderRadius: 4
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                aspectRatio: 1.6,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: {
                            color: '#e9ecef'
                        },
                        ticks: {
                            precision: 0
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Revenue Line Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: Object.keys(revenueLast7DaysData),
                datasets: [{
                    label: 'Daily Revenue ($)',
                    data: Object.values(revenueLast7DaysData),
                    fill: true,
                    backgroundColor: 'rgba(25, 135, 84, 0.08)',
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 2,
                    tension: 0.35,
                    pointBackgroundColor: 'rgba(25, 135, 84, 1)',
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                aspectRatio: 1.6,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: '#e9ecef'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
