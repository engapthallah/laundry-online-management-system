<?php

namespace Tests\Feature\Integration;

use App\Models\Order;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReviewFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_review_can_only_be_submitted_for_delivered_order()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, ['status' => 'confirmed']);

        $response = $this->actingAs($customer)->post('/customer/reviews', [
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'Should fail',
        ]);

        $this->assertFalse(Review::where('order_id', $order->id)->exists());
        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_review_can_be_submitted_for_delivered_order()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, ['status' => 'delivered']);

        $response = $this->actingAs($customer)->post('/customer/reviews', [
            'order_id' => $order->id,
            'rating' => 4,
            'comment' => 'Very good laundry experience!',
        ]);

        $this->assertTrue(Review::where('order_id', $order->id)->exists());
        $this->assertDatabaseHas('reviews', [
            'order_id' => $order->id,
            'rating' => 4,
            'comment' => 'Very good laundry experience!',
        ]);
        $response->assertRedirect(route('customer.reviews.index'));
    }

    public function test_duplicate_review_is_blocked()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, ['status' => 'delivered']);

        // First review
        $response1 = $this->actingAs($customer)->post('/customer/reviews', [
            'order_id' => $order->id,
            'rating' => 5,
            'comment' => 'First review',
        ]);
        $response1->assertRedirect();

        // Second review attempt
        $response2 = $this->actingAs($customer)->post('/customer/reviews', [
            'order_id' => $order->id,
            'rating' => 1,
            'comment' => 'Second review',
        ]);

        $this->assertEquals(1, Review::where('order_id', $order->id)->count());
        $response2->assertSessionHas('error');
    }

    public function test_review_is_visible_on_public_page()
    {
        $customer = $this->createCustomer();
        $order = $this->createOrder($customer, ['status' => 'delivered']);
        
        Review::create([
            'order_id' => $order->id,
            'customer_id' => $customer->id,
            'rating' => 5,
            'comment' => 'Unbelievable customer service!',
        ]);

        // Request reviews page as guest
        $response = $this->get('/reviews');

        $response->assertStatus(200);
        $response->assertSee('Unbelievable customer service!');
    }
}
