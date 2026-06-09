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
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($staff)->patch("/staff/orders/{$order->id}/status", [
            'status' => 'washing',
        ]);

        $this->assertEquals('washing', $order->fresh()->status);
        $response->assertRedirect(route('staff.orders.show', $order->id));
    }

    public function test_staff_cannot_skip_status()
    {
        $staff = $this->createStaff();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'staff_id' => $staff->id,
            'status' => 'confirmed',
        ]);

        // Attempting to transition from confirmed directly to folding (skipping washing, drying, ironing)
        $response = $this->actingAs($staff)->patch("/staff/orders/{$order->id}/status", [
            'status' => 'folding',
        ]);

        $this->assertEquals('confirmed', $order->fresh()->status);
        $response->assertSessionHas('error');
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
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($staff)->patch("/staff/orders/{$order->id}/status", [
            'status' => 'washing',
        ]);

        $notification = Notification::where('user_id', $customer->id)
            ->where('order_id', $order->id)
            ->latest()
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('washed', $notification->message);
    }

    public function test_staff_cannot_set_delivered_status()
    {
        $staff = $this->createStaff();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'staff_id' => $staff->id,
            'status' => 'folding',
        ]);

        $response = $this->actingAs($staff)->patch("/staff/orders/{$order->id}/status", [
            'status' => 'delivered',
        ]);

        $this->assertNotEquals('delivered', $order->fresh()->status);
    }
}
