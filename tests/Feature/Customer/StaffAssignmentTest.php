<?php

namespace Tests\Feature\Customer;

use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class StaffAssignmentTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a newly placed order is automatically assigned to an active staff member.
     */
    public function test_order_is_automatically_assigned_to_active_staff_member(): void
    {
        // Create one active staff member
        $staff = $this->createStaff(['name' => 'Staff Alice']);

        $customer = $this->createCustomer();
        $service = $this->createService();

        $this->actingAs($customer)->post('/customer/orders', [
            'services' => [
                [
                    'selected' => '1',
                    'service_id' => $service->id,
                    'quantity' => 1,
                    'weight_kg' => 1.0,
                ]
            ],
            'pickup_address' => '123 Test Street',
            'delivery_address' => '456 Test Ave',
            'pickup_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'delivery_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'payment_method' => 'cash',
        ]);

        $order = Order::orderBy('id', 'desc')->first();
        $this->assertNotNull($order);
        $this->assertEquals($staff->id, $order->staff_id);
    }

    /**
     * Test that ordering cycles through active staff members in round-robin fashion.
     */
    public function test_round_robin_assignment_cycles_through_staff_members(): void
    {
        // Create three active staff members (will be ordered by ID ASC)
        $staff1 = $this->createStaff(['name' => 'Staff 1']);
        $staff2 = $this->createStaff(['name' => 'Staff 2']);
        $staff3 = $this->createStaff(['name' => 'Staff 3']);

        $customer = $this->createCustomer();
        $service = $this->createService();

        // Placing 4 orders.
        // Expect order of assignments: staff1 -> staff2 -> staff3 -> staff1
        $expectedOrder = [$staff1->id, $staff2->id, $staff3->id, $staff1->id];

        for ($i = 0; $i < 4; $i++) {
            $this->actingAs($customer)->post('/customer/orders', [
                'services' => [
                    [
                        'selected' => '1',
                        'service_id' => $service->id,
                        'quantity' => 1,
                        'weight_kg' => 1.0,
                    ]
                ],
                'pickup_address' => '123 Test Street',
                'delivery_address' => '456 Test Ave',
                'pickup_time' => now()->addDay()->addHours($i + 1)->format('Y-m-d H:i:s'),
                'delivery_time' => now()->addDays(2)->addHours($i + 1)->format('Y-m-d H:i:s'),
                'payment_method' => 'cash',
            ]);

            $order = Order::orderBy('id', 'desc')->first();
            $this->assertNotNull($order);
            $this->assertEquals($expectedOrder[$i], $order->staff_id, "Order {$i} was not assigned to expected staff");
        }
    }

    /**
     * Test that assignment skips inactive staff members.
     */
    public function test_order_assignment_skips_inactive_staff_members(): void
    {
        $staff1 = $this->createStaff(['name' => 'Staff 1', 'is_active' => true]);
        $staff2 = $this->createStaff(['name' => 'Staff 2', 'is_active' => false]);
        $staff3 = $this->createStaff(['name' => 'Staff 3', 'is_active' => true]);

        $customer = $this->createCustomer();
        $service = $this->createService();

        // Since staff2 is inactive, the round-robin should cycle only through staff1 and staff3.
        // Expected order: staff1 -> staff3 -> staff1
        $expectedOrder = [$staff1->id, $staff3->id, $staff1->id];

        for ($i = 0; $i < 3; $i++) {
            $this->actingAs($customer)->post('/customer/orders', [
                'services' => [
                    [
                        'selected' => '1',
                        'service_id' => $service->id,
                        'quantity' => 1,
                        'weight_kg' => 1.0,
                    ]
                ],
                'pickup_address' => '123 Test Street',
                'delivery_address' => '456 Test Ave',
                'pickup_time' => now()->addDay()->addHours($i + 1)->format('Y-m-d H:i:s'),
                'delivery_time' => now()->addDays(2)->addHours($i + 1)->format('Y-m-d H:i:s'),
                'payment_method' => 'cash',
            ]);

            $order = Order::orderBy('id', 'desc')->first();
            $this->assertNotNull($order);
            $this->assertEquals($expectedOrder[$i], $order->staff_id);
        }
    }

    /**
     * Test that order placement succeeds even if no active staff exists.
     */
    public function test_order_placement_succeeds_even_if_no_active_staff_exists(): void
    {
        // Mock Log to expect the warnings, while ignoring other logs like errors
        Log::shouldReceive('warning')
            ->with('No active staff users available for round-robin assignment.')
            ->once();
        Log::shouldReceive('warning')
            ->with('LOMS: No active delivery agents available for assignment.')
            ->once();
        Log::shouldReceive('error')->byDefault();
        Log::shouldReceive('info')->byDefault();

        $customer = $this->createCustomer();
        $service = $this->createService();

        // Place an order. Since there are no staff members created, staff_id should be null
        $response = $this->actingAs($customer)->post('/customer/orders', [
            'services' => [
                [
                    'selected' => '1',
                    'service_id' => $service->id,
                    'quantity' => 1,
                    'weight_kg' => 1.0,
                ]
            ],
            'pickup_address' => '123 Test Street',
            'delivery_address' => '456 Test Ave',
            'pickup_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'delivery_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'payment_method' => 'cash',
        ]);

        $order = Order::orderBy('id', 'desc')->first();
        $this->assertNotNull($order);
        $this->assertNull($order->staff_id);
        $response->assertRedirect(route('customer.orders.show', $order->id));
    }
}
