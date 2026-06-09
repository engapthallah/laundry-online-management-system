<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users_list()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Users');
    }

    public function test_admin_can_create_user()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'New Staff Member',
            'email' => 'newstaff@example.com',
            'password' => 'password123',
            'role' => 'staff',
            'phone' => '1234567890',
            'address' => 'Staff St 1',
            'is_active' => 'on',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'newstaff@example.com',
            'role' => 'staff',
        ]);
        $response->assertRedirect(route('admin.users.index'));
    }

    public function test_admin_can_create_user_with_any_role()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'New Delivery Agent',
            'email' => 'newdelivery@example.com',
            'password' => 'password123',
            'role' => 'delivery',
            'phone' => '1234567890',
            'address' => 'Delivery St 1',
            'is_active' => 'on',
        ]);

        $user = User::where('email', 'newdelivery@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('delivery', $user->role);
    }

    public function test_admin_can_update_user()
    {
        $admin = $this->createAdmin();
        $staff = $this->createStaff();

        $response = $this->actingAs($admin)->patch("/admin/users/{$staff->id}", [
            'name' => 'Updated Staff Name',
            'email' => $staff->email,
            'role' => 'staff',
            'phone' => '9999999999',
            'address' => $staff->address,
        ]);

        $this->assertEquals('Updated Staff Name', $staff->fresh()->name);
        $response->assertRedirect(route('admin.users.index'));
    }

    public function test_admin_can_delete_user()
    {
        $admin = $this->createAdmin();
        $staff = $this->createStaff();

        $response = $this->actingAs($admin)->delete("/admin/users/{$staff->id}");

        $this->assertDatabaseMissing('users', [
            'id' => $staff->id,
        ]);
        $response->assertRedirect(route('admin.users.index'));
    }

    public function test_admin_cannot_create_user_with_duplicate_email()
    {
        $admin = $this->createAdmin();
        $existing = $this->createCustomer(['email' => 'duplicate@example.com']);

        $response = $this->actingAs($admin)->post('/admin/users', [
            'name' => 'Name',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'role' => 'customer',
        ]);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_customer_cannot_access_user_management()
    {
        $customer = $this->createCustomer();

        $response = $this->actingAs($customer)->get('/admin/users');

        $response->assertRedirect('/dashboard');
    }
}
