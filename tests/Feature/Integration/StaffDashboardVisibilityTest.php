<?php

namespace Tests\Feature\Integration;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffDashboardVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_dashboard_and_order_visibility()
    {
        $staff = $this->createStaff();
        $customer = $this->createCustomer();

        // Create orders with different statuses assigned to staff
        $orderPending = $this->createOrder($customer, ['staff_id' => $staff->id, 'status' => 'pending_pickup']);
        $orderPickedUp = $this->createOrder($customer, ['staff_id' => $staff->id, 'status' => 'picked_up_from_customer']);
        $orderDeliveredToLaundry = $this->createOrder($customer, ['staff_id' => $staff->id, 'status' => 'delivered_to_laundry']);
        $orderProcessing = $this->createOrder($customer, ['staff_id' => $staff->id, 'status' => 'processing']);
        $orderReady = $this->createOrder($customer, ['staff_id' => $staff->id, 'status' => 'ready_for_delivery']);

        // 1. Staff dashboard active orders count includes delivered_to_laundry + processing
        $responseDash = $this->actingAs($staff)->get('/staff/dashboard');
        $responseDash->assertOk();
        $responseDash->assertViewHas('activeOrdersCount', 2);
        $responseDash->assertViewHas('totalAssigned', 5);

        // 2. Staff "My Orders" list shows orders from pending_pickup through ready_for_delivery
        $responseIndex = $this->actingAs($staff)->get('/staff/orders');
        $responseIndex->assertOk();
        $responseIndex->assertSee($orderPending->order_number);
        $responseIndex->assertSee($orderPickedUp->order_number);
        $responseIndex->assertSee($orderDeliveredToLaundry->order_number);
        $responseIndex->assertSee($orderProcessing->order_number);
        $responseIndex->assertSee($orderReady->order_number);

        // Filter status=active should show active orders (delivered_to_laundry and processing)
        $responseActive = $this->actingAs($staff)->get('/staff/orders?status=active');
        $responseActive->assertOk();
        $responseActive->assertSee($orderDeliveredToLaundry->order_number);
        $responseActive->assertSee($orderProcessing->order_number);
        $responseActive->assertDontSee($orderPending->order_number);
        $responseActive->assertDontSee($orderReady->order_number);

        // 3. Staff order detail shows correct informational messages for early lifecycle statuses
        $respPending = $this->actingAs($staff)->get("/staff/orders/{$orderPending->id}");
        $respPending->assertSee('Waiting for Pickup');

        $respPickedUp = $this->actingAs($staff)->get("/staff/orders/{$orderPickedUp->id}");
        $respPickedUp->assertSee('Items Collected from Customer');

        $respDelivered = $this->actingAs($staff)->get("/staff/orders/{$orderDeliveredToLaundry->id}");
        $respDelivered->assertSee('Items Arrived at Laundry Shop');

        // 4. Staff order detail shows "Mark Ready for Delivery" button when status = processing
        $respProcessing = $this->actingAs($staff)->get("/staff/orders/{$orderProcessing->id}");
        $respProcessing->assertSee('Mark Ready for Delivery');
        $respProcessing->assertSee('btn-info text-dark');

        // 5. Staff sidebar badge shows correct active order count (which is 2)
        $responseDash->assertSee('Active Orders');
        
        echo "   Staff dashboard and order visibility integration test passed!\n";
    }
}
