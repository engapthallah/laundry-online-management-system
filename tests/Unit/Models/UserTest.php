<?php

namespace Tests\Unit\Models;

use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    public function test_user_has_customer_role_by_default()
    {
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret',
        ]);

        $this->assertEquals('customer', $user->fresh()->role);
    }

    public function test_is_admin_returns_true_for_admin_role()
    {
        $user = $this->createAdmin();

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isCustomer());
    }

    public function test_is_customer_returns_true_for_customer_role()
    {
        $user = $this->createCustomer();

        $this->assertTrue($user->isCustomer());
        $this->assertFalse($user->isAdmin());
    }

    public function test_is_staff_returns_true_for_staff_role()
    {
        $user = $this->createStaff();

        $this->assertTrue($user->isStaff());
    }

    public function test_is_delivery_returns_true_for_delivery_role()
    {
        $user = $this->createDelivery();

        $this->assertTrue($user->isDelivery());
    }

    public function test_user_has_many_orders_as_customer()
    {
        $user = $this->createCustomer();
        
        $this->createOrder($user, ['order_number' => 'LOMS-ORDER-1']);
        $this->createOrder($user, ['order_number' => 'LOMS-ORDER-2']);
        $this->createOrder($user, ['order_number' => 'LOMS-ORDER-3']);

        $this->assertEquals(3, $user->orders->count());
    }

    public function test_user_password_is_hashed()
    {
        $user = User::factory()->create([
            'password' => 'secret',
        ]);

        $this->assertNotEquals('secret', $user->password);
        $this->assertTrue(Hash::check('secret', $user->password));
    }

    public function test_inactive_user_flag()
    {
        $user = $this->createCustomer(['is_active' => false]);

        $this->assertFalse($user->is_active);
    }
}
