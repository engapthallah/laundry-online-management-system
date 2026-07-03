<?php

namespace Tests\Feature\Staff;

use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_dashboard_loads_normally_with_default_orders()
    {
        $staff = $this->createStaff();
        $customer = $this->createCustomer();
        
        // Active orders that should show by default
        $order1 = $this->createOrder($customer, [
            'staff_id' => $staff->id,
            'status' => 'processing',
        ]);
        // A non-active order that should NOT show by default
        $order2 = $this->createOrder($customer, [
            'staff_id' => $staff->id,
            'status' => 'pending_pickup',
        ]);

        $response = $this->actingAs($staff)->get('/staff/dashboard');

        $response->assertStatus(200);
        $response->assertSee($order1->order_number);
        $response->assertDontSee($order2->order_number);
    }

    public function test_staff_dashboard_can_filter_orders_by_search_query()
    {
        $staff = $this->createStaff();
        $customer = $this->createCustomer();
        
        $order1 = $this->createOrder($customer, [
            'staff_id' => $staff->id,
            'status' => 'processing',
            'order_number' => 'LOMS-TESTMATCH',
        ]);
        $order2 = $this->createOrder($customer, [
            'staff_id' => $staff->id,
            'status' => 'processing',
            'order_number' => 'LOMS-TESTNOMATCH',
        ]);

        $response = $this->actingAs($staff)->get('/staff/dashboard?search=TESTMATCH');

        $response->assertStatus(200);
        $response->assertSee($order1->order_number);
        $response->assertDontSee($order2->order_number);
    }

    public function test_staff_dashboard_filter_does_not_affect_overall_kpi_stats()
    {
        $staff = $this->createStaff();
        $customer = $this->createCustomer();
        
        $order = $this->createOrder($customer, [
            'staff_id' => $staff->id,
            'status' => 'processing',
            'order_number' => 'LOMS-TESTMATCH',
        ]);

        // Filter for something that returns no matches
        $response = $this->actingAs($staff)->get('/staff/dashboard?search=NOMATCHINGORDER');

        $response->assertStatus(200);
        $response->assertSee('No Orders Found');
        
        // Assert the stats cards are still loaded and show the correct overall counts
        $response->assertSee('Total Assigned');
        // Total Assigned card count is 1
        $response->assertSee('1</h3>', false);
    }
}
