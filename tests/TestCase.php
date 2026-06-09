<?php

namespace Tests;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Str;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function createAdmin(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'admin',
            'is_active' => true,
        ], $attrs));
    }

    protected function createCustomer(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'customer',
            'is_active' => true,
        ], $attrs));
    }

    protected function createStaff(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'staff',
            'is_active' => true,
        ], $attrs));
    }

    protected function createDelivery(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'role' => 'delivery',
            'is_active' => true,
        ], $attrs));
    }

    protected function createService(array $attrs = []): Service
    {
        $name = $attrs['name'] ?? ('General Laundry ' . Str::random(5));
        return Service::create(array_merge([
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => 'Standard washing and cleaning',
            'price_per_kg' => 3.50,
            'price_per_item' => 1.50,
            'is_active' => true,
        ], $attrs));
    }

    protected function createOrder(User $customer, array $attrs = []): Order
    {
        // 1. We need a service for the order item
        $service = isset($attrs['service_id']) 
            ? Service::find($attrs['service_id']) 
            : $this->createService();

        // 2. Create the order
        $order = Order::create(array_merge([
            'order_number' => 'LOMS-' . strtoupper(Str::random(8)),
            'customer_id' => $customer->id,
            'total_price' => 25.50,
            'weight' => 5.00,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'cash',
            'pickup_address' => $customer->address ?: '123 Customer St',
            'delivery_address' => $customer->address ?: '123 Customer St',
            'pickup_time' => now()->addDay(),
            'delivery_time' => now()->addDays(2),
        ], collect($attrs)->except(['service_id', 'quantity', 'price', 'notes'])->toArray()));

        // 3. Create one order item
        OrderItem::create([
            'order_id' => $order->id,
            'service_id' => $service->id,
            'quantity' => $attrs['quantity'] ?? 2,
            'price' => $attrs['price'] ?? $service->price_per_kg ?? 3.50,
            'notes' => $attrs['notes'] ?? null,
        ]);

        // 4. Create one payment record
        Payment::create([
            'order_id' => $order->id,
            'user_id' => $customer->id,
            'payment_method' => $order->payment_method,
            'amount' => $order->total_price,
            'status' => $order->payment_status === 'paid' ? 'completed' : ($attrs['payment_status'] ?? 'pending'),
            'paid_at' => $order->payment_status === 'paid' ? now() : null,
        ]);

        return $order;
    }

    protected function actingAsAdmin(): static
    {
        $admin = $this->createAdmin();
        return $this->actingAs($admin);
    }

    protected function actingAsCustomer(): static
    {
        $customer = $this->createCustomer();
        return $this->actingAs($customer);
    }

    protected function actingAsStaff(): static
    {
        $staff = $this->createStaff();
        return $this->actingAs($staff);
    }

    protected function actingAsDelivery(): static
    {
        $delivery = $this->createDelivery();
        return $this->actingAs($delivery);
    }
}
