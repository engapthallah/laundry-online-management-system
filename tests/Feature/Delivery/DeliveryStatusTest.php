<?php

namespace Tests\Feature\Delivery;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DeliveryStatusTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_delivery_agent_can_view_assignments()
    {
        $delivery = $this->createDelivery();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'delivery_agent_id' => $delivery->id,
            'status' => 'pending_pickup',
        ]);

        $response = $this->actingAs($delivery)->get('/delivery/orders');

        $response->assertStatus(200);
        $response->assertSee($order->order_number);
    }

    public function test_delivery_agent_can_mark_as_picked_up_from_customer()
    {
        $delivery = $this->createDelivery();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'delivery_agent_id' => $delivery->id,
            'status' => 'pending_pickup',
        ]);

        $response = $this->actingAs($delivery)->patch("/delivery/orders/{$order->id}/status");

        $this->assertEquals('picked_up_from_customer', $order->fresh()->status);
        $response->assertRedirect(route('delivery.orders.show', $order->id));
    }

    public function test_delivery_agent_can_mark_as_delivered_to_laundry_which_auto_advances_to_processing()
    {
        $delivery = $this->createDelivery();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'delivery_agent_id' => $delivery->id,
            'status' => 'picked_up_from_customer',
        ]);

        $response = $this->actingAs($delivery)->patch("/delivery/orders/{$order->id}/status");

        // delivered_to_laundry auto-advances to processing
        $this->assertEquals('processing', $order->fresh()->status);
        $response->assertRedirect(route('delivery.orders.show', $order->id));
    }

    public function test_delivery_agent_can_mark_as_picked_up_from_laundry()
    {
        $delivery = $this->createDelivery();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'delivery_agent_id' => $delivery->id,
            'status' => 'ready_for_delivery',
        ]);

        $response = $this->actingAs($delivery)->patch("/delivery/orders/{$order->id}/status");

        $this->assertEquals('picked_up_from_laundry', $order->fresh()->status);
        $response->assertRedirect(route('delivery.orders.show', $order->id));
    }

    public function test_delivery_agent_can_mark_as_delivered()
    {
        $delivery = $this->createDelivery();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'delivery_agent_id' => $delivery->id,
            'status' => 'on_the_way',
        ]);

        $response = $this->actingAs($delivery)->patch("/delivery/orders/{$order->id}/status");

        $this->assertEquals('delivered', $order->fresh()->status);
        $response->assertRedirect(route('delivery.orders.index'));
    }

    public function test_cash_payment_confirmed_on_delivery()
    {
        $delivery = $this->createDelivery();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'delivery_agent_id' => $delivery->id,
            'status' => 'on_the_way',
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);

        $response = $this->actingAs($delivery)->patch("/delivery/orders/{$order->id}/status");

        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertEquals('completed', $order->payment->fresh()->status);
        $this->assertNotNull($order->payment->fresh()->paid_at);
    }

    public function test_delivery_agent_cannot_skip_status()
    {
        $delivery = $this->createDelivery();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'delivery_agent_id' => $delivery->id,
            'status' => 'processing', // Cannot advance from processing
        ]);

        $response = $this->actingAs($delivery)->patch("/delivery/orders/{$order->id}/status");

        $this->assertEquals('processing', $order->fresh()->status);
        $response->assertStatus(403);
    }

    public function test_delivery_agent_cannot_access_others_assignment()
    {
        $agentA = $this->createDelivery();
        $agentB = $this->createDelivery();
        $customer = $this->createCustomer();
        $orderOfB = $this->createOrder($customer, [
            'delivery_agent_id' => $agentB->id,
        ]);

        $response = $this->actingAs($agentA)->get("/delivery/orders/{$orderOfB->id}");

        $response->assertStatus(403);
    }

    public function test_delivery_agent_visibility_rules()
    {
        $delivery = $this->createDelivery();
        $customer = $this->createCustomer();

        // 1. Zaad order with payment_status = pending_verification should NOT be visible
        $unverifiedZaadOrder = $this->createOrder($customer, [
            'delivery_agent_id' => $delivery->id,
            'status' => 'pending_pickup',
            'payment_method' => 'zaad',
            'payment_status' => 'pending_verification',
        ]);

        // 2. Edahab order with payment_status = rejected should NOT be visible
        $rejectedEdahabOrder = $this->createOrder($customer, [
            'delivery_agent_id' => $delivery->id,
            'status' => 'pending_pickup',
            'payment_method' => 'edahab',
            'payment_status' => 'rejected',
        ]);

        // 3. Cash order should be visible
        $cashOrder = $this->createOrder($customer, [
            'delivery_agent_id' => $delivery->id,
            'status' => 'pending_pickup',
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);

        // 4. Zaad order with payment_status = verified should be visible
        $verifiedZaadOrder = $this->createOrder($customer, [
            'delivery_agent_id' => $delivery->id,
            'status' => 'pending_pickup',
            'payment_method' => 'zaad',
            'payment_status' => 'verified',
        ]);

        $response = $this->actingAs($delivery)->get('/delivery/orders');

        $response->assertStatus(200);
        $response->assertDontSee($unverifiedZaadOrder->order_number);
        $response->assertDontSee($rejectedEdahabOrder->order_number);
        $response->assertSee($cashOrder->order_number);
        $response->assertSee($verifiedZaadOrder->order_number);
    }

    public function test_delivery_agent_dashboard_and_show_route_rules()
    {
        $delivery = $this->createDelivery();
        $customer = $this->createCustomer();

        // 1. Zaad order with payment_status = pending_verification should NOT be in dashboard
        $unverifiedZaadOrder = $this->createOrder($customer, [
            'delivery_agent_id' => $delivery->id,
            'status' => 'pending_pickup',
            'payment_method' => 'zaad',
            'payment_status' => 'pending_verification',
        ]);

        $response = $this->actingAs($delivery)->get('/delivery/dashboard');
        $response->assertStatus(200);
        $this->assertEquals(0, $response->viewData('totalAssigned'));
        $this->assertEquals(0, $response->viewData('activeDeliveriesCount'));
        $this->assertTrue($response->viewData('activeAssignments')->isEmpty());

        // 2. Direct order detail access should be blocked
        $responseShow = $this->actingAs($delivery)->get("/delivery/orders/{$unverifiedZaadOrder->id}");
        $responseShow->assertStatus(403);
    }
}


