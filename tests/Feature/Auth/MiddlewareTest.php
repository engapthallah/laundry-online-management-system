<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_admin_dashboard()
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_customer_dashboard()
    {
        $response = $this->get('/customer/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_staff_dashboard()
    {
        $response = $this->get('/staff/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_guest_cannot_access_delivery_dashboard()
    {
        $response = $this->get('/delivery/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_customer_cannot_access_admin_routes()
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)->get('/admin/dashboard');

        $response->assertRedirect('/dashboard');
    }

    public function test_customer_cannot_access_staff_routes()
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)->get('/staff/dashboard');

        $response->assertRedirect('/dashboard');
    }

    public function test_customer_cannot_access_delivery_routes()
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)->get('/delivery/dashboard');

        $response->assertRedirect('/dashboard');
    }

    public function test_admin_cannot_access_customer_dashboard()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/customer/dashboard');

        $response->assertRedirect('/dashboard');
    }

    public function test_staff_cannot_access_admin_routes()
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get('/admin/dashboard');

        $response->assertRedirect('/dashboard');
    }

    public function test_staff_cannot_access_customer_routes()
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get('/customer/orders');

        $response->assertRedirect('/dashboard');
    }

    public function test_delivery_cannot_access_admin_routes()
    {
        $delivery = $this->createDelivery();

        $response = $this->actingAs($delivery)->get('/admin/dashboard');

        $response->assertRedirect('/dashboard');
    }

    public function test_admin_can_access_admin_dashboard()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_customer_can_access_customer_dashboard()
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)->get('/customer/dashboard');

        $response->assertStatus(200);
    }

    public function test_staff_can_access_staff_dashboard()
    {
        $staff = $this->createStaff();

        $response = $this->actingAs($staff)->get('/staff/dashboard');

        $response->assertStatus(200);
    }

    public function test_delivery_can_access_delivery_dashboard()
    {
        $delivery = $this->createDelivery();

        $response = $this->actingAs($delivery)->get('/delivery/dashboard');

        $response->assertStatus(200);
    }
}
