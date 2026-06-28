<?php

namespace Tests\Feature\Customer;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class OrderPlacementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_customer_can_view_order_create_form()
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)->get('/customer/orders/create');

        $response->assertStatus(200);
    }

    public function test_customer_can_place_order_with_cash_payment()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $service = $this->createService();

        $response = $this->actingAs($customer)->post('/customer/orders', [
            'services' => [
                [
                    'selected' => '1',
                    'service_id' => $service->id,
                    'quantity' => 2,
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
        $this->assertEquals('pending_pickup', $order->status);
        
        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('pending', $payment->status);
        $this->assertEquals('cash', $payment->payment_method);

        // Assert Notifications created for customer and admin
        $this->assertTrue(Notification::where('user_id', $customer->id)->where('order_id', $order->id)->exists());
        $this->assertTrue(Notification::where('user_id', $admin->id)->where('order_id', $order->id)->exists());

        $response->assertRedirect(route('customer.orders.show', $order->id));
    }

    public function test_customer_can_place_order_with_zaad_payment()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $service = $this->createService();

        $response = $this->actingAs($customer)->post('/customer/orders', [
            'services' => [
                [
                    'selected' => '1',
                    'service_id' => $service->id,
                    'quantity' => 1,
                    'weight_kg' => 1.5,
                ]
            ],
            'pickup_address' => '123 Test Street',
            'delivery_address' => '456 Test Ave',
            'pickup_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'delivery_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'payment_method' => 'zaad',
            'payment_phone' => '12345678',
        ]);

        $order = Order::latest()->first();
        $this->assertNotNull($order);
        
        $payment = Payment::where('order_id', $order->id)->first();
        $this->assertNotNull($payment);
        $this->assertEquals('zaad', $payment->payment_method);
        $this->assertStringContainsString('MOBILEPAY-12345678', $payment->transaction_reference);
    }

    public function test_order_number_is_generated_automatically()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $service = $this->createService();

        $this->actingAs($customer)->post('/customer/orders', [
            'services' => [
                [
                    'selected' => '1',
                    'service_id' => $service->id,
                    'quantity' => 1,
                    'weight_kg' => 1.0,
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
        $this->assertStringStartsWith('LOMS-', $order->order_number);
    }

    public function test_order_requires_at_least_one_service()
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)->post('/customer/orders', [
            'services' => [],
            'pickup_address' => '123 Test Street',
            'delivery_address' => '456 Test Ave',
            'pickup_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'delivery_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'payment_method' => 'cash',
        ]);

        $response->assertSessionHasErrors(['services']);
    }

    public function test_order_requires_pickup_address()
    {
        $customer = $this->createCustomer();
        $service = $this->createService();

        $response = $this->actingAs($customer)->post('/customer/orders', [
            'services' => [
                [
                    'selected' => '1',
                    'service_id' => $service->id,
                    'quantity' => 1,
                ]
            ],
            'pickup_address' => '',
            'delivery_address' => '456 Test Ave',
            'pickup_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'delivery_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'payment_method' => 'cash',
        ]);

        $response->assertSessionHasErrors(['pickup_address']);
    }

    public function test_order_requires_valid_payment_method()
    {
        $customer = $this->createCustomer();
        $service = $this->createService();

        $response = $this->actingAs($customer)->post('/customer/orders', [
            'services' => [
                [
                    'selected' => '1',
                    'service_id' => $service->id,
                    'quantity' => 1,
                ]
            ],
            'pickup_address' => '123 Street',
            'delivery_address' => '456 Test Ave',
            'pickup_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'delivery_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'payment_method' => 'bitcoin',
        ]);

        $response->assertSessionHasErrors(['payment_method']);
    }

    public function test_order_pickup_time_must_be_in_future()
    {
        $customer = $this->createCustomer();
        $service = $this->createService();

        $response = $this->actingAs($customer)->post('/customer/orders', [
            'services' => [
                [
                    'selected' => '1',
                    'service_id' => $service->id,
                    'quantity' => 1,
                ]
            ],
            'pickup_address' => '123 Street',
            'delivery_address' => '456 Test Ave',
            'pickup_time' => now()->subDay()->format('Y-m-d H:i:s'),
            'delivery_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'payment_method' => 'cash',
        ]);

        $response->assertSessionHasErrors(['pickup_time']);
    }

    public function test_customer_can_view_their_own_orders()
    {
        $customer = $this->createCustomer();
        $order1 = $this->createOrder($customer, ['order_number' => 'LOMS-ORDER-ONE']);
        $order2 = $this->createOrder($customer, ['order_number' => 'LOMS-ORDER-TWO']);

        $response = $this->actingAs($customer)->get('/customer/orders');

        $response->assertStatus(200);
        $response->assertSee('LOMS-ORDER-ONE');
        $response->assertSee('LOMS-ORDER-TWO');
    }

    public function test_customer_cannot_view_other_customers_orders()
    {
        $customerA = $this->createCustomer();
        $customerB = $this->createCustomer();
        $orderOfB = $this->createOrder($customerB);

        $response = $this->actingAs($customerA)->get("/customer/orders/{$orderOfB->id}");

        $response->assertStatus(403);
    }

    public function test_customer_can_cancel_confirmed_order()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, ['status' => 'pending_pickup']);

        $response = $this->actingAs($customer)->post("/customer/orders/{$order->id}/cancel");

        $this->assertEquals('cancelled', $order->fresh()->status);
        $response->assertRedirect(route('customer.orders.show', $order->id));
    }

    public function test_customer_cannot_cancel_non_confirmed_order()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, ['status' => 'processing']);

        $response = $this->actingAs($customer)->post("/customer/orders/{$order->id}/cancel");

        $response->assertSessionHas('error');
    }

    public function test_customer_placing_order_with_confirmed_payment_sets_awaiting_staff_review()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $service = $this->createService();

        $response = $this->actingAs($customer)->post('/customer/orders', [
            'services' => [
                [
                    'selected' => '1',
                    'service_id' => $service->id,
                    'quantity' => 1,
                    'weight_kg' => 1.5,
                ]
            ],
            'pickup_address' => '123 Test Street',
            'delivery_address' => '456 Test Ave',
            'pickup_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'delivery_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'payment_method' => 'zaad',
            'payment_phone' => '12345678',
            'customer_payment_confirmed' => '1',
        ]);

        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('awaiting_staff_review', $order->payment_status);
    }

    public function test_customer_placing_order_without_confirmed_payment_sets_pending_verification()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $service = $this->createService();

        $response = $this->actingAs($customer)->post('/customer/orders', [
            'services' => [
                [
                    'selected' => '1',
                    'service_id' => $service->id,
                    'quantity' => 1,
                    'weight_kg' => 1.5,
                ]
            ],
            'pickup_address' => '123 Test Street',
            'delivery_address' => '456 Test Ave',
            'pickup_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'delivery_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'payment_method' => 'zaad',
            'payment_phone' => '12345678',
            'customer_payment_confirmed' => '0',
        ]);

        $order = Order::latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('pending_verification', $order->payment_status);
    }
}
