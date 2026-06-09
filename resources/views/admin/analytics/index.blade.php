@extends('layouts.admin')

@section('content')
<style>
    .kpi-card {
        border-top-width: 4px !important;
        transition: transform 0.2s, box-shadow 0.2s;
    }
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15) !important;
    }
    .min-vh-250 {
        min-height: 250px;
    }
</style>

@php
    $renderTrend = function($trend) {
        if ($trend['direction'] === 'up') {
            return '<span class="text-success fw-bold"><i class="fas fa-arrow-up me-1"></i>+' . number_format($trend['percentage'], 1) . '%</span> <span class="text-muted small">vs last period</span>';
        } elseif ($trend['direction'] === 'down') {
            return '<span class="text-danger fw-bold"><i class="fas fa-arrow-down me-1"></i>-' . number_format($trend['percentage'], 1) . '%</span> <span class="text-muted small">vs last period</span>';
        } else {
            return '<span class="text-muted fw-bold"><i class="fas fa-minus me-1"></i>Same</span> <span class="text-muted small">vs last period</span>';
        }
    };
    $kpi = $data['kpi'];
@endphp

<!-- Header Row -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Analytics Dashboard</h1>
        <small class="text-muted">Showing data for: <strong class="text-dark">{{ $start->format('M d, Y') }}</strong> &mdash; <strong class="text-dark">{{ $end->format('M d, Y') }}</strong></small>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.analytics.index', array_merge(request()->query(), ['refresh' => 1])) }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-1">
            <i class="fas fa-sync-alt"></i> Refresh Data
        </a>
        <a href="{{ route('admin.analytics.export.csv', request()->query()) }}" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1">
            <i class="fas fa-file-csv"></i> Export CSV
        </a>
        <a href="{{ route('admin.analytics.export.pdf', request()->query()) }}" target="_blank" class="btn btn-outline-danger btn-sm d-flex align-items-center gap-1">
            <i class="fas fa-file-pdf"></i> Print Report
        </a>
    </div>
</div>

