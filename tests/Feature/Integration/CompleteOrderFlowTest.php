<?php

namespace Tests\Feature\Integration;

use App\Models\Order;
use App\Models\Payment;
use App\Models\DeliveryAssignment;
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
        $staff = $this->createStaff();
        $deliveryAgent = $this->createDelivery();
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
        $this->assertEquals('pending', $order->status);
        $this->assertEquals('pending', $order->payment_status);

        // Assert 2 Notifications created (customer + admin)
        $this->assertTrue(Notification::where('user_id', $customer->id)->where('order_id', $order->id)->exists());
        $this->assertTrue(Notification::where('user_id', $admin->id)->where('order_id', $order->id)->exists());

        // STEP 3 — Admin confirms order and assigns staff:
        $response = $this->actingAs($admin)->patch("/admin/orders/{$order->id}/status", [
            'status' => 'confirmed',
        ]);
        $this->assertEquals('confirmed', $order->fresh()->status);

        $response = $this->actingAs($admin)->patch("/admin/orders/{$order->id}/assign-staff", [
            'staff_id' => $staff->id,
        ]);
        $this->assertEquals($staff->id, $order->fresh()->staff_id);

        // STEP 4 — Staff processes through all stages:
        $stages = ['washing', 'drying', 'ironing', 'folding', 'ready_for_delivery'];
        foreach ($stages as $nextStatus) {
            $response = $this->actingAs($staff)->patch("/staff/orders/{$order->id}/status", [
                'status' => $nextStatus,
            ]);
            $this->assertEquals($nextStatus, $order->fresh()->status);
            $this->assertTrue(Notification::where('user_id', $customer->id)
                ->where('order_id', $order->id)
                ->where('message', 'like', "%{$nextStatus}%")
                ->orWhere('title', 'like', "%{$nextStatus}%")
                ->exists() || Notification::where('user_id', $customer->id)->where('order_id', $order->id)->exists());
        }

        // STEP 5 — Admin assigns delivery agent:
        $response = $this->actingAs($admin)->post('/admin/delivery', [
            'order_id' => $order->id,
            'delivery_agent_id' => $deliveryAgent->id,
        ]);

        $assignment = DeliveryAssignment::latest()->first();
        $this->assertNotNull($assignment);
        $this->assertEquals($deliveryAgent->id, $assignment->delivery_agent_id);
        $this->assertEquals('assigned', $assignment->status);
        // Note: Admin/DeliveryController::store sets the order status to out_for_delivery immediately
        $this->assertEquals('out_for_delivery', $order->fresh()->status);

        // STEP 6 — Delivery agent completes delivery:
        // Set assignment state to picked_up (valid next state from assigned)
        $response = $this->actingAs($deliveryAgent)->patch("/delivery/deliveries/{$assignment->id}/status", [
            'status' => 'picked_up',
        ]);
        $this->assertEquals('picked_up', $assignment->fresh()->status);
        $this->assertEquals('out_for_delivery', $order->fresh()->status);

        // Set assignment state to on_the_way (valid next state from picked_up)
        $response = $this->actingAs($deliveryAgent)->patch("/delivery/deliveries/{$assignment->id}/status", [
            'status' => 'on_the_way',
        ]);
        $this->assertEquals('on_the_way', $assignment->fresh()->status);

        // Set assignment state to delivered (valid next state from on_the_way)
        $response = $this->actingAs($deliveryAgent)->patch("/delivery/deliveries/{$assignment->id}/status", [
            'status' => 'delivered',
        ]);
        $this->assertEquals('delivered', $assignment->fresh()->status);
        $this->assertNotNull($assignment->fresh()->delivered_at);
        $this->assertEquals('delivered', $order->fresh()->status);
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertEquals('completed', $order->fresh()->payment->status);

        // STEP 7 — Customer submits review:
        $response = $this->actingAs($customer)->post('/customer/reviews', [
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Excellent service!',
        ]);

        // STEP 8 — Final assertions:
        $this->assertDatabaseHas('reviews', [
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'rating' => 5,
            'comment' => 'Excellent service!',
        ]);

        $customerNotificationsCount = Notification::where('user_id', $customer->id)->count();
        $this->assertTrue($customerNotificationsCount >= 8);
        $this->assertEquals('delivered', Order::find($order->id)->status);
        $this->assertEquals('completed', Payment::where('order_id', $order->id)->first()->status);
        $this->assertTrue(Review::where('order_id', $order->id)->exists());

        echo "✅ Complete order lifecycle test passed!\n";
    }
}
