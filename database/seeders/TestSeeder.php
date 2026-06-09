<?php

namespace Database\Seeders;

use App\Models\DeliveryAssignment;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Service;
use App\Models\SupportMessage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Users
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@loms.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'phone' => '1111111111',
            'address' => 'Admin HQ',
            'is_active' => true,
        ]);

        $staff = User::create([
            'name' => 'Test Staff',
            'email' => 'staff@loms.com',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'phone' => '2222222222',
            'address' => 'Staff Room',
            'is_active' => true,
        ]);

        $delivery = User::create([
            'name' => 'Test Delivery',
            'email' => 'delivery@loms.com',
            'password' => Hash::make('password'),
            'role' => 'delivery',
            'phone' => '3333333333',
            'address' => 'Delivery Hub',
            'is_active' => true,
        ]);

        $customer1 = User::create([
            'name' => 'Customer One',
            'email' => 'customer1@loms.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '4444444444',
            'address' => 'Customer Address 1',
            'is_active' => true,
        ]);

        $customer2 = User::create([
            'name' => 'Customer Two',
            'email' => 'customer2@loms.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '5555555555',
            'address' => 'Customer Address 2',
            'is_active' => true,
        ]);

        $users = [$admin, $staff, $delivery, $customer1, $customer2];

        // 2. Create 3 active services
        $serviceWash = Service::create([
            'name' => 'Washing',
            'slug' => 'washing',
            'description' => 'Wash and clean clothes',
            'price_per_kg' => 3.00,
            'price_per_item' => 1.00,
            'is_active' => true,
        ]);

        $serviceDry = Service::create([
            'name' => 'Dry Cleaning',
            'slug' => 'dry-cleaning',
            'description' => 'Professional dry cleaning',
            'price_per_kg' => 6.00,
            'price_per_item' => 3.00,
            'is_active' => true,
        ]);

        $serviceIron = Service::create([
            'name' => 'Ironing',
            'slug' => 'ironing',
            'description' => 'Iron clothes neatly',
            'price_per_kg' => 2.00,
            'price_per_item' => 0.50,
            'is_active' => true,
        ]);

        // 3. Create Orders (2 per customer: 1 pending, 1 delivered)
        $customers = [$customer1, $customer2];
        $deliveredOrderToReview = null;

        foreach ($customers as $index => $customer) {
            // Pending Order
            $pendingOrder = Order::create([
                'order_number' => 'LOMS-PENDING-' . ($index + 1),
                'customer_id' => $customer->id,
                'total_price' => 15.00,
                'weight' => 5.00,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'cash',
                'pickup_address' => $customer->address,
                'delivery_address' => $customer->address,
                'pickup_time' => now()->addDay(),
                'delivery_time' => now()->addDays(2),
            ]);

            OrderItem::create([
                'order_id' => $pendingOrder->id,
                'service_id' => $serviceWash->id,
                'quantity' => 2,
                'price' => $serviceWash->price_per_kg,
            ]);

            Payment::create([
                'order_id' => $pendingOrder->id,
                'user_id' => $customer->id,
                'payment_method' => 'cash',
                'amount' => 15.00,
                'status' => 'pending',
            ]);

            // Delivered Order
            $deliveredOrder = Order::create([
                'order_number' => 'LOMS-DELIVERED-' . ($index + 1),
                'customer_id' => $customer->id,
                'staff_id' => $staff->id,
                'total_price' => 30.00,
                'weight' => 5.00,
                'status' => 'delivered',
                'payment_status' => 'paid',
                'payment_method' => 'zaad',
                'pickup_address' => $customer->address,
                'delivery_address' => $customer->address,
                'pickup_time' => now()->subDays(3),
                'delivery_time' => now()->subDays(1),
            ]);

            OrderItem::create([
                'order_id' => $deliveredOrder->id,
                'service_id' => $serviceDry->id,
                'quantity' => 1,
                'price' => $serviceDry->price_per_kg,
            ]);

            Payment::create([
                'order_id' => $deliveredOrder->id,
                'user_id' => $customer->id,
                'payment_method' => 'zaad',
                'amount' => 30.00,
                'transaction_reference' => 'ZAAD-' . Str::upper(Str::random(10)),
                'status' => 'completed',
                'paid_at' => now()->subDays(3),
            ]);

            DeliveryAssignment::create([
                'order_id' => $deliveredOrder->id,
                'delivery_agent_id' => $delivery->id,
                'status' => 'delivered',
                'assigned_at' => now()->subDays(2),
                'picked_up_at' => now()->subDays(2),
                'delivered_at' => now()->subDays(1),
            ]);

            if ($customer->id === $customer1->id) {
                $deliveredOrderToReview = $deliveredOrder;
            }
        }

        // 4. Create 1 review on delivered order
        if ($deliveredOrderToReview) {
            Review::create([
                'order_id' => $deliveredOrderToReview->id,
                'customer_id' => $customer1->id,
                'rating' => 5,
                'comment' => 'Excellent laundry! Highly recommended.',
            ]);
        }

        // 5. Create 1 support message (pending)
        SupportMessage::create([
            'user_id' => $customer1->id,
            'name' => $customer1->name,
            'email' => $customer1->email,
            'subject' => 'Missing socks',
            'message' => 'I lost two socks in my wash. Can you please check?',
            'status' => 'pending',
        ]);

        // 6. Create 3 notifications per user
        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'title' => 'Welcome to LOMS',
                'message' => 'Welcome to the Laundry Online Management System!',
                'type' => 'system',
                'is_read' => false,
                'sent_at' => now(),
            ]);

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Profile Complete',
                'message' => 'Your user profile details have been successfully configured.',
                'type' => 'system',
                'is_read' => true,
                'sent_at' => now()->subDay(),
            ]);

            Notification::create([
                'user_id' => $user->id,
                'title' => 'Promotional Offer',
                'message' => 'Get 10% off on your next dry cleaning order this week.',
                'type' => 'system',
                'is_read' => false,
                'sent_at' => now()->subDays(2),
            ]);
        }
    }
}
