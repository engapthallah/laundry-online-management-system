<?php

namespace Tests\Feature\Staff;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PaymentVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_staff_can_verify_mobile_payment()
    {
        $staff = $this->createStaff();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'staff_id' => $staff->id,
            'payment_method' => 'zaad',
            'payment_status' => 'awaiting_staff_review',
        ]);

        $response = $this->actingAs($staff)->patch("/staff/orders/{$order->id}/verify-payment");

        $response->assertStatus(302);
        $order = $order->fresh();
        $this->assertEquals('verified', $order->payment_status);
        $this->assertEquals('completed', $order->payment->status);
        $this->assertEquals('pending_pickup', $order->status); // unchanged status
    }

    public function test_staff_rejecting_payment_cancels_order_and_notifies_customer()
    {
        $staff = $this->createStaff();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, [
            'staff_id' => $staff->id,
            'payment_method' => 'zaad',
            'payment_status' => 'awaiting_staff_review',
            'status' => 'pending_pickup',
        ]);

        $response = $this->actingAs($staff)->patch("/staff/orders/{$order->id}/reject-payment");

        $response->assertStatus(302);
        
        $order = $order->fresh();
        $this->assertEquals('rejected', $order->payment_status);
        $this->assertEquals('cancelled', $order->status);
        $this->assertEquals('rejected', $order->payment->status);

        // Assert customer notification was created via NotificationService pipeline
        $notification = Notification::where('user_id', $customer->id)
            ->where('order_id', $order->id)
            ->where('type', 'system')
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('payment', $notification->message);
        $this->assertStringContainsString('cancelled', $notification->message);

        // Assert email notification was sent
        Mail::assertSent(\App\Mail\OrderStatusUpdatedMail::class, function ($mail) use ($customer, $order) {
            return $mail->hasTo($customer->email) && $mail->order->id === $order->id && $mail->status === 'cancelled';
        });
    }
}
