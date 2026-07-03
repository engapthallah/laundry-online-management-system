<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_customer_can_register()
    {
        $response = $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'testcustomer@example.com',
            'phone' => '1234567890',
            'address' => '123 Customer St',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();
        
        // Assert user created in database
        $this->assertDatabaseHas('users', [
            'email' => 'testcustomer@example.com',
            'role' => 'customer',
        ]);

        // Assert direct redirect is to the home route
        $response->assertRedirect('/');

        // Let's also verify that following redirects lands us on the home page
        $followResponse = $this->followingRedirects()->post('/register', [
            'name' => 'Test Customer 2',
            'email' => 'testcustomer2@example.com',
            'phone' => '1234567890',
            'address' => '123 Customer St',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        
        $this->assertEquals(url('/'), url()->current());
    }

    public function test_registration_requires_name()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => 'testcustomer@example.com',
            'phone' => '1234567890',
            'address' => '123 Customer St',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['name']);
        $this->assertGuest();
    }

    public function test_registration_requires_valid_email()
    {
        $response = $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'not-an-email',
            'phone' => '1234567890',
            'address' => '123 Customer St',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_registration_requires_password_min_8_chars()
    {
        $response = $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'testcustomer@example.com',
            'phone' => '1234567890',
            'address' => '123 Customer St',
            'password' => '1234567',
            'password_confirmation' => '1234567',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_registration_requires_password_confirmation()
    {
        $response = $this->post('/register', [
            'name' => 'Test Customer',
            'email' => 'testcustomer@example.com',
            'phone' => '1234567890',
            'address' => '123 Customer St',
            'password' => 'password123',
            'password_confirmation' => 'mismatched',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_duplicate_email_is_rejected()
    {
        $this->createCustomer(['email' => 'test@test.com']);

        $response = $this->post('/register', [
            'name' => 'Another Customer',
            'email' => 'test@test.com',
            'phone' => '1234567890',
            'address' => '123 Customer St',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    public function test_registered_user_is_customer_by_default()
    {
        $response = $this->post('/register', [
            'name' => 'Test Customer Default',
            'email' => 'defaultcustomer@example.com',
            'phone' => '1234567890',
            'address' => '123 Customer St',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $user = User::where('email', 'defaultcustomer@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
    }
}
