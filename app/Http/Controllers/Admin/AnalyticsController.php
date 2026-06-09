<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AnalyticsController extends Controller
{
    /**
     * Display the analytics dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->get('period', 'thismonth');
        $from   = $request->get('from');
        $to     = $request->get('to');

        $dateRange = AnalyticsService::getDateRange($period, $from, $to);
        $start = $dateRange['start'];
        $end   = $dateRange['end'];

        $cacheKey = "analytics_{$period}_{$start->toDateString()}_{$end->toDateString()}";

        // Clear cache if refresh is requested
        if ($request->has('refresh')) {
            Cache::forget($cacheKey);
            return redirect()->route('admin.analytics.index', $request->except('refresh'));
        }

        $data = Cache::remember($cacheKey, 300, function() use ($start, $end) {
            return [
                'kpi'            => AnalyticsService::getKpiCards($start, $end),
                'revenueDay'     => AnalyticsService::getRevenueByDay($start, $end),
                'revenueMonth'   => AnalyticsService::getRevenueByMonth(Carbon::now()->year),
                'orderStatus'    => AnalyticsService::getOrdersByStatus($start, $end),
                'paymentMethod'  => AnalyticsService::getOrdersByPaymentMethod($start, $end),
                'topServices'    => AnalyticsService::getTopServices($start, $end, 15),
                'staffPerf'      => AnalyticsService::getStaffPerformance($start, $end),
                'deliveryPerf'   => AnalyticsService::getDeliveryPerformance($start, $end),
                'customerGrowth' => AnalyticsService::getCustomerGrowth(Carbon::now()->year),
                'reviewStats'    => AnalyticsService::getReviewStats($start, $end),
                'supportStats'   => AnalyticsService::getSupportStats($start, $end),
                'ordersDay'      => AnalyticsService::getOrdersByDay($start, $end),
                'topCustomers'   => AnalyticsService::getTopCustomers($start, $end),
            ];
        });

        return view('admin.analytics.index', compact('data', 'period', 'start', 'end', 'from', 'to'));
    }

    /**
     * Redirect export/pdf to print-optimized HTML view.
     */
    public function exportPdf(Request $request)
    {
        return redirect()->route('admin.analytics.printable', $request->query());
    }

    /**
     * Render the print-optimized standalone page.
     */
    public function printable(Request $request)
    {
        $period = $request->get('period', 'thismonth');
        $from   = $request->get('from');
        $to     = $request->get('to');

        $dateRange = AnalyticsService::getDateRange($period, $from, $to);
        $start = $dateRange['start'];
        $end   = $dateRange['end'];

        $cacheKey = "analytics_{$period}_{$start->toDateString()}_{$end->toDateString()}";

        $data = Cache::remember($cacheKey, 300, function() use ($start, $end) {
            return [
                'kpi'            => AnalyticsService::getKpiCards($start, $end),
                'revenueDay'     => AnalyticsService::getRevenueByDay($start, $end),
                'revenueMonth'   => AnalyticsService::getRevenueByMonth(Carbon::now()->year),
                'orderStatus'    => AnalyticsService::getOrdersByStatus($start, $end),
                'paymentMethod'  => AnalyticsService::getOrdersByPaymentMethod($start, $end),
                'topServices'    => AnalyticsService::getTopServices($start, $end, 15),
                'staffPerf'      => AnalyticsService::getStaffPerformance($start, $end),
                'deliveryPerf'   => AnalyticsService::getDeliveryPerformance($start, $end),
                'customerGrowth' => AnalyticsService::getCustomerGrowth(Carbon::now()->year),
                'reviewStats'    => AnalyticsService::getReviewStats($start, $end),
                'supportStats'   => AnalyticsService::getSupportStats($start, $end),
                'ordersDay'      => AnalyticsService::getOrdersByDay($start, $end),
                'topCustomers'   => AnalyticsService::getTopCustomers($start, $end),
            ];
        });

        return view('admin.analytics.print', compact('data', 'period', 'start', 'end', 'from', 'to'));
    }

    /**
     * Export analytics data as CSV.
     */
    public function exportCsv(Request $request)
    {
        $period = $request->get('period', 'thismonth');
        $from   = $request->get('from');
        $to     = $request->get('to');

        $dateRange = AnalyticsService::getDateRange($period, $from, $to);
        $start = $dateRange['start'];
        $end   = $dateRange['end'];

        $cacheKey = "analytics_{$period}_{$start->toDateString()}_{$end->toDateString()}";

        $data = Cache::remember($cacheKey, 300, function() use ($start, $end) {
            return [
                'kpi'            => AnalyticsService::getKpiCards($start, $end),
                'revenueDay'     => AnalyticsService::getRevenueByDay($start, $end),
                'revenueMonth'   => AnalyticsService::getRevenueByMonth(Carbon::now()->year),
                'orderStatus'    => AnalyticsService::getOrdersByStatus($start, $end),
                'paymentMethod'  => AnalyticsService::getOrdersByPaymentMethod($start, $end),
                'topServices'    => AnalyticsService::getTopServices($start, $end, 15),
                'staffPerf'      => AnalyticsService::getStaffPerformance($start, $end),
                'deliveryPerf'   => AnalyticsService::getDeliveryPerformance($start, $end),
                'customerGrowth' => AnalyticsService::getCustomerGrowth(Carbon::now()->year),
                'reviewStats'    => AnalyticsService::getReviewStats($start, $end),
                'supportStats'   => AnalyticsService::getSupportStats($start, $end),
                'ordersDay'      => AnalyticsService::getOrdersByDay($start, $end),
                'topCustomers'   => AnalyticsService::getTopCustomers($start, $end),
            ];
        });

        $filename = "loms-analytics-{$period}-" . now()->format('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->streamDownload(function() use ($data, $start, $end) {
            $file = fopen('php://output', 'w');

            // Section 1: Report Header
            fputcsv($file, ['LOMS Analytics Report']);
            fputcsv($file, ['Generated', now()->toDateTimeString()]);
            fputcsv($file, ['Period', $start->toDateString() . ' to ' . $end->toDateString()]);
            fputcsv($file, []);

            // Section 2: KPI Summary
            fputcsv($file, ['Metric', 'Value']);
            fputcsv($file, ['Total Revenue', '$' . number_format($data['kpi']['total_revenue'], 2)]);
            fputcsv($file, ['Total Orders', $data['kpi']['total_orders']]);
            fputcsv($file, ['New Customers', $data['kpi']['new_customers']]);
            fputcsv($file, ['Delivered Orders', $data['kpi']['delivered_orders']]);
            fputcsv($file, ['Average Order Value', '$' . number_format($data['kpi']['avg_order_value'], 2)]);
            fputcsv($file, ['Average Rating', number_format($data['kpi']['avg_rating'], 1) . ' / 5.0']);
            fputcsv($file, []);

            // Section 3: Daily Revenue
            fputcsv($file, ['Daily Revenue']);
            fputcsv($file, ['Date', 'Revenue']);
            foreach ($data['revenueDay']['labels'] as $index => $label) {
                fputcsv($file, [$label, '$' . number_format($data['revenueDay']['data'][$index], 2)]);
            }
            fputcsv($file, []);

            // Section 4: Orders by Status
            fputcsv($file, ['Orders by Status']);
            fputcsv($file, ['Status', 'Count']);
            foreach ($data['orderStatus'] as $status => $count) {
                fputcsv($file, [ucfirst(str_replace('_', ' ', $status)), $count]);
            }
            fputcsv($file, []);

            // Section 5: Top Services
            fputcsv($file, ['Top Services']);
            fputcsv($file, ['Service', 'Orders', 'Revenue']);
            foreach ($data['topServices'] as $service) {
                fputcsv($file, [$service['name'], $service['total_orders'], '$' . number_format($service['total_revenue'], 2)]);
            }
            fputcsv($file, []);

            // Section 6: Staff Performance
            fputcsv($file, ['Staff Performance']);
            fputcsv($file, ['Staff Name', 'Orders Handled', 'Completion Rate']);
            foreach ($data['staffPerf'] as $staff) {
                fputcsv($file, [$staff['name'], $staff['total_orders_handled'], number_format($staff['completion_rate'], 1) . '%']);
            }

            fclose($file);
        }, $filename, $headers);
    }

    /**
     * Get AJAX revenue chart data.
     */
    public function revenueData(Request $request)
    {
        $period = $request->get('period', 'thismonth');
        $from   = $request->get('from');
        $to     = $request->get('to');

        $dateRange = AnalyticsService::getDateRange($period, $from, $to);
        $start = $dateRange['start'];
        $end   = $dateRange['end'];

        $revenueDay = AnalyticsService::getRevenueByDay($start, $end);

        return response()->json($revenueDay);
    }

    /**
     * Get AJAX orders chart data.
     */
    public function ordersData(Request $request)
    {
        $period = $request->get('period', 'thismonth');
        $from   = $request->get('from');
        $to     = $request->get('to');

        $dateRange = AnalyticsService::getDateRange($period, $from, $to);
        $start = $dateRange['start'];
        $end   = $dateRange['end'];

        $ordersStatus = AnalyticsService::getOrdersByStatus($start, $end);
        $ordersDay = AnalyticsService::getOrdersByDay($start, $end);

        return response()->json([
            'status' => $ordersStatus,
            'volume' => $ordersDay
        ]);
    }
}
