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
}
