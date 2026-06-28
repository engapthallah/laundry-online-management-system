<?php

namespace Tests\Feature\Integration;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryAgentPaymentBlockTest extends TestCase
{
    use RefreshDatabase;

    public function test_delivery_agent_payment_block_flow()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $staff = $this->createStaff(['name' => 'Alice Staff']);
        $deliveryAgent = $this->createDelivery(['name' => 'Bob Delivery']);
        $service = $this->createService(['name' => 'Washing', 'price_per_kg' => 5.00]);

        // 1. New Zaad/Edahab order created → delivery_agent_id = NULL
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
            'payment_method' => 'zaad',
            'payment_phone' => '123456789',
        ]);

        if (session('errors')) {
            dd(session('errors')->getMessages());
        }
        $response->assertRedirect();

        $order = Order::where('payment_method', 'zaad')->first();
        $this->assertNotNull($order);
        $this->assertNull($order->delivery_agent_id);

        // 2. New Cash order created → delivery_agent_id assigned immediately
        $responseCash = $this->actingAs($customer)->post('/customer/orders', [
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

        if (session('errors')) {
            dd(session('errors')->getMessages());
        }
        $responseCash->assertRedirect();

        $cashOrder = Order::where('payment_method', 'cash')->first();
        $this->assertNotNull($cashOrder);
        $this->assertEquals($deliveryAgent->id, $cashOrder->delivery_agent_id);

        // 3. Delivery agent logs in → unverified Zaad/Edahab orders NOT visible anywhere
        $responseAgent = $this->actingAs($deliveryAgent)->get('/delivery/orders');
        $responseAgent->assertOk();
        $responseAgent->assertSee($cashOrder->order_number);
        $responseAgent->assertDontSee($order->order_number);

        // 4. Staff verifies payment → delivery_agent_id assigned at that moment
        $this->assertEquals($staff->id, $order->staff_id);
        $responseStaff = $this->actingAs($staff)->patch("/staff/orders/{$order->id}/verify-payment");
        $responseStaff->assertRedirect();
        
        $order = $order->fresh();
        $this->assertEquals('verified', $order->payment_status);
        $this->assertEquals($deliveryAgent->id, $order->delivery_agent_id);

        // 5. Delivery agent logs in after verification → order NOW visible
        $responseAgentAfter = $this->actingAs($deliveryAgent)->get('/delivery/orders');
        $responseAgentAfter->assertOk();
        $responseAgentAfter->assertSee($order->order_number);
        
        echo "   Delivery agent payment block flow integration test passed!\n";
    }
}
