<?php

namespace Tests\Unit\Models;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    public function test_payment_belongs_to_order()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer);
        $payment = $order->payment;

        $this->assertInstanceOf(Order::class, $payment->order);
    }

    public function test_payment_has_pending_status_by_default()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer);
        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'payment_method' => 'cash',
            'amount' => 20.00,
        ]);

        $this->assertEquals('pending', $payment->fresh()->status);
    }

    public function test_payment_amount_is_decimal()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer);
        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'payment_method' => 'cash',
            'amount' => 25.50,
        ]);

        $this->assertEquals(25.50, $payment->amount);
    }

    public function test_payment_method_can_be_cash_zaad_edahab()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer);

        foreach (['cash', 'zaad', 'edahab'] as $method) {
            $payment = Payment::create([
                'order_id' => $order->id,
                'user_id' => $customer->id,
                'payment_method' => $method,
                'amount' => 10.00,
            ]);

            $this->assertEquals($method, $payment->payment_method);
        }
    }

    public function test_completed_payment_has_paid_at_timestamp()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer);
        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'payment_method' => 'zaad',
            'amount' => 50.00,
            'status' => 'completed',
            'paid_at' => now(),
        ]);

        $this->assertNotNull($payment->paid_at);
        $this->assertInstanceOf(Carbon::class, $payment->paid_at);
    }
}