<!-- Date Range Filter Bar -->
<div class="card bg-light border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="row align-items-center g-3">
            <!-- Left Side: Quick Buttons -->
            <div class="col-xl-7 col-lg-12">
                <div class="btn-group w-100 flex-wrap" role="group" aria-label="Period Selection">
                    <a href="{{ route('admin.analytics.index', ['period' => 'today']) }}" class="btn {{ $period === 'today' ? 'btn-primary' : 'btn-outline-secondary' }}">Today</a>
                    <a href="{{ route('admin.analytics.index', ['period' => 'last7days']) }}" class="btn {{ $period === 'last7days' ? 'btn-primary' : 'btn-outline-secondary' }}">Last 7 Days</a>
                    <a href="{{ route('admin.analytics.index', ['period' => 'last30days']) }}" class="btn {{ $period === 'last30days' ? 'btn-primary' : 'btn-outline-secondary' }}">Last 30 Days</a>
                    <a href="{{ route('admin.analytics.index', ['period' => 'thismonth']) }}" class="btn {{ $period === 'thismonth' ? 'btn-primary' : 'btn-outline-secondary' }}">This Month</a>
                    <a href="{{ route('admin.analytics.index', ['period' => 'lastmonth']) }}" class="btn {{ $period === 'lastmonth' ? 'btn-primary' : 'btn-outline-secondary' }}">Last Month</a>
                    <a href="{{ route('admin.analytics.index', ['period' => 'thisyear']) }}" class="btn {{ $period === 'thisyear' ? 'btn-primary' : 'btn-outline-secondary' }}">This Year</a>
                </div>
            </div>
            
            <!-- Right Side: Custom Date Range Form -->
            <div class="col-xl-5 col-lg-12">
                <form action="{{ route('admin.analytics.index') }}" method="GET" class="row g-2 align-items-center justify-content-xl-end">
                    <input type="hidden" name="period" value="custom">
                    <div class="col-auto">
                        <label for="from" class="col-form-label col-form-label-sm fw-medium">From:</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" id="from" name="from" value="{{ $from ?? $start->toDateString() }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-auto">
                        <label for="to" class="col-form-label col-form-label-sm fw-medium">To:</label>
                    </div>
                    <div class="col-auto">
                        <input type="date" id="to" name="to" value="{{ $to ?? $end->toDateString() }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-success btn-sm px-3"><i class="fas fa-filter me-1"></i>Apply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- ROW 1 — PRIMARY KPI CARDS -->
<div class="row g-3 mb-4">
    <!-- Card 1: Total Revenue -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 shadow-sm kpi-card border-top border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase mb-0 small fw-bold">Total Revenue</h6>
                    <i class="fas fa-dollar-sign fa-2x text-success opacity-50"></i>
                </div>
                <h2 class="mb-1 font-weight-bold fw-bold text-dark">${{ number_format($kpi['total_revenue'], 2) }}</h2>
                <div class="text-muted small mb-2">Completed payments in period</div>
                <div>
                    {!! $renderTrend($kpi['trends']['total_revenue']) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Total Orders -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 shadow-sm kpi-card border-top border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase mb-0 small fw-bold">Total Orders</h6>
                    <i class="fas fa-shopping-bag fa-2x text-primary opacity-50"></i>
                </div>
                <h2 class="mb-1 font-weight-bold fw-bold text-dark">{{ $kpi['total_orders'] }}</h2>
                <div class="text-muted small mb-2">Orders placed in period</div>
                <div>
                    {!! $renderTrend($kpi['trends']['total_orders']) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: New Customers -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 shadow-sm kpi-card border-top border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase mb-0 small fw-bold">New Customers</h6>
                    <i class="fas fa-user-plus fa-2x text-info opacity-50"></i>
                </div>
                <h2 class="mb-1 font-weight-bold fw-bold text-dark">{{ $kpi['new_customers'] }}</h2>
                <div class="text-muted small mb-2">Customer registrations in period</div>
                <div>
                    {!! $renderTrend($kpi['trends']['new_customers']) !!}
                </div>
            </div>
        </div>
    </div>

    <!-- Card 4: Delivered Orders -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 shadow-sm kpi-card border-top border-warning" style="border-top-color: #fd7e14 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase mb-0 small fw-bold">Delivered Orders</h6>
                    <i class="fas fa-check-circle fa-2x text-warning opacity-50" style="color: #fd7e14 !important;"></i>
                </div>
                <h2 class="mb-1 font-weight-bold fw-bold text-dark">{{ $kpi['delivered_orders'] }}</h2>
                <div class="text-muted small mb-2">Successfully delivered in period</div>
                <div>
                    {!! $renderTrend($kpi['trends']['delivered_orders']) !!}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ROW 2 — SECONDARY KPI CARDS -->
<div class="row g-3 mb-4">
    <!-- Card 5: Pending Orders -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 shadow-sm kpi-card border-top border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase mb-0 small fw-bold">Pending Orders</h6>
                    <i class="fas fa-hourglass-half fa-lg text-warning opacity-50"></i>
                </div>
                <h2 class="mb-1 font-weight-bold fw-bold text-dark">{{ $kpi['pending_orders'] }}</h2>
                <div class="text-muted small">Awaiting processing</div>
            </div>
        </div>
    </div>

    <!-- Card 6: Average Order Value -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 shadow-sm kpi-card border-top" style="border-top-color: #6f42c1 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase mb-0 small fw-bold">Avg Order Value</h6>
                    <i class="fas fa-chart-line fa-lg opacity-50" style="color: #6f42c1 !important;"></i>
                </div>
                <h2 class="mb-1 font-weight-bold fw-bold text-dark">${{ number_format($kpi['avg_order_value'], 2) }}</h2>
                <div class="text-muted small">Per order average in period</div>
            </div>
        </div>
    </div>

    <!-- Card 7: Average Rating -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 shadow-sm kpi-card border-top" style="border-top-color: #fd7e14 !important;">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="text-muted text-uppercase mb-0 small fw-bold">Average Rating</h6>
                    <i class="fas fa-star fa-lg text-warning"></i>
                </div>
                <h2 class="mb-1 font-weight-bold fw-bold text-dark">{{ number_format($kpi['avg_rating'], 1) }} / 5.0</h2>
                <div class="text-muted small">Customer satisfaction score</div>
                <div class="stars mt-2">
                    @for($i = 1; $i <= 5; $i++)
                        @if($kpi['avg_rating'] >= $i)
                            <i class="fas fa-star text-warning"></i>
                        @elseif($kpi['avg_rating'] >= $i - 0.5)
                            <i class="fas fa-star-half-alt text-warning"></i>
                        @else
                            <i class="far fa-star text-warning"></i>
                        @endif
                    @endfor
                </div>
            </div>
        </div>
    </div>

    <!-- Card 8: Open Support Tickets -->
    <div class="col-xl-3 col-md-6">
        <div class="card h-100 shadow-sm kpi-card border-top border-danger">
            <div class="card-body d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="text-muted text-uppercase mb-0 small fw-bold">Open Tickets</h6>
                        <i class="fas fa-headset fa-lg text-danger opacity-50"></i>
                    </div>
                    <h2 class="mb-1 font-weight-bold fw-bold text-dark">{{ $kpi['pending_support'] }}</h2>
                    <div class="text-muted small mb-2">Messages awaiting reply</div>
                </div>
                <div>
                    <a href="{{ route('admin.support.index') }}" class="text-danger text-decoration-none small fw-semibold">View Messages &rarr;</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CHART ROW 1 — REVENUE TRENDS -->
<div class="row g-4 mb-4">
    <!-- Chart 1: Daily Revenue -->
    <div class="col-lg-8 col-md-12">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="card-title mb-0 fw-bold text-dark">Daily Revenue</h5>
                    <small class="text-muted">Revenue trend for selected period</small>
                </div>
            </div>
            <div class="card-body">
                @if(empty($data['revenueDay']['data']) || array_sum($data['revenueDay']['data']) == 0)
                    <div class="d-flex justify-content-center align-items-center py-5 h-100 min-vh-250">
                        <div class="text-center text-muted">
                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                            <p class="mb-0 small">No revenue data for this period</p>
                        </div>
                    </div>
                @else
                    <div style="position:relative; height:300px;">
                        <canvas id="dailyRevenueChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Chart 2: Monthly Revenue -->
    <div class="col-lg-4 col-md-12">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">Monthly Revenue</h5>
                <small class="text-muted">{{ now()->year }} overview</small>
            </div>
            <div class="card-body">
                @if(empty($data['revenueMonth']['data']) || array_sum($data['revenueMonth']['data']) == 0)
                    <div class="d-flex justify-content-center align-items-center py-5 h-100 min-vh-250">
                        <div class="text-center text-muted">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i>
                            <p class="mb-0 small">No monthly data for this year</p>
                        </div>
                    </div>
                @else
                    <div style="position:relative; height:300px;">
                        <canvas id="monthlyRevenueChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- CHART ROW 2 — PAYMENT ANALYTICS -->
<div class="row g-4 mb-4">
    <!-- Chart 3: Orders by Payment Method -->
    <div class="col-md-6 col-sm-12">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">Payment Methods</h5>
                <small class="text-muted">Distribution of payment methods</small>
            </div>
            <div class="card-body">
                @if(empty($data['paymentMethod']['counts']) || array_sum($data['paymentMethod']['counts']) == 0)
                    <div class="d-flex justify-content-center align-items-center py-5 h-100 min-vh-250">
                        <div class="text-center text-muted">
                            <i class="fas fa-chart-pie fa-2x mb-2"></i>
                            <p class="mb-0 small">No payment method data</p>
                        </div>
                    </div>
                @else
                    <div style="position:relative; height:280px;">
                        <canvas id="paymentMethodsChart"></canvas>
                    </div>
                    <div class="d-flex justify-content-around mt-3 flex-wrap gap-2">
                        @foreach($data['paymentMethod']['counts'] as $method => $count)
                            <div class="text-center">
                                <span class="badge px-2.5 py-1 mb-1 rounded-pill" style="background-color: {{ $method === 'Cash on Delivery' ? '#6c757d' : ($method === 'Zaad' ? '#28a745' : '#dc3545') }}; color: #ffffff;">{{ $method }}</span>
                                <div class="fw-bold text-dark small">{{ $count }} orders</div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Chart 4: Revenue by Payment Method -->
    <div class="col-md-6 col-sm-12">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">Revenue by Payment Method</h5>
                <small class="text-muted">Completed payments per method</small>
            </div>
            <div class="card-body">
                @if(empty($data['paymentMethod']['revenue']) || array_sum($data['paymentMethod']['revenue']) == 0)
                    <div class="d-flex justify-content-center align-items-center py-5 h-100 min-vh-250">
                        <div class="text-center text-muted">
                            <i class="fas fa-hand-holding-usd fa-2x mb-2"></i>
                            <p class="mb-0 small">No completed payments for this period</p>
                        </div>
                    </div>
                @else
                    <div style="position:relative; height:280px;">
                        <canvas id="revenueByPaymentMethodChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- CHART ROW 3 — ORDER STATUS ANALYTICS -->
<div class="row g-4 mb-4">
    <!-- Chart 5: Orders by Status -->
    <div class="col-lg-7 col-md-12">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">Orders by Status</h5>
                <small class="text-muted">Current order pipeline</small>
            </div>
            <div class="card-body">
                @if(empty($data['orderStatus']) || array_sum($data['orderStatus']) == 0)
                    <div class="d-flex justify-content-center align-items-center py-5 h-100" style="min-height: 300px;">
                        <div class="text-center text-muted">
                            <i class="fas fa-shopping-basket fa-2x mb-2"></i>
                            <p class="mb-0 small">No orders in this period</p>
                        </div>
                    </div>
                @else
                    <div style="position:relative; height:350px;">
                        <canvas id="ordersByStatusChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Chart 6: Order Volume Trend -->
    <div class="col-lg-5 col-md-12">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">Order Volume Trend</h5>
                <small class="text-muted">Daily order count for selected period</small>
            </div>
            <div class="card-body">
                @if(empty($data['ordersDay']['data']) || array_sum($data['ordersDay']['data']) == 0)
                    <div class="d-flex justify-content-center align-items-center py-5 h-100" style="min-height: 300px;">
                        <div class="text-center text-muted">
                            <i class="fas fa-chart-line fa-2x mb-2"></i>
                            <p class="mb-0 small">No orders in this period</p>
                        </div>
                    </div>
                @else
                    <div style="position:relative; height:350px;">
                        <canvas id="orderVolumeChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- CHART ROW 4 — CUSTOMER ANALYTICS -->
<div class="row g-4 mb-4">
    <!-- Chart 7: Customer Growth -->
    <div class="col-lg-6 col-md-12">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">Customer Growth</h5>
                <small class="text-muted">New registrations per month ({{ now()->year }})</small>
            </div>
            <div class="card-body">
                @if(empty($data['customerGrowth']['new_customers']) || array_sum($data['customerGrowth']['new_customers']) == 0)
                    <div class="d-flex justify-content-center align-items-center py-5 h-100 min-vh-250">
                        <div class="text-center text-muted">
                            <i class="fas fa-users fa-2x mb-2"></i>
                            <p class="mb-0 small">No customer registrations this year</p>
                        </div>
                    </div>
                @else
                    <div style="position:relative; height:300px;">
                        <canvas id="customerGrowthChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Component 8: Customer Activity Table -->
    <div class="col-lg-6 col-md-12">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">Customer Order Frequency</h5>
                <small class="text-muted">Top 10 customers by order count in period</small>
            </div>
            <div class="card-body p-0">
                @if(empty($data['topCustomers']))
                    <div class="d-flex justify-content-center align-items-center py-5" style="min-height: 300px;">
                        <div class="text-center text-muted">
                            <i class="fas fa-user-friends fa-2x mb-2"></i>
                            <p class="mb-0 small">No customer activity in this period</p>
                        </div>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 text-center" style="width: 65px;">Rank</th>
                                    <th>Customer Name</th>
                                    <th class="text-center">Orders</th>
                                    <th class="text-end">Total Spent</th>
                                    <th class="text-end">Avg Order Value</th>
                                    <th class="text-center pe-3">Last Order</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['topCustomers'] as $cust)
                                    <tr>
                                        <td class="ps-3 fw-bold text-center">
                                            @if($cust['rank'] === 1)
                                                🥇
                                            @elseif($cust['rank'] === 2)
                                                🥈
                                            @elseif($cust['rank'] === 3)
                                                🥉
                                            @else
                                                {{ $cust['rank'] }}
                                            @endif
                                        </td>
                                        <td class="fw-semibold text-dark">{{ $cust['name'] }}</td>
                                        <td class="text-center fw-bold">{{ $cust['orders_count'] }}</td>
                                        <td class="text-end text-success fw-semibold">${{ number_format($cust['total_spent'], 2) }}</td>
                                        <td class="text-end">${{ number_format($cust['avg_order_value'], 2) }}</td>
                                        <td class="text-center text-muted pe-3">{{ $cust['last_order_date'] ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- SECTION — TOP SERVICES PERFORMANCE -->
<div class="row g-4 mb-4">
    <!-- Top Services Table -->
    <div class="col-lg-7 col-md-12">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">Top Services</h5>
                <small class="text-muted">Most popular services in period</small>
            </div>
            <div class="card-body p-0">
                @php
                    $tableServices = array_slice($data['topServices'], 0, 5);
                @endphp
                @if(empty($tableServices))
                    <div class="d-flex justify-content-center align-items-center py-5" style="min-height: 250px;">
                        <p class="text-muted mb-0 small">No service data for this period</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3 text-center" style="width: 65px;">Rank</th>
                                    <th>Service Name</th>
                                    <th class="text-center">Orders</th>
                                    <th class="text-center">Qty Sold</th>
                                    <th class="text-end">Revenue</th>
                                    <th class="text-end">Avg Price</th>
                                    <th class="pe-3" style="width: 150px;">Share of Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tableServices as $index => $srv)
                                    <tr>
                                        <td class="ps-3 fw-bold text-center">
                                            @if($index === 0)
                                                🥇
                                            @elseif($index === 1)
                                                🥈
                                            @elseif($index === 2)
                                                🥉
                                            @else
                                                {{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td class="fw-semibold text-dark">{{ $srv['name'] }}</td>
                                        <td class="text-center">{{ $srv['total_orders'] }}</td>
                                        <td class="text-center">{{ $srv['total_quantity'] }}</td>
                                        <td class="text-end text-success fw-semibold">${{ number_format($srv['total_revenue'], 2) }}</td>
                                        <td class="text-end">${{ number_format($srv['avg_price'], 2) }}</td>
                                        <td class="pe-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress w-100" style="height: 6px;">
                                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $srv['share_percentage'] }}%;" aria-valuenow="{{ $srv['share_percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="small text-muted" style="font-size: 0.75rem;">{{ number_format($srv['share_percentage'], 1) }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Services Revenue Pie (Doughnut) -->
    <div class="col-lg-5 col-md-12">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">Services Revenue Share</h5>
                <small class="text-muted">Revenue share per service</small>
            </div>
            <div class="card-body">
                @if(empty($tableServices))
                    <div class="d-flex justify-content-center align-items-center py-5 h-100" style="min-height: 250px;">
                        <p class="text-muted mb-0 small">No service data for this period</p>
                    </div>
                @else
                    <div style="position:relative; height:280px;">
                        <canvas id="servicesRevenuePieChart"></canvas>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- SECTION — STAFF PERFORMANCE -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">Laundry Staff Performance</h5>
                <small class="text-muted">Staff productivity in period</small>
            </div>
            <div class="card-body p-0">
                @if(empty($data['staffPerf']))
                    <div class="p-4 text-center text-muted">No staff activity in this period.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Staff Name</th>
                                    <th class="text-center">Orders Assigned</th>
                                    <th class="text-center">Orders Completed</th>
                                    <th style="width: 250px;">Completion Rate</th>
                                    <th class="text-center">Avg Processing Time</th>
                                    <th class="text-center pe-3" style="width: 180px;">Performance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['staffPerf'] as $staff)
                                    <tr>
                                        <td class="ps-3 fw-semibold text-dark">{{ $staff['name'] }}</td>
                                        <td class="text-center">{{ $staff['total_orders_handled'] }}</td>
                                        <td class="text-center">{{ $staff['orders_completed'] }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress w-100" style="height: 6px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $staff['completion_rate'] }}%;" aria-valuenow="{{ $staff['completion_rate'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="small text-muted" style="font-size: 0.75rem;">{{ number_format($staff['completion_rate'], 1) }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold">{{ number_format($staff['avg_processing_time'], 1) }} hours</td>
                                        <td class="text-center pe-3">
                                            @if($staff['completion_rate'] >= 90)
                                                <span class="badge bg-success">Excellent</span>
                                            @elseif($staff['completion_rate'] >= 70)
                                                <span class="badge bg-primary">Good</span>
                                            @elseif($staff['completion_rate'] >= 50)
                                                <span class="badge bg-warning text-dark">Average</span>
                                            @else
                                                <span class="badge bg-danger">Needs Improvement</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- SECTION — DELIVERY PERFORMANCE -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="card-title mb-0 fw-bold text-dark">Delivery Agent Performance</h5>
                <small class="text-muted">Delivery metrics in period</small>
            </div>
            <div class="card-body p-0">
                @if(empty($data['deliveryPerf']))
                    <div class="p-4 text-center text-muted">No delivery agent activity in this period.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Agent Name</th>
                                    <th class="text-center">Assigned</th>
                                    <th class="text-center">Delivered</th>
                                    <th style="width: 250px;">Delivery Rate</th>
                                    <th class="text-center">Avg Delivery Time</th>
                                    <th class="text-center pe-3" style="width: 180px;">On-Time Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['deliveryPerf'] as $agent)
                                    <tr>
                                        <td class="ps-3 fw-semibold text-dark">{{ $agent['name'] }}</td>
                                        <td class="text-center">{{ $agent['total_assigned'] }}</td>
                                        <td class="text-center">{{ $agent['total_delivered'] }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="progress w-100" style="height: 6px;">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $agent['delivery_rate'] }}%;" aria-valuenow="{{ $agent['delivery_rate'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                                <span class="small text-muted" style="font-size: 0.75rem;">{{ number_format($agent['delivery_rate'], 1) }}%</span>
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold">{{ number_format($agent['avg_delivery_time'], 1) }} hours</td>
                                        <td class="text-center pe-3">
                                            @if($agent['on_time_rate'] >= 90)
                                                <span class="badge bg-success">On Time ({{ number_format($agent['on_time_rate'], 0) }}%)</span>
                                            @elseif($agent['on_time_rate'] >= 70)
                                                <span class="badge bg-primary">Acceptable ({{ number_format($agent['on_time_rate'], 0) }}%)</span>
                                            @else
                                                <span class="badge bg-danger">Needs Attention ({{ number_format($agent['on_time_rate'], 0) }}%)</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- SECTION — REVIEW ANALYTICS -->
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="card-title mb-0 fw-bold text-dark">Customer Satisfaction</h5>
        <small class="text-muted">Review metrics in period</small>
    </div>
    <div class="card-body">
        @php
            $revStats = $data['reviewStats'];
            $totalReviews = $revStats['total'];
        @endphp
        @if($totalReviews == 0)
            <div class="p-5 text-center text-muted">
                <i class="fas fa-star fa-2x mb-2 opacity-50"></i>
                <p class="mb-0 small">No reviews received for this period</p>
            </div>
        @else
            <div class="row align-items-center g-4">
                <!-- Left: Avg Display -->
                <div class="col-md-5 text-center border-end">
                    <h1 class="display-3 fw-bold text-dark mb-1">{{ number_format($revStats['average'], 1) }}</h1>
                    <div class="stars fs-4 text-warning mb-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($revStats['average'] >= $i)
                                <i class="fas fa-star text-warning"></i>
                            @elseif($revStats['average'] >= $i - 0.5)
                                <i class="fas fa-star-half-alt text-warning"></i>
                            @else
                                <i class="far fa-star text-warning"></i>
                            @endif
                        @endfor
                    </div>
                    <div class="text-muted small">Based on {{ $totalReviews }} reviews in period</div>
                </div>

                <!-- Right: Star Breakdown -->
                <div class="col-md-7">
                    @for($star = 5; $star >= 1; $star--)
                        @php
                            $count = $revStats['distribution'][$star] ?? 0;
                            $pct = $totalReviews > 0 ? ($count / $totalReviews * 100) : 0;
                            
                            $colorMap = [
                                5 => 'success',
                                4 => 'info',
                                3 => 'warning',
                                2 => 'warning',
                                1 => 'danger'
                            ];
                            $color = $colorMap[$star];
                        @endphp
                        <div class="d-flex align-items-center mb-2" style="font-size: 0.85rem;">
                            <div class="fw-bold text-dark me-2" style="width: 50px;">{{ $star }} Stars</div>
                            <div class="progress flex-grow-1" style="height: 10px;">
                                <div class="progress-bar bg-{{ $color }}" role="progressbar" style="width: {{ $pct }}%; @if($star === 2) background-color: #fd7e14 !important; @endif" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                            <div class="text-muted text-end ms-3" style="width: 80px;">{{ $count }} ({{ number_format($pct, 0) }}%)</div>
                        </div>
                    @endfor
                </div>
            </div>
        @endif
    </div>
</div>

<!-- SECTION — SUPPORT MESSAGE STATS -->
<div class="row g-3 mb-4">
    @php
        $support = $data['supportStats'];
        $resolvedPct = $support['total_messages'] > 0 ? ($support['resolved'] / $support['total_messages'] * 100) : 0;
        $responseTime = $support['avg_response_hours'];
        
        $timeColor = 'success';
        if ($responseTime > 48) {
            $timeColor = 'danger';
        } elseif ($responseTime > 24) {
            $timeColor = 'warning';
        }
    @endphp
    <!-- Card 1: Total Messages -->
    <div class="col-md-3 col-sm-6">
        <div class="card shadow-sm border-0 border-start border-4 border-primary">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Total Support Messages</div>
                <h4 class="mb-0 fw-bold text-dark">{{ $support['total_messages'] }}</h4>
            </div>
        </div>
    </div>
    
    <!-- Card 2: Pending -->
    <div class="col-md-3 col-sm-6">
        <div class="card shadow-sm border-0 border-start border-4 border-warning">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Pending Replies</div>
                <h4 class="mb-0 fw-bold text-warning">{{ $support['pending'] }}</h4>
            </div>
        </div>
    </div>

    <!-- Card 3: Resolved Rate -->
    <div class="col-md-3 col-sm-6">
        <div class="card shadow-sm border-0 border-start border-4 border-success">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Resolution Rate</div>
                <h4 class="mb-0 fw-bold text-success">{{ number_format($resolvedPct, 1) }}%</h4>
            </div>
        </div>
    </div>

    <!-- Card 4: Avg Response Time -->
    <div class="col-md-3 col-sm-6">
        <div class="card shadow-sm border-0 border-start border-4 border-{{ $timeColor }}">
            <div class="card-body py-3">
                <div class="text-muted small text-uppercase fw-bold mb-1" style="font-size: 0.7rem;">Avg Response Time</div>
                <h4 class="mb-0 fw-bold text-{{ $timeColor }}">{{ number_format($responseTime, 1) }} hours</h4>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // ------------------------------------
        // Center Text Plugin for Doughnut Chart
        // ------------------------------------
        const centerTextPlugin = {
            id: 'centerText',
            afterDraw: function (chart) {
                if (chart.config.type !== 'doughnut') return;
                const { ctx, chartArea: { top, bottom, left, right } } = chart;
                ctx.save();
                
                const count = chart.config.options.plugins.centerText.text || '0';
                const label = 'Orders';
                
                const centerX = left + (right - left) / 2;
                const centerY = top + (bottom - top) / 2;
                
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                
                // Count number text
                ctx.fillStyle = '#212529';
                ctx.font = 'bold 22px Inter, sans-serif';
                ctx.fillText(count, centerX, centerY - 8);
                
                // Label text
                ctx.fillStyle = '#6c757d';
                ctx.font = '500 11px Inter, sans-serif';
                ctx.fillText(label, centerX, centerY + 12);
                
                ctx.restore();
            }
        };

        Chart.register(centerTextPlugin);

        // ------------------------------------
        // Chart 1: Daily Revenue (Line)
        // ------------------------------------
        @if(!empty($data['revenueDay']['data']) && array_sum($data['revenueDay']['data']) > 0)
            const dailyRevenueData = @json($data['revenueDay']);
            new Chart(document.getElementById('dailyRevenueChart'), {
                type: 'line',
                data: {
                    labels: dailyRevenueData.labels,
                    datasets: [{
                        label: 'Revenue',
                        data: dailyRevenueData.data,
                        tension: 0.4,
                        fill: true,
                        backgroundColor: 'rgba(40,167,69,0.08)',
                        borderColor: '#28a745',
                        pointBackgroundColor: '#28a745',
                        pointHoverBackgroundColor: '#28a745',
                        pointRadius: 3,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ' $' + ctx.parsed.y.toFixed(2)
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (val) => '$' + val
                            }
                        }
                    }
                }
            });
        @endif

        // ------------------------------------
        // Chart 2: Monthly Revenue (Bar)
        // ------------------------------------
        @if(!empty($data['revenueMonth']['data']) && array_sum($data['revenueMonth']['data']) > 0)
            const monthlyRevenueData = @json($data['revenueMonth']);
            
            // Highlight current month in orange, others in blue
            const currentMonthIdx = new Date().getMonth();
            const monthlyBarColors = Array(12).fill('#007bff');
            monthlyBarColors[currentMonthIdx] = '#fd7e14';
            const monthlyBarHoverColors = Array(12).fill('#0056b3');
            monthlyBarHoverColors[currentMonthIdx] = '#ca6510';

            new Chart(document.getElementById('monthlyRevenueChart'), {
                type: 'bar',
                data: {
                    labels: monthlyRevenueData.labels,
                    datasets: [{
                        data: monthlyRevenueData.data,
                        backgroundColor: monthlyBarColors,
                        hoverBackgroundColor: monthlyBarHoverColors,
                        borderRadius: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ' $' + ctx.parsed.y.toFixed(2)
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: (val) => '$' + val
                            }
                        }
                    }
                }
            });
        @endif

        // ------------------------------------
        // Chart 3: Orders by Payment Method (Doughnut)
        // ------------------------------------
        @if(!empty($data['paymentMethod']['counts']) && array_sum($data['paymentMethod']['counts']) > 0)
            const paymentCounts = @json($data['paymentMethod']['counts']);
            const countsLabels = Object.keys(paymentCounts);
            const countsValues = Object.values(paymentCounts);

            new Chart(document.getElementById('paymentMethodsChart'), {
                type: 'doughnut',
                data: {
                    labels: countsLabels,
                    datasets: [{
                        data: countsValues,
                        backgroundColor: ['#6c757d', '#28a745', '#dc3545'],
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '72%',
                    plugins: {
                        legend: { position: 'bottom' },
                        centerText: {
                            text: '{{ $data["kpi"]["total_orders"] }}'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    const val = ctx.parsed;
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ` ${ctx.label}: ${val} orders (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        @endif

        // ------------------------------------
        // Chart 4: Revenue by Payment Method (Horizontal Bar)
        // ------------------------------------
        @if(!empty($data['paymentMethod']['revenue']) && array_sum($data['paymentMethod']['revenue']) > 0)
            const paymentRevenue = @json($data['paymentMethod']['revenue']);
            const revLabels = Object.keys(paymentRevenue);
            const revValues = Object.values(paymentRevenue);

            new Chart(document.getElementById('revenueByPaymentMethodChart'), {
                type: 'bar',
                data: {
                    labels: revLabels,
                    datasets: [{
                        data: revValues,
                        backgroundColor: ['#6c757d', '#28a745', '#dc3545'],
                        hoverBackgroundColor: ['#5a6268', '#218838', '#c82333'],
                        borderRadius: 4,
                        barThickness: 28
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ' $' + ctx.parsed.x.toFixed(2)
                            }
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                callback: (val) => '$' + val
                            }
                        }
                    }
                }
            });
        @endif

        // ------------------------------------
        // Chart 5: Orders by Status (Horizontal Bar - Sorted Descending)
        // ------------------------------------
        @if(!empty($data['orderStatus']) && array_sum($data['orderStatus']) > 0)
            const orderStatusDataRaw = @json($data['orderStatus']);
            
            const statusConfig = {
                pending: { label: 'Pending', color: '#ffc107' },
                confirmed: { label: 'Confirmed', color: '#17a2b8' },
                washing: { label: 'Washing', color: '#6610f2' },
                drying: { label: 'Drying', color: '#6f42c1' },
                ironing: { label: 'Ironing', color: '#e83e8c' },
                folding: { label: 'Folding', color: '#20c997' },
                ready_for_delivery: { label: 'Ready for Delivery', color: '#fd7e14' },
                out_for_delivery: { label: 'Out for Delivery', color: '#007bff' },
                delivered: { label: 'Delivered', color: '#28a745' },
                cancelled: { label: 'Cancelled', color: '#dc3545' }
            };

            const sortedStatusArray = Object.entries(orderStatusDataRaw)
                .map(([status, count]) => ({
                    status,
                    count,
                    label: statusConfig[status]?.label || status,
                    color: statusConfig[status]?.color || '#6c757d'
                }))
                .sort((a, b) => b.count - a.count);

            new Chart(document.getElementById('ordersByStatusChart'), {
                type: 'bar',
                data: {
                    labels: sortedStatusArray.map(item => item.label),
                    datasets: [{
                        data: sortedStatusArray.map(item => item.count),
                        backgroundColor: sortedStatusArray.map(item => item.color),
                        borderRadius: 4,
                        barThickness: 16
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        @endif

        // ------------------------------------
        // Chart 6: Order Volume Trend (Line)
        // ------------------------------------
        @if(!empty($data['ordersDay']['data']) && array_sum($data['ordersDay']['data']) > 0)
            const orderVolumeData = @json($data['ordersDay']);
            new Chart(document.getElementById('orderVolumeChart'), {
                type: 'line',
                data: {
                    labels: orderVolumeData.labels,
                    datasets: [{
                        label: 'Orders',
                        data: orderVolumeData.data,
                        tension: 0.3,
                        fill: true,
                        backgroundColor: 'rgba(0,123,255,0.06)',
                        borderColor: '#007bff',
                        pointBackgroundColor: '#007bff',
                        pointHoverBackgroundColor: '#007bff',
                        pointRadius: 3,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        @endif

        // ------------------------------------
        // Chart 7: Customer Growth (Dual Y-Axis Bar/Line)
        // ------------------------------------
        @if(!empty($data['customerGrowth']['new_customers']) && array_sum($data['customerGrowth']['new_customers']) > 0)
            const customerGrowthData = @json($data['customerGrowth']);
            new Chart(document.getElementById('customerGrowthChart'), {
                type: 'bar',
                data: {
                    labels: customerGrowthData.labels,
                    datasets: [
                        {
                            type: 'bar',
                            label: 'New Customers',
                            data: customerGrowthData.new_customers,
                            backgroundColor: '#17a2b8',
                            borderRadius: 4,
                            yAxisID: 'y'
                        },
                        {
                            type: 'line',
                            label: 'Total Customers (Cumulative)',
                            data: customerGrowthData.cumulative_customers,
                            borderColor: '#28a745',
                            backgroundColor: 'transparent',
                            tension: 0.25,
                            fill: false,
                            yAxisID: 'y1',
                            pointBackgroundColor: '#28a745'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'New Registrations',
                                font: { weight: 'bold' }
                            },
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Total Customers',
                                font: { weight: 'bold' }
                            },
                            beginAtZero: true,
                            grid: {
                                drawOnChartArea: false
                            },
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        @endif

        // ------------------------------------
        // Chart 9: Services Revenue Share (Pie/Doughnut)
        // ------------------------------------
        @if(!empty($data['topServices']))
            const servicesData = @json($data['topServices']);
            
            const top5 = servicesData.slice(0, 5);
            const remainder = servicesData.slice(5);
            
            const serviceLabels = top5.map(item => item.name);
            const serviceValues = top5.map(item => item.total_revenue);
            
            if (remainder.length > 0) {
                const othersSum = remainder.reduce((sum, item) => sum + item.total_revenue, 0);
                serviceLabels.push('Others');
                serviceValues.push(othersSum);
            }

            new Chart(document.getElementById('servicesRevenuePieChart'), {
                type: 'doughnut',
                data: {
                    labels: serviceLabels,
                    datasets: [{
                        data: serviceValues,
                        backgroundColor: [
                            '#0d6efd',
                            '#198754',
                            '#ffc107',
                            '#dc3545',
                            '#6f42c1',
                            '#6c757d'
                        ],
                        borderWidth: 2,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' },
                        tooltip: {
                            callbacks: {
                                label: function (ctx) {
                                    const val = ctx.parsed;
                                    const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ` ${ctx.label}: $${val.toFixed(2)} (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        @endif
    });
</script>
@endsection
