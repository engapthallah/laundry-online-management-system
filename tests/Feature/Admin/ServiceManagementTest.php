<?php

namespace Tests\Feature\Admin;

use App\Models\Service;
use App\Models\OrderItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_services_list()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/services');

        $response->assertStatus(200);
    }

    public function test_admin_can_create_service()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/services', [
            'name' => 'Premium Dry Cleaning',
            'description' => 'Luxury care for suits and gowns',
            'price_per_kg' => 12.50,
            'price_per_item' => 5.00,
            'is_active' => 'on',
        ]);

        $this->assertDatabaseHas('services', [
            'name' => 'Premium Dry Cleaning',
            'price_per_kg' => 12.50,
        ]);
        $response->assertRedirect(route('admin.services.index'));
    }

    public function test_service_slug_is_auto_generated()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/services', [
            'name' => 'Dry Cleaning Special',
            'description' => 'Special care description',
            'price_per_kg' => 8.00,
            'price_per_item' => 3.00,
        ]);

        $service = Service::where('name', 'Dry Cleaning Special')->first();
        $this->assertNotNull($service);
        $this->assertEquals('dry-cleaning-special', $service->slug);
    }

    public function test_admin_can_update_service()
    {
        $admin = $this->createAdmin();
        $service = $this->createService();

        $response = $this->actingAs($admin)->patch("/admin/services/{$service->id}", [
            'name' => 'Super Laundering',
            'description' => 'Updated description',
            'price_per_kg' => 4.50,
            'price_per_item' => 2.00,
        ]);

        $this->assertEquals('Super Laundering', $service->fresh()->name);
        $this->assertEquals('super-laundering', $service->fresh()->slug);
        $response->assertRedirect(route('admin.services.index'));
    }

    public function test_admin_can_delete_service()
    {
        $admin = $this->createAdmin();
        $service = $this->createService();

        $response = $this->actingAs($admin)->delete("/admin/services/{$service->id}");

        $this->assertDatabaseMissing('services', [
            'id' => $service->id,
        ]);
        $response->assertRedirect(route('admin.services.index'));
    }

    public function test_admin_cannot_delete_service_with_order_items()
    {
        $admin = $this->createAdmin();
        $customer = $this->createCustomer();
        
        // createOrder creates 1 order item by default using an auto-generated service
        $order = $this->createOrder($customer);
        $service = $order->orderItems->first()->service;

        $response = $this->actingAs($admin)->delete("/admin/services/{$service->id}");

        // Service should still exist
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
        ]);
        $response->assertSessionHas('error');
        $response->assertRedirect(route('admin.services.index'));
    }
}
