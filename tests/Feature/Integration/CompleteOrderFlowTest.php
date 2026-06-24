<?php

namespace Tests\Feature\Integration;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\Review;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CompleteOrderFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_complete_order_lifecycle_from_placement_to_delivery()
    {
        // STEP 1 — Setup:
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        // Create active staff and delivery agent so round-robin auto-assignment succeeds
        $staff = $this->createStaff(['name' => 'Alice Staff']);
        $deliveryAgent = $this->createDelivery(['name' => 'Bob Delivery']);
        $service = $this->createService(['name' => 'Washing', 'price_per_kg' => 5.00]);

        // STEP 2 — Customer places order:
        $response = $this->actingAs($customer)->post('/customer/orders', [
            'services' => [
                [
                    'selected' => '1',
                    'service_id' => $service->id,
                    'quantity' => 1,
                    'weight_kg' => 2.5,
                ]
            ],
            'pickup_address' => '123 Test Street',
            'delivery_address' => '456 Test Ave',
            'pickup_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'delivery_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'payment_method' => 'cash',
        ]);

        $order = Order::latest()->first();
        $this->assertNotNull($order);
        
        // Assert initial status and auto-assigned staff/agent
        $this->assertEquals('pending_pickup', $order->status);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertEquals($staff->id, $order->staff_id);
        $this->assertEquals($deliveryAgent->id, $order->delivery_agent_id);

        // Assert 2 Notifications created (customer + admin)
        $this->assertTrue(Notification::where('user_id', $customer->id)->where('order_id', $order->id)->exists());
        $this->assertTrue(Notification::where('user_id', $admin->id)->where('order_id', $order->id)->exists());

        // STEP 3 — Delivery Agent: pending_pickup -> picked_up_from_customer
        $response = $this->actingAs($deliveryAgent)->patch("/delivery/orders/{$order->id}/status");
        $this->assertEquals('picked_up_from_customer', $order->fresh()->status);

        // STEP 4 — Delivery Agent: picked_up_from_customer -> delivered_to_laundry (auto-advances to processing)
        $response = $this->actingAs($deliveryAgent)->patch("/delivery/orders/{$order->id}/status");
        $this->assertEquals('processing', $order->fresh()->status);

        // STEP 5 — Staff: processing -> ready_for_delivery
        $response = $this->actingAs($staff)->patch("/staff/orders/{$order->id}/status", [
            'status' => 'ready_for_delivery',
        ]);
        $this->assertEquals('ready_for_delivery', $order->fresh()->status);

        // STEP 6 — Delivery Agent: ready_for_delivery -> picked_up_from_laundry
        $response = $this->actingAs($deliveryAgent)->patch("/delivery/orders/{$order->id}/status");
        $this->assertEquals('picked_up_from_laundry', $order->fresh()->status);

        // STEP 7 — Delivery Agent: picked_up_from_laundry -> on_the_way
        $response = $this->actingAs($deliveryAgent)->patch("/delivery/orders/{$order->id}/status");
        $this->assertEquals('on_the_way', $order->fresh()->status);

        // STEP 8 — Delivery Agent: on_the_way -> delivered
        $response = $this->actingAs($deliveryAgent)->patch("/delivery/orders/{$order->id}/status");
        $this->assertEquals('delivered', $order->fresh()->status);
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertEquals('completed', $order->fresh()->payment->status);

        // STEP 9 — Customer submits review:
        $response = $this->actingAs($customer)->post('/customer/reviews', [
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Excellent service!',
        ]);

        // STEP 10 — Final assertions:
        $this->assertDatabaseHas('reviews', [
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'rating' => 5,
            'comment' => 'Excellent service!',
        ]);

        $this->assertEquals('delivered', Order::find($order->id)->status);
        $this->assertEquals('completed', Payment::where('order_id', $order->id)->first()->status);
        $this->assertTrue(Review::where('order_id', $order->id)->exists());

        echo "   Complete order lifecycle test passed!\n";
    }
}
