<?php

namespace Tests\Feature\Staff;

use App\Models\Order;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class StatusUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_staff_can_view_assigned_orders()
    {
        $staff = $this->createStaff();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, ['staff_id' => $staff->id]);

        $response = $this->actingAs($staff)->get('/staff/orders');

        $response->assertStatus(200);
    }

    public function test_staff_can_update_order_status_sequentially()
    {
        $staff = $this->createStaff();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'staff_id' => $staff->id,
            'status' => 'processing',
        ]);

        // processing -> ready_for_delivery
        $response = $this->actingAs($staff)->patch("/staff/orders/{$order->id}/status", [
            'status' => 'ready_for_delivery',
        ]);
        $this->assertEquals('ready_for_delivery', $order->fresh()->status);
        $response->assertRedirect(route('staff.orders.show', $order->id));
    }

    public function test_staff_cannot_skip_status()
    {
        $staff = $this->createStaff();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'staff_id' => $staff->id,
            'status' => 'pending_pickup',
        ]);

        // Attempting to transition from pending_pickup directly to ready_for_delivery (skipping processing)
        $response = $this->actingAs($staff)->patch("/staff/orders/{$order->id}/status", [
            'status' => 'ready_for_delivery',
        ]);

        $this->assertEquals('pending_pickup', $order->fresh()->status);
        $response->assertStatus(403);
    }

    public function test_staff_cannot_access_unassigned_order()
    {
        $staffA = $this->createStaff();
        $staffB = $this->createStaff();
        $customer = $this->createCustomer();
        $orderOfB = $this->createOrder($customer, ['staff_id' => $staffB->id]);

        $response = $this->actingAs($staffA)->get("/staff/orders/{$orderOfB->id}");

        $response->assertStatus(403);
    }

    public function test_status_update_creates_customer_notification()
    {
        $staff = $this->createStaff();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'staff_id' => $staff->id,
            'status' => 'processing',
        ]);

        $response = $this->actingAs($staff)->patch("/staff/orders/{$order->id}/status", [
            'status' => 'ready_for_delivery',
        ]);

        $notification = Notification::where('user_id', $customer->id)
            ->where('order_id', $order->id)
            ->latest()
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('ready', $notification->message);
    }

    public function test_staff_cannot_set_delivered_status()
    {
        $staff = $this->createStaff();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'staff_id' => $staff->id,
            'status' => 'processing',
        ]);

        $response = $this->actingAs($staff)->patch("/staff/orders/{$order->id}/status", [
            'status' => 'delivered',
        ]);

        $this->assertNotEquals('delivered', $order->fresh()->status);
    }
}
