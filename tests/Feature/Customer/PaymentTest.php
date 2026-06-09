<?php

namespace Tests\Feature\Customer;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_customer_can_view_payment_page()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer);
        $payment = $order->payment;

        $response = $this->actingAs($customer)->get("/customer/payments/{$payment->id}");

        $response->assertStatus(200);
    }

    public function test_customer_can_confirm_zaad_payment()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'payment_method' => 'zaad',
            'payment_status' => 'pending',
        ]);
        $payment = $order->payment;

        $response = $this->actingAs($customer)->post("/customer/payments/{$payment->id}/confirm");

        $this->assertEquals('completed', $payment->fresh()->status);
        $this->assertNotNull($payment->fresh()->paid_at);
        $this->assertStringStartsWith('ZAAD-', $payment->fresh()->transaction_reference);
        $this->assertEquals('paid', $order->fresh()->payment_status);
        $response->assertRedirect(route('customer.payments.show', $payment->id));
    }

    public function test_customer_cannot_confirm_cash_payment()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'payment_method' => 'cash',
            'payment_status' => 'pending',
        ]);
        $payment = $order->payment;

        $response = $this->actingAs($customer)->post("/customer/payments/{$payment->id}/confirm");

        $this->assertEquals('pending', $payment->fresh()->status);
        $response->assertSessionHas('error');
    }

    public function test_customer_cannot_access_another_customers_payment()
    {
        $customerA = $this->createCustomer();
        $customerB = $this->createCustomer();
        $orderOfB = $this->createOrder($customerB);
        $paymentOfB = $orderOfB->payment;

        $response = $this->actingAs($customerA)->get("/customer/payments/{$paymentOfB->id}");

        $response->assertStatus(403);
    }

    public function test_payment_confirmation_creates_notification()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'payment_method' => 'zaad',
            'payment_status' => 'pending',
        ]);
        $payment = $order->payment;

        $response = $this->actingAs($customer)->post("/customer/payments/{$payment->id}/confirm");

        $notification = Notification::where('user_id', $customer->id)
            ->where('order_id', $order->id)
            ->latest()
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('Payment Confirmed', $notification->title);
    }
}
