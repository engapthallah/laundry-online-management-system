<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class OrderTest extends TestCase
{
    public function test_order_belongs_to_customer()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer);

        $this->assertInstanceOf(User::class, $order->customer);
        $this->assertEquals('customer', $order->customer->role);
    }

    public function test_order_has_default_confirmed_status()
    {
        $customer = $this->createCustomer();
        // Create an order without passing a status, should default to 'confirmed'
        $order = Order::create([
            'order_number' => 'LOMS-' . strtoupper(Str::random(8)),
            'customer_id' => $customer->id,
            'total_price' => 25.50,
            'weight' => 5.00,
            'pickup_address' => '123 Customer St',
            'delivery_address' => '123 Customer St',
        ]);

        $this->assertEquals('confirmed', $order->fresh()->status);
    }

    public function test_order_has_default_unpaid_payment_status()
    {
        $customer = $this->createCustomer();
        // Create an order without passing a payment_status, should default to 'pending'
        $order = Order::create([
            'order_number' => 'LOMS-' . strtoupper(Str::random(8)),
            'customer_id' => $customer->id,
            'total_price' => 25.50,
            'weight' => 5.00,
            'pickup_address' => '123 Customer St',
            'delivery_address' => '123 Customer St',
        ]);

        $this->assertEquals('pending', $order->fresh()->payment_status);
    }

    public function test_order_has_many_order_items()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer);

        // createOrder creates 1 order item by default. We create a second one here.
        $service = $this->createService();
        OrderItem::create([
            'order_id' => $order->id,
            'service_id' => $service->id,
            'quantity' => 1,
            'price' => 5.00,
        ]);

        $this->assertEquals(2, $order->fresh()->orderItems->count());
    }

    public function test_order_has_one_payment()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer);

        $this->assertInstanceOf(Payment::class, $order->payment);
    }

    public function test_order_number_is_unique()
    {
        $customer = $this->createCustomer();
        $order1 = $this->createOrder($customer);
        $order2 = $this->createOrder($customer);

        $this->assertNotEquals($order1->order_number, $order2->order_number);
    }

    public function test_order_belongs_to_staff_when_assigned()
    {
        $customer = $this->createCustomer();
        $staff = $this->createStaff();
        $order = $this->createOrder($customer, ['staff_id' => $staff->id]);

        $this->assertInstanceOf(User::class, $order->staff);
        $this->assertEquals('staff', $order->staff->role);
    }

    public function test_order_staff_is_null_when_not_assigned()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, ['staff_id' => null]);

        $this->assertNull($order->staff);
    }
}
