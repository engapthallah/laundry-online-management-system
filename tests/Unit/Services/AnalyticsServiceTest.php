<?php

namespace Tests\Unit\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Services\AnalyticsService;
use Carbon\Carbon;
use Tests\TestCase;

class AnalyticsServiceTest extends TestCase
{
    public function test_get_date_range_returns_today()
    {
        $range = AnalyticsService::getDateRange('today');

        $this->assertTrue($range['start']->isToday());
        $this->assertTrue($range['end']->isToday());
    }

    public function test_get_date_range_returns_last_7_days()
    {
        $range = AnalyticsService::getDateRange('last7days');

        $this->assertEquals(6, $range['start']->startOfDay()->diffInDays($range['end']->startOfDay()));
    }

    public function test_get_date_range_returns_custom_range()
    {
        $range = AnalyticsService::getDateRange('custom', '2024-01-01', '2024-01-31');

        $this->assertEquals('2024-01-01', $range['start']->format('Y-m-d'));
        $this->assertEquals('2024-01-31', $range['end']->format('Y-m-d'));
    }

    public function test_kpi_cards_returns_required_keys()
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        $kpis = AnalyticsService::getKpiCards($start, $end);

        $requiredKeys = [
            'total_revenue',
            'total_orders',
            'new_customers',
            'delivered_orders',
            'pending_orders',
            'avg_order_value',
            'avg_rating',
            'pending_support',
            'trends'
        ];

        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $kpis);
        }
    }

    public function test_revenue_by_day_returns_labels_and_data()
    {
        $start = Carbon::now()->subDays(6)->startOfDay();
        $end = Carbon::now()->endOfDay();

        $revenue = AnalyticsService::getRevenueByDay($start, $end);

        $this->assertArrayHasKey('labels', $revenue);
        $this->assertArrayHasKey('data', $revenue);
        $this->assertIsArray($revenue['labels']);
        $this->assertIsArray($revenue['data']);
        $this->assertEquals(count($revenue['labels']), count($revenue['data']));
        $this->assertEquals(7, count($revenue['labels']));
    }

    public function test_orders_by_status_returns_all_statuses()
    {
        $customer = $this->createCustomer();
        $this->createOrder($customer, ['status' => 'pending']);
        $this->createOrder($customer, ['status' => 'washing']);
        $this->createOrder($customer, ['status' => 'delivered']);

        $start = Carbon::now()->subDay()->startOfDay();
        $end = Carbon::now()->addDay()->endOfDay();

        $stats = AnalyticsService::getOrdersByStatus($start, $end);

        $this->assertEquals(1, $stats['pending']);
        $this->assertEquals(1, $stats['washing']);
        $this->assertEquals(1, $stats['delivered']);
        $this->assertEquals(0, $stats['cancelled']);
    }
}
