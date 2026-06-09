<?php

namespace Tests\Feature\Delivery;

use App\Models\Order;
use App\Models\Payment;
use App\Models\DeliveryAssignment;
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
        $order = $this->createOrder($customer);
        
        $assignment = DeliveryAssignment::create([
            'order_id' => $order->id,
            'delivery_agent_id' => $delivery->id,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        $response = $this->actingAs($delivery)->get('/delivery/deliveries');

        $response->assertStatus(200);
    }

    public function test_delivery_agent_can_mark_as_picked_up()
    {
        $delivery = $this->createDelivery();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, ['status' => 'ready_for_delivery']);
        
        $assignment = DeliveryAssignment::create([
            'order_id' => $order->id,
            'delivery_agent_id' => $delivery->id,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        $response = $this->actingAs($delivery)->patch("/delivery/deliveries/{$assignment->id}/status", [
            'status' => 'picked_up',
        ]);

        $this->assertEquals('picked_up', $assignment->fresh()->status);
        $this->assertNotNull($assignment->fresh()->picked_up_at);
        $this->assertEquals('out_for_delivery', $order->fresh()->status);
        $response->assertRedirect(route('delivery.deliveries.show', $assignment->id));
    }

    public function test_delivery_agent_can_mark_as_delivered()
    {
        $delivery = $this->createDelivery();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, ['status' => 'out_for_delivery']);
        
        $assignment = DeliveryAssignment::create([
            'order_id' => $order->id,
            'delivery_agent_id' => $delivery->id,
            'status' => 'on_the_way',
            'assigned_at' => now()->subHours(2),
            'picked_up_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($delivery)->patch("/delivery/deliveries/{$assignment->id}/status", [
            'status' => 'delivered',
        ]);

        $this->assertEquals('delivered', $assignment->fresh()->status);
        $this->assertNotNull($assignment->fresh()->delivered_at);
        $this->assertEquals('delivered', $order->fresh()->status);
        $response->assertRedirect(route('delivery.deliveries.index'));
    }

    public function test_cash_payment_confirmed_on_delivery()
    {
        $delivery = $this->createDelivery();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'status' => 'out_for_delivery',
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);
        
        $assignment = DeliveryAssignment::create([
            'order_id' => $order->id,
            'delivery_agent_id' => $delivery->id,
            'status' => 'on_the_way',
            'assigned_at' => now()->subHours(2),
            'picked_up_at' => now()->subHour(),
        ]);

        $response = $this->actingAs($delivery)->patch("/delivery/deliveries/{$assignment->id}/status", [
            'status' => 'delivered',
        ]);

        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertEquals('completed', $order->payment->fresh()->status);
        $this->assertNotNull($order->payment->fresh()->paid_at);
    }

    public function test_delivery_agent_cannot_skip_status()
    {
        $delivery = $this->createDelivery();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, ['status' => 'ready_for_delivery']);
        
        $assignment = DeliveryAssignment::create([
            'order_id' => $order->id,
            'delivery_agent_id' => $delivery->id,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        // Attempting to jump from assigned to delivered, bypassing picked_up and on_the_way
        $response = $this->actingAs($delivery)->patch("/delivery/deliveries/{$assignment->id}/status", [
            'status' => 'delivered',
        ]);

        $this->assertEquals('assigned', $assignment->fresh()->status);
        $response->assertSessionHas('error');
    }

    public function test_delivery_agent_cannot_access_others_assignment()
    {
        $agentA = $this->createDelivery();
        $agentB = $this->createDelivery();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer);
        
        $assignmentOfB = DeliveryAssignment::create([
            'order_id' => $order->id,
            'delivery_agent_id' => $agentB->id,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);

        $response = $this->actingAs($agentA)->get("/delivery/deliveries/{$assignmentOfB->id}");

        $response->assertStatus(403);
    }
}
