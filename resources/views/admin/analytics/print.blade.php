<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LOMS Analytics Report - Print View</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome CDN -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8f9fa;
            color: #212529;
            font-size: 14px;
        }
        
        .print-container {
            max-width: 900px;
            margin: 30px auto;
            background: #ffffff;
            padding: 40px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
            border-radius: 8px;
        }

        .page-break {
            display: none;
        }

        /* Styling for screen vs print header */
        .print-header {
            display: none;
        }

        @media print {
            body {
                background-color: #ffffff;
                font-size: 12px;
            }
            .print-container {
                max-width: 100%;
                margin: 0;
                padding: 0;
                box-shadow: none;
                border-radius: 0;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                display: block;
                page-break-before: always;
            }
            .print-header {
                display: block !important;
            }
            .card {
                border: 1px solid #dee2e6 !important;
                box-shadow: none !important;
            }
            table {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>

<div class="print-container">
    <!-- Screen Controls (hidden when printing) -->
    <div class="no-print d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">
        <div>
            <a href="{{ route('admin.analytics.index', request()->query()) }}" class="btn btn-outline-secondary btn-sm me-2">
                <i class="fas fa-arrow-left me-1"></i> Back to Analytics
            </a>
            <a href="{{ route('admin.analytics.export.csv', request()->query()) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-file-csv me-1"></i> Export CSV
            </a>
        </div>
        <button onclick="window.print()" class="btn btn-danger btn-sm">
            <i class="fas fa-print me-1"></i> Print Report
        </button>
    </div>

    <!-- Print Header (shown only when printing) -->
    <div class="print-header mb-4 text-center">
        <h2 class="fw-bold mb-1">LOMS Analytics Report</h2>
        <h5 class="text-muted mb-2">Iimaan Dry Cleaner &mdash; Hargeisa, Somaliland</h5>
        <div class="small mb-1"><strong>Report Period:</strong> {{ $start->format('M d, Y') }} &mdash; {{ $end->format('M d, Y') }}</div>
        <div class="small text-muted"><strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}</div>
        <hr class="my-3">
    </div>

    <!-- Screen Header (shown only on screen) -->
    <div class="no-print mb-4">
        <div class="text-center">
            <h3 class="fw-bold mb-1">LOMS Analytics Report Preview</h3>
            <p class="text-muted mb-2">Iimaan Dry Cleaner &mdash; Hargeisa, Somaliland</p>
            <span class="badge bg-secondary mb-1">Period: {{ $start->format('M d, Y') }} &mdash; {{ $end->format('M d, Y') }}</span>
        </div>
    </div>

    <!-- SECTION 1: KPI SUMMARY -->
    <div class="card mb-4 border-0 bg-white">
        <div class="card-header bg-light border py-2">
            <h6 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2"></i>Key Performance Indicators (KPI) Summary</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Metric Group</th>
                        <th>KPI Metric Description</th>
                        <th class="text-end" style="width: 200px;">Value</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td rowspan="4" class="fw-semibold bg-light-subtle">Primary KPIs</td>
                        <td>Total Completed Revenue</td>
                        <td class="text-end fw-bold text-success">${{ number_format($data['kpi']['total_revenue'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Total Orders Created</td>
                        <td class="text-end fw-bold">{{ $data['kpi']['total_orders'] }}</td>
                    </tr>
                    <tr>
                        <td>New Customer Registrations</td>
                        <td class="text-end fw-bold">{{ $data['kpi']['new_customers'] }}</td>
                    </tr>
                    <tr>
                        <td>Delivered Orders</td>
                        <td class="text-end fw-bold">{{ $data['kpi']['delivered_orders'] }}</td>
                    </tr>
                    <tr>
                        <td rowspan="4" class="fw-semibold bg-light-subtle">Secondary KPIs</td>
                        <td>Pending / Processing Orders</td>
                        <td class="text-end fw-bold text-warning">{{ $data['kpi']['pending_orders'] }}</td>
                    </tr>
                    <tr>
                        <td>Average Order Value (AOV)</td>
                        <td class="text-end fw-bold">${{ number_format($data['kpi']['avg_order_value'], 2) }}</td>
                    </tr>
                    <tr>
                        <td>Average Review Rating</td>
                        <td class="text-end fw-bold text-primary">{{ number_format($data['kpi']['avg_rating'], 1) }} / 5.0</td>
                    </tr>
                    <tr>
                        <td>Open Support Tickets</td>
                        <td class="text-end fw-bold text-danger">{{ $data['kpi']['pending_support'] }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGE BREAK -->
    <div class="page-break"></div>

    <!-- SECTION 2: ORDERS BY STATUS & PAYMENT METHODS -->
    <div class="row g-4 mb-4">
        <!-- Orders by Status -->
        <div class="col-md-6">
            <div class="card border-0 bg-white">
                <div class="card-header bg-light border py-2">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-shopping-basket me-2"></i>Orders by Status Pipeline</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Status</th>
                                <th class="text-center" style="width: 100px;">Orders Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['orderStatus'] as $status => $count)
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $status)) }}</td>
                                    <td class="text-center fw-semibold">{{ $count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payment Methods counts -->
        <div class="col-md-6">
            <div class="card border-0 bg-white">
                <div class="card-header bg-light border py-2">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-credit-card me-2"></i>Payment Methods Summary</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Method</th>
                                <th class="text-center">Orders</th>
                                <th class="text-end">Completed Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data['paymentMethod']['counts'] as $method => $count)
                                <tr>
                                    <td class="fw-semibold">{{ $method }}</td>
                                    <td class="text-center">{{ $count }}</td>
                                    <td class="text-end text-success fw-bold">${{ number_format($data['paymentMethod']['revenue'][$method] ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- SECTION 3: TOP SERVICES -->
    <div class="card mb-4 border-0 bg-white">
        <div class="card-header bg-light border py-2">
            <h6 class="mb-0 fw-bold"><i class="fas fa-tags me-2"></i>Top Services Performance</h6>
        </div>
        <div class="card-body p-0">
            @php
                $tableServices = array_slice($data['topServices'], 0, 5);
            @endphp
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 70px;">Rank</th>
                        <th>Service Name</th>
                        <th class="text-center">Orders</th>
                        <th class="text-center">Qty Sold</th>
                        <th class="text-end">Revenue Generated</th>
                        <th class="text-end">Avg Price</th>
                        <th class="text-center">Revenue Share</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tableServices as $index => $srv)
                        <tr>
                            <td class="text-center fw-bold">{{ $index + 1 }}</td>
                            <td class="fw-semibold text-dark">{{ $srv['name'] }}</td>
                            <td class="text-center">{{ $srv['total_orders'] }}</td>
                            <td class="text-center">{{ $srv['total_quantity'] }}</td>
                            <td class="text-end text-success fw-bold">${{ number_format($srv['total_revenue'], 2) }}</td>
                            <td class="text-end">${{ number_format($srv['avg_price'], 2) }}</td>
                            <td class="text-center fw-semibold">{{ number_format($srv['share_percentage'], 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGE BREAK -->
    <div class="page-break"></div>

    <!-- SECTION 4: STAFF & DELIVERY PERFORMANCE -->
    <div class="card mb-4 border-0 bg-white">
        <div class="card-header bg-light border py-2">
            <h6 class="mb-0 fw-bold"><i class="fas fa-users-cog me-2"></i>Laundry Staff Performance</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Staff Member Name</th>
                        <th class="text-center">Orders Assigned</th>
                        <th class="text-center">Orders Completed</th>
                        <th class="text-center">Completion Rate</th>
                        <th class="text-center">Avg Processing Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['staffPerf'] as $staff)
                        <tr>
                            <td class="fw-semibold text-dark">{{ $staff['name'] }}</td>
                            <td class="text-center">{{ $staff['total_orders_handled'] }}</td>
                            <td class="text-center">{{ $staff['orders_completed'] }}</td>
                            <td class="text-center fw-bold">{{ number_format($staff['completion_rate'], 1) }}%</td>
                            <td class="text-center">{{ number_format($staff['avg_processing_time'], 1) }} hours</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mb-4 border-0 bg-white">
        <div class="card-header bg-light border py-2">
            <h6 class="mb-0 fw-bold"><i class="fas fa-truck-ramp-box me-2"></i>Delivery Agent Performance</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Agent Name</th>
                        <th class="text-center">Total Assigned</th>
                        <th class="text-center">Total Delivered</th>
                        <th class="text-center">Delivery Rate</th>
                        <th class="text-center">Avg Delivery Time</th>
                        <th class="text-center">On-Time Rate</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['deliveryPerf'] as $agent)
                        <tr>
                            <td class="fw-semibold text-dark">{{ $agent['name'] }}</td>
                            <td class="text-center">{{ $agent['total_assigned'] }}</td>
                            <td class="text-center">{{ $agent['total_delivered'] }}</td>
                            <td class="text-center fw-bold">{{ number_format($agent['delivery_rate'], 1) }}%</td>
                            <td class="text-center">{{ number_format($agent['avg_delivery_time'], 1) }} hours</td>
                            <td class="text-center fw-bold text-success">{{ number_format($agent['on_time_rate'], 1) }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- PAGE BREAK -->
    <div class="page-break"></div>

    <!-- SECTION 5: CUSTOMER SATISFACTION & SUPPORT STATS -->
    <div class="row g-4 mb-4">
        <!-- Satisfactions -->
        <div class="col-md-7">
            <div class="card border-0 bg-white">
                <div class="card-header bg-light border py-2">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-star me-2"></i>Customer Satisfaction score</h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered align-middle mb-0">
                        <tbody>
                            <tr>
                                <td class="text-center fw-bold fs-4 bg-light-subtle" style="width: 45%;">
                                    <div>{{ number_format($data['reviewStats']['average'], 1) }} / 5.0</div>
                                    <div class="small text-muted" style="font-size: 0.75rem;">Total Reviews: {{ $data['reviewStats']['total'] }}</div>
                                </td>
                                <td>
                                    @for($star = 5; $star >= 1; $star--)
                                        @php
                                            $count = $data['reviewStats']['distribution'][$star] ?? 0;
                                            $pct = $data['reviewStats']['total'] > 0 ? ($count / $data['reviewStats']['total'] * 100) : 0;
                                        @endphp
                                        <div class="d-flex align-items-center mb-1" style="font-size: 0.75rem;">
                                            <div style="width: 50px;">{{ $star }} Stars:</div>
                                            <div class="fw-bold text-dark ms-2">{{ $count }} ({{ number_format($pct, 0) }}%)</div>
                                        </div>
                                    @endfor
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Support Tickets -->
        <div class="col-md-5">
            <div class="card border-0 bg-white">
                <div class="card-header bg-light border py-2">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-headset me-2"></i>Support Tickets Summary</h6>
                </div>
                <div class="card-body p-0">
                    @php
                        $sup = $data['supportStats'];
                        $resolvedRate = $sup['total_messages'] > 0 ? ($sup['resolved'] / $sup['total_messages'] * 100) : 0;
                    @endphp
                    <table class="table table-bordered align-middle mb-0" style="font-size: 0.85rem;">
                        <tbody>
                            <tr>
                                <th>Total Support Tickets</th>
                                <td class="text-end fw-bold">{{ $sup['total_messages'] }}</td>
                            </tr>
                            <tr>
                                <th>Pending Replies</th>
                                <td class="text-end fw-bold text-warning">{{ $sup['pending'] }}</td>
                            </tr>
                            <tr>
                                <th>Resolution Rate</th>
                                <td class="text-end fw-bold text-success">{{ number_format($resolvedRate, 1) }}%</td>
                            </tr>
                            <tr>
                                <th>Avg Response Time</th>
                                <td class="text-end fw-bold">{{ number_format($sup['avg_response_hours'], 1) }} hours</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-5 pt-3 border-top text-center text-muted" style="font-size: 11px;">
        <div class="fw-bold">LOMS &mdash; Laundry Online Management System</div>
        <div>Report generated automatically by LOMS System. Hargeisa, Somaliland.</div>
    </div>
</div>

</body>
</html>
