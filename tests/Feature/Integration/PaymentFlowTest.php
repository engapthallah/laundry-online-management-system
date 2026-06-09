<?php

namespace Tests\Feature\Integration;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_zaad_payment_full_flow()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'payment_method' => 'zaad',
            'payment_status' => 'pending',
        ]);
        $payment = $order->payment;

        $this->assertEquals('pending', $payment->status);

        $response = $this->actingAs($customer)->post("/customer/payments/{$payment->id}/confirm");

        $this->assertEquals('completed', $payment->fresh()->status);
        $this->assertStringStartsWith('ZAAD-', $payment->fresh()->transaction_reference);
        $this->assertTrue(Notification::where('user_id', $customer->id)
            ->where('order_id', $order->id)
            ->where('title', 'like', '%Payment Confirmed%')
            ->exists());
    }

    public function test_admin_can_manually_complete_cash_payment()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);
        $payment = $order->payment;

        $response = $this->actingAs($admin)->post("/admin/payments/{$payment->id}/complete", [
            'transaction_reference' => 'CASH-REF-1234',
        ]);

        $this->assertEquals('completed', $payment->fresh()->status);
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $this->assertTrue(Notification::where('user_id', $customer->id)
            ->where('order_id', $order->id)
            ->where('title', 'like', '%Payment Confirmed%')
            ->exists());
    }

    public function test_admin_can_refund_payment()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'status' => 'folding', // NOT delivered
            'payment_status' => 'paid',
        ]);
        
        $payment = $order->payment;
        $payment->status = 'completed';
        $payment->save();

        $response = $this->actingAs($admin)->post("/admin/payments/{$payment->id}/refund");

        $this->assertEquals('refunded', $payment->fresh()->status);
        $this->assertEquals('refunded', $order->fresh()->payment_status);
        $this->assertTrue(Notification::where('user_id', $customer->id)->where('order_id', $order->id)->exists());
    }

    public function test_refund_blocked_for_delivered_orders()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'status' => 'delivered', // Delivered
            'payment_status' => 'paid',
        ]);
        
        $payment = $order->payment;
        $payment->status = 'completed';
        $payment->save();

        $response = $this->actingAs($admin)->post("/admin/payments/{$payment->id}/refund");

        $this->assertEquals('completed', $payment->fresh()->status);
        $response->assertSessionHas('error');
    }
}
