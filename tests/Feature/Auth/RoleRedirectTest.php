<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_redirect()
    {
        $admin = $this->createAdmin();
        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertRedirect('/admin/analytics');
    }

    public function test_customer_dashboard_redirect()
    {
        $customer = $this->createCustomer();
        $response = $this->actingAs($customer)->get('/dashboard');
        $response->assertRedirect('/');
    }

    public function test_staff_dashboard_redirect()
    {
        $staff = $this->createStaff();
        $response = $this->actingAs($staff)->get('/dashboard');
        $response->assertRedirect('/staff/dashboard');
    }

    public function test_delivery_dashboard_redirect()
    {
        $delivery = $this->createDelivery();
        $response = $this->actingAs($delivery)->get('/dashboard');
        $response->assertRedirect('/delivery/dashboard');
    }
}
