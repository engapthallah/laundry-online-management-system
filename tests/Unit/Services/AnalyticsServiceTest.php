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
            'confirmed_orders',
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
        $this->createOrder($customer, ['status' => 'pending_pickup']);
        $this->createOrder($customer, ['status' => 'processing']);
        $this->createOrder($customer, ['status' => 'delivered']);

        $start = Carbon::now()->subDay()->startOfDay();
        $end = Carbon::now()->addDay()->endOfDay();

        $stats = AnalyticsService::getOrdersByStatus($start, $end);

        $this->assertEquals(1, $stats['pending_pickup']);
        $this->assertEquals(1, $stats['processing']);
        $this->assertEquals(1, $stats['delivered']);
        $this->assertEquals(0, $stats['cancelled']);
    }

    public function test_delivery_performance_returns_correct_metrics()
    {
        $deliveryAgent = $this->createDelivery();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'delivery_agent_id' => $deliveryAgent->id,
            'status' => 'delivered',
            'delivery_time' => Carbon::now()->addDay(),
        ]);

        $assignment = \App\Models\DeliveryAssignment::create([
            'order_id' => $order->id,
            'delivery_agent_id' => $deliveryAgent->id,
            'status' => 'delivered',
            'assigned_at' => Carbon::now()->subHour(),
            'delivered_at' => Carbon::now(),
        ]);

        $start = Carbon::now()->subDays(2)->startOfDay();
        $end = Carbon::now()->addDays(2)->endOfDay();

        $performance = AnalyticsService::getDeliveryPerformance($start, $end);

        $this->assertNotEmpty($performance);
        $this->assertEquals($deliveryAgent->name, $performance[0]['name']);
        $this->assertEquals(1, $performance[0]['total_assigned']);
        $this->assertEquals(1, $performance[0]['total_delivered']);
        $this->assertEquals(100.0, $performance[0]['delivery_rate']);
        $this->assertEquals(100.0, $performance[0]['on_time_rate']);
    }
}
