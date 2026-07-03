<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = $this->createCustomer([
            'email' => 'customer@loms.com',
        ]);

        $response = $this->post('/login', [
            'email' => 'customer@loms.com',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_user_cannot_login_with_wrong_password()
    {
        $user = $this->createCustomer([
            'email' => 'customer@loms.com',
        ]);

        $response = $this->post('/login', [
            'email' => 'customer@loms.com',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }

    public function test_inactive_user_cannot_login()
    {
        $user = $this->createCustomer([
            'email' => 'customer@loms.com',
            'is_active' => false,
        ]);

        $response = $this->post('/login', [
            'email' => 'customer@loms.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }

    public function test_login_redirects_admin_to_admin_analytics()
    {
        $admin = $this->createAdmin([
            'email' => 'admin@loms.com',
        ]);

        $response = $this->followingRedirects()->post('/login', [
            'email' => 'admin@loms.com',
            'password' => 'password',
        ]);

        $this->assertEquals(url('/admin/analytics'), url()->current());
    }

    public function test_login_redirects_customer_to_customer_dashboard()
    {
        $customer = $this->createCustomer([
            'email' => 'customer@loms.com',
        ]);

        $response = $this->followingRedirects()->post('/login', [
            'email' => 'customer@loms.com',
            'password' => 'password',
        ]);

        $this->assertEquals(url('/'), url()->current());
    }

    public function test_login_redirects_staff_to_staff_dashboard()
    {
        $staff = $this->createStaff([
            'email' => 'staff@loms.com',
        ]);

        $response = $this->followingRedirects()->post('/login', [
            'email' => 'staff@loms.com',
            'password' => 'password',
        ]);

        $this->assertEquals(url('/staff/dashboard'), url()->current());
    }

    public function test_login_redirects_delivery_to_delivery_dashboard()
    {
        $delivery = $this->createDelivery([
            'email' => 'delivery@loms.com',
        ]);

        $response = $this->followingRedirects()->post('/login', [
            'email' => 'delivery@loms.com',
            'password' => 'password',
        ]);

        $this->assertEquals(url('/delivery/dashboard'), url()->current());
    }

    public function test_user_can_logout()
    {
        $user = $this->createCustomer();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
