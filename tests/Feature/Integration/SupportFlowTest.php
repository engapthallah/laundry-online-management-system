<?php

namespace Tests\Feature\Integration;

use App\Models\SupportMessage;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SupportFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_customer_can_submit_support_message()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)->post('/customer/support', [
            'subject' => 'Issue with Ironing',
            'message' => 'My shirt was returned with creases still present on the sleeves.',
        ]);

        $this->assertDatabaseHas('support_messages', [
            'user_id' => $customer->id,
            'subject' => 'Issue with Ironing',
        ]);

        // Assert notification created for admin
        $this->assertTrue(Notification::where('user_id', $admin->id)
            ->where('title', 'like', '%New Support Message%')
            ->exists());

        $response->assertRedirect(route('customer.support.index'));
    }

    public function test_admin_can_reply_to_support_message()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $message = SupportMessage::create([
            'user_id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'subject' => 'Mismatched clothes',
            'message' => 'I got a shirt that does not belong to me.',
            'status' => 'pending',
        ]);

        $response = $this->actingAs($admin)->post("/admin/support/{$message->id}/reply", [
            'admin_reply' => 'Thank you for contacting us. We will resolve this immediately.',
        ]);

        $this->assertNotNull($message->fresh()->admin_reply);
        $this->assertEquals('resolved', $message->fresh()->status);
        $this->assertNotNull($message->fresh()->replied_at);

        // Assert notification created for customer
        $this->assertTrue(Notification::where('user_id', $customer->id)
            ->where('title', 'like', '%Support Reply%')
            ->exists());

        $response->assertRedirect(route('admin.support.show', $message->id));
    }

    public function test_customer_sees_admin_reply()
    {
        $customer = $this->createCustomer();
        $message = SupportMessage::create([
            'user_id' => $customer->id,
            'name' => $customer->name,
            'email' => $customer->email,
            'subject' => 'Status Check',
            'message' => 'Is my laundry completed yet?',
            'admin_reply' => 'Yes, it is ready for collection now.',
            'status' => 'resolved',
            'replied_at' => now(),
        ]);

        $response = $this->actingAs($customer)->get("/customer/support/{$message->id}");

        $response->assertStatus(200);
        $response->assertSee('Yes, it is ready for collection now.');
    }

    public function test_guest_can_submit_contact_form()
    {
        $admin = $this->createAdmin();

        $response = $this->post('/contact', [
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'subject' => 'Public Inquiry',
            'message' => 'Hi, do you offer bulk corporate cleaning services?',
        ]);

        $this->assertDatabaseHas('support_messages', [
            'user_id' => null,
            'name' => 'Guest User',
            'email' => 'guest@example.com',
            'subject' => 'Public Inquiry',
        ]);

        $response->assertRedirect(route('contact.create'));
    }

    public function test_honeypot_blocks_bot_submissions()
    {
        $admin = $this->createAdmin();

        $response = $this->post('/contact', [
            'name' => 'Spam Bot',
            'email' => 'bot@example.com',
            'subject' => 'Spam Subject',
            'message' => 'This is a spam message that should be caught by the honeypot fields.',
            'website' => 'http://spam.com', // Honeypot trap filled
        ]);

        $this->assertDatabaseMissing('support_messages', [
            'name' => 'Spam Bot',
        ]);

        $response->assertRedirect(route('contact.create'));
    }
}
