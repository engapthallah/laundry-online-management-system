<?php

namespace Tests\Unit\Services;

use App\Models\Notification;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_send_creates_notification_in_database()
    {
        $user = $this->createCustomer();
        
        NotificationService::send($user->id, 'Test Title', 'Test message');

        $this->assertDatabaseHas('notifications', [
            'user_id' => $user->id,
            'title' => 'Test Title',
            'message' => 'Test message',
            'is_read' => false,
        ]);

        $notification = Notification::where('user_id', $user->id)->first();
        $this->assertNotNull($notification->sent_at);
    }

    public function test_send_returns_notification_model()
    {
        $user = $this->createCustomer();
        
        $result = NotificationService::send($user->id, 'Test Title', 'Test message');

        $this->assertInstanceOf(Notification::class, $result);
    }

    public function test_send_does_not_throw_on_invalid_user()
    {
        // Calling with a non-existent user ID should not throw an exception (wrapped in try/catch)
        $result = NotificationService::send(999999, 'Test Title', 'Test message');
        
        // Since there is a foreign key constraint on user_id, SQLite will throw an exception
        // inside the database, which gets caught and returns null.
        $this->assertNull($result);
    }

    public function test_order_placed_creates_customer_notification()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer);

        NotificationService::orderPlaced($order);

        $this->assertTrue(Notification::where('user_id', $customer->id)->exists());
    }

    public function test_order_placed_creates_admin_notification()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer);

        NotificationService::orderPlaced($order);

        $this->assertTrue(Notification::where('user_id', $admin->id)->exists());
    }

    public function test_order_status_updated_creates_notification()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer);

        NotificationService::orderStatusUpdated($order, 'processing');

        $notification = Notification::where('user_id', $customer->id)
            ->where('order_id', $order->id)
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString('cleaning', $notification->message);
    }

    public function test_send_whatsapp_notification_ready_for_delivery()
    {
        \Illuminate\Support\Facades\Http::fake();
        \Illuminate\Support\Facades\Queue::fake();
        config([
            'services.callmebot.apikey' => 'test-key',
            'services.callmebot.phone' => '252637205471',
        ]);

        $customer = $this->createCustomer(['phone' => '+252633336664']);
        $order = $this->createOrder($customer, ['status' => 'processing']);

        NotificationService::orderStatusUpdated($order, 'ready_for_delivery');

        // Verify the job was pushed
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SendWhatsAppNotificationJob::class);

        // Verify the customer email was sent
        Mail::assertSent(\App\Mail\OrderReadyForDeliveryMail::class, function ($mail) use ($customer, $order) {
            return $mail->order->id === $order->id;
        });

        // Run the call to test the HTTP request directly
        NotificationService::sendWhatsAppNotification($customer, $order, 'ready_for_delivery');

        \Illuminate\Support\Facades\Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.callmebot.com/whatsapp.php') &&
                $request['phone'] === '252637205471' && // Recipient is owner, not customer
                str_contains($request['text'], 'Order Ready for Delivery') &&
                $request['apikey'] === 'test-key';
        });
    }

    public function test_send_whatsapp_notification_delivered()
    {
        \Illuminate\Support\Facades\Http::fake();
        \Illuminate\Support\Facades\Queue::fake();
        config([
            'services.callmebot.apikey' => 'test-key',
            'services.callmebot.phone' => '252637205471',
        ]);

        $customer = $this->createCustomer(['phone' => '+252633336664']);
        $order = $this->createOrder($customer, ['status' => 'ready_for_delivery']);

        NotificationService::orderStatusUpdated($order, 'delivered');

        // Verify the job was pushed
        \Illuminate\Support\Facades\Queue::assertPushed(\App\Jobs\SendWhatsAppNotificationJob::class);

        // Verify the customer email was sent
        Mail::assertSent(\App\Mail\OrderDeliveredMail::class, function ($mail) use ($customer, $order) {
            return $mail->order->id === $order->id;
        });

        // Run the call directly
        NotificationService::sendWhatsAppNotification($customer, $order, 'delivered');

        \Illuminate\Support\Facades\Http::assertSent(function ($request) {
            return str_contains($request->url(), 'api.callmebot.com/whatsapp.php') &&
                $request['phone'] === '252637205471' && // Recipient is owner, not customer
                str_contains($request['text'], 'Order Delivered') &&
                $request['apikey'] === 'test-key';
        });
    }

    public function test_send_whatsapp_notification_handles_failure_without_throwing()
    {
        \Illuminate\Support\Facades\Http::fake([
            'api.callmebot.com/*' => \Illuminate\Support\Facades\Http::response('Error', 500),
        ]);
        config([
            'services.callmebot.apikey' => 'test-key',
            'services.callmebot.phone' => '252637205471',
        ]);

        $customer = $this->createCustomer(['phone' => '+252633336664']);
        $order = $this->createOrder($customer);

        // This should run without throwing any exceptions
        $result = NotificationService::sendWhatsAppNotification($customer, $order, 'ready_for_delivery');
        $this->assertFalse($result);
    }
}
