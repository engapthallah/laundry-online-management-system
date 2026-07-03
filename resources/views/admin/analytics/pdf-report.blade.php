<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LOMS Analytics Report</title>
    <style>
        @page {
            margin: 1.5cm;
        }
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
            color: #333333;
            font-size: 11px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 10px;
        }
        .header h1 {
            font-size: 20px;
            color: #1e3a8a;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header h2 {
            font-size: 12px;
            color: #666666;
            margin: 0 0 10px 0;
            font-weight: normal;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .meta-table td {
            border: none;
            padding: 2px 0;
        }
        .meta-table .label {
            font-weight: bold;
            color: #1e3a8a;
            width: 150px;
        }
        .section-title {
            font-size: 13px;
            color: #1e3a8a;
            border-bottom: 1px solid #dddddd;
            padding-bottom: 4px;
            margin-top: 25px;
            margin-bottom: 12px;
            text-transform: uppercase;
            font-weight: bold;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th {
            background-color: #f3f4f6;
            color: #1e3a8a;
            font-weight: bold;
            text-align: left;
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            font-size: 10px;
            text-transform: uppercase;
        }
        table.data-table td {
            border: 1px solid #e5e7eb;
            padding: 6px 8px;
            vertical-align: middle;
        }
        table.data-table tr:nth-child(even) td {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .fw-bold {
            font-weight: bold;
        }
        .text-success {
            color: #10b981;
        }
        .text-primary {
            color: #3b82f6;
        }
        .text-danger {
            color: #ef4444;
        }
        .page-break {
            page-break-before: always;
        }
        /* Grid replacement for side-by-side elements */
        .columns-container {
            width: 100%;
            margin-bottom: 20px;
        }
        .columns-container td.column {
            width: 48%;
            vertical-align: top;
        }
        .columns-container td.spacer {
            width: 4%;
        }
        .kpi-container {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .kpi-card {
            width: 23%;
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
            padding: 8px;
            text-align: center;
            border-radius: 4px;
        }
        .kpi-val {
            font-size: 14px;
            font-weight: bold;
            color: #1e3a8a;
            margin-top: 4px;
        }
        .kpi-label {
            font-size: 9px;
            color: #6b7280;
            text-transform: uppercase;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Iimaan Dry Cleaner</h1>
        <h2>LOMS Analytics Report</h2>
    </div>

    <table class="meta-table">
        <tr>
            <td class="label">Report Period:</td>
            <td>{{ $start->format('F d, Y') }} &mdash; {{ $end->format('F d, Y') }} (Preset: {{ ucfirst($period) }})</td>
            <td class="label text-right">Generated At:</td>
            <td class="text-right">{{ now()->format('M d, Y h:i A') }}</td>
        </tr>
    </table>

    <div class="section-title">Key Performance Indicators</div>
    
    <!-- KPI Row 1 -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 10px; margin-left: -10px; margin-right: -10px; margin-bottom: 5px;">
        <tr>
            <td class="kpi-card">
                <div class="kpi-label">Total Revenue</div>
                <div class="kpi-val text-success">${{ number_format($data['kpi']['total_revenue'], 2) }}</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-label">Total Orders</div>
                <div class="kpi-val">{{ $data['kpi']['total_orders'] }}</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-label">New Customers</div>
                <div class="kpi-val">{{ $data['kpi']['new_customers'] }}</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-label">Delivered Orders</div>
                <div class="kpi-val">{{ $data['kpi']['delivered_orders'] }}</div>
            </td>
        </tr>
    </table>
    
    <!-- KPI Row 2 -->
    <table style="width: 100%; border-collapse: separate; border-spacing: 10px; margin-left: -10px; margin-right: -10px; margin-bottom: 20px;">
        <tr>
            <td class="kpi-card">
                <div class="kpi-label">Confirmed Orders</div>
                <div class="kpi-val text-primary">{{ $data['kpi']['confirmed_orders'] }}</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-label">Avg Order Value</div>
                <div class="kpi-val">${{ number_format($data['kpi']['avg_order_value'], 2) }}</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-label">Average Rating</div>
                <div class="kpi-val text-primary">{{ number_format($data['kpi']['avg_rating'], 1) }} / 5.0</div>
            </td>
            <td class="kpi-card">
                <div class="kpi-label">Open Tickets</div>
                <div class="kpi-val text-danger">{{ $data['kpi']['pending_support'] }}</div>
            </td>
        </tr>
    </table>

    <table class="columns-container" style="border: 0; border-collapse: collapse;">
        <tr>
            <!-- Left Column: Orders by Status -->
            <td class="column">
                <div class="section-title" style="margin-top: 0;">Orders by Status Pipeline</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th class="text-center" style="width: 80px;">Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['orderStatus'] as $status => $count)
                            <tr>
                                <td>{{ ucfirst(str_replace('_', ' ', $status)) }}</td>
                                <td class="text-center fw-bold">{{ $count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            
            <td class="spacer"></td>
            
            <!-- Right Column: Payment Methods Summary -->
            <td class="column">
                <div class="section-title" style="margin-top: 0;">Payment Methods Summary</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Method</th>
                            <th class="text-center" style="width: 60px;">Orders</th>
                            <th class="text-right" style="width: 100px;">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['paymentMethod']['counts'] as $method => $count)
                            <tr>
                                <td class="fw-bold">{{ strtoupper($method) }}</td>
                                <td class="text-center">{{ $count }}</td>
                                <td class="text-right text-success fw-bold">${{ number_format($data['paymentMethod']['revenue'][$method] ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <div class="page-break"></div>

    <div class="section-title">Top Services Performance</div>
    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center" style="width: 40px;">Rank</th>
                <th>Service Name</th>
                <th class="text-center" style="width: 60px;">Orders</th>
                <th class="text-center" style="width: 60px;">Qty Sold</th>
                <th class="text-right" style="width: 110px;">Revenue Generated</th>
                <th class="text-right" style="width: 80px;">Avg Price</th>
                <th class="text-center" style="width: 80px;">Rev Share</th>
            </tr>
        </thead>
        <tbody>
            @foreach(array_slice($data['topServices'], 0, 10) as $index => $srv)
                <tr>
                    <td class="text-center fw-bold">{{ $index + 1 }}</td>
                    <td class="fw-bold">{{ $srv['name'] }}</td>
                    <td class="text-center">{{ $srv['total_orders'] }}</td>
                    <td class="text-center">{{ $srv['total_quantity'] }}</td>
                    <td class="text-right text-success fw-bold">${{ number_format($srv['total_revenue'], 2) }}</td>
                    <td class="text-right">${{ number_format($srv['avg_price'], 2) }}</td>
                    <td class="text-center">{{ number_format($srv['share_percentage'], 1) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Daily Revenue breakdown</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Date</th>
                <th class="text-right">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data['revenueDay']['labels'] as $index => $label)
                @if($data['revenueDay']['data'][$index] > 0)
                    <tr>
                        <td>{{ $label }}, {{ $start->year }}</td>
                        <td class="text-right text-success fw-bold">${{ number_format($data['revenueDay']['data'][$index], 2) }}</td>
                    </tr>
                @endif
            @endforeach
        </tbody>
    </table>

    <table class="columns-container" style="border: 0; border-collapse: collapse;">
        <tr>
            <!-- Left Column: Monthly Revenue breakdown -->
            <td class="column">
                <div class="section-title">Monthly Revenue breakdown</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th class="text-right">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['revenueMonth']['labels'] as $index => $label)
                            <tr>
                                <td>{{ $label }} {{ Carbon\Carbon::now()->year }}</td>
                                <td class="text-right text-success fw-bold">${{ number_format($data['revenueMonth']['data'][$index], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </td>
            
            <td class="spacer"></td>
            
            <!-- Right Column: Support and Customer Satisfaction -->
            <td class="column">
                <div class="section-title">Support & Satisfaction</div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th colspan="2">Support Tickets Summary</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sup = $data['supportStats'];
                            $resolvedRate = $sup['total_messages'] > 0 ? ($sup['resolved'] / $sup['total_messages'] * 100) : 0;
                        @endphp
                        <tr>
                            <td>Total Support Tickets</td>
                            <td class="text-right fw-bold">{{ $sup['total_messages'] }}</td>
                        </tr>
                        <tr>
                            <td>Pending Replies</td>
                            <td class="text-right fw-bold text-danger">{{ $sup['pending'] }}</td>
                        </tr>
                        <tr>
                            <td>Resolution Rate</td>
                            <td class="text-right fw-bold text-success">{{ number_format($resolvedRate, 1) }}%</td>
                        </tr>
                        <tr>
                            <td>Avg Response Time</td>
                            <td class="text-right">{{ number_format($sup['avg_response_hours'], 1) }} hours</td>
                        </tr>
                    </tbody>
                </table>

                <table class="data-table" style="margin-top: 15px;">
                    <thead>
                        <tr>
                            <th colspan="2">Customer Satisfaction Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center fw-bold" style="font-size: 13px; background-color: #f9fafb; width: 40%;">
                                {{ number_format($data['reviewStats']['average'], 1) }} / 5.0
                                <div style="font-size: 8px; color: #6b7280; font-weight: normal; margin-top: 2px;">
                                    Total Reviews: {{ $data['reviewStats']['total'] }}
                                </div>
                            </td>
                            <td>
                                @for($star = 5; $star >= 1; $star--)
                                    @php
                                        $count = $data['reviewStats']['distribution'][$star] ?? 0;
                                        $pct = $data['reviewStats']['total'] > 0 ? ($count / $data['reviewStats']['total'] * 100) : 0;
                                    @endphp
                                    <div style="font-size: 8px; margin-bottom: 2px;">
                                        {{ $star }} Stars: <strong>{{ $count }} ({{ number_format($pct, 0) }}%)</strong>
                                    </div>
                                @endfor
                            </td>
                        </tr>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <div style="margin-top: 30px; border-top: 1px solid #e5e7eb; padding-top: 10px; text-align: center; color: #9ca3af; font-size: 9px;">
        <p style="margin: 0;">LOMS &mdash; Laundry Online Management System</p>
        <p style="margin: 2px 0 0 0;">Report generated automatically. Iimaan Dry Cleaner, Hargeisa, Somaliland.</p>
    </div>

</body>
</html>
