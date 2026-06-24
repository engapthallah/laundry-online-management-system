<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'delivery_agent_id')) {
                $table->foreignId('delivery_agent_id')
                      ->nullable()
                      ->constrained('users')
                      ->nullOnDelete();
            }
        });

        if (DB::getDriverName() !== 'sqlite') {
            // Step 1: Temporarily expand the ENUM to include both old and new statuses
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'confirmed',
                'processing',
                'ready_for_delivery',
                'out_for_delivery',
                'delivered',
                'cancelled',
                'pending_pickup',
                'picked_up_from_customer',
                'delivered_to_laundry',
                'picked_up_from_laundry',
                'on_the_way'
            ) NOT NULL DEFAULT 'confirmed'");

            // Step 2: Migrate existing order status values
            DB::table('orders')->where('status', 'confirmed')->update(['status' => 'pending_pickup']);
            DB::table('orders')->where('status', 'out_for_delivery')->update(['status' => 'on_the_way']);

            // Step 3: Shrink ENUM to the final full workflow status set
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'pending_pickup',
                'picked_up_from_customer',
                'delivered_to_laundry',
                'processing',
                'ready_for_delivery',
                'picked_up_from_laundry',
                'on_the_way',
                'delivered',
                'cancelled'
            ) NOT NULL DEFAULT 'pending_pickup'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            // Step 1: Temporarily expand the ENUM to include both old and new statuses for rollback
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'confirmed',
                'processing',
                'ready_for_delivery',
                'out_for_delivery',
                'delivered',
                'cancelled',
                'pending_pickup',
                'picked_up_from_customer',
                'delivered_to_laundry',
                'picked_up_from_laundry',
                'on_the_way'
            ) NOT NULL DEFAULT 'pending_pickup'");

            // Step 2: Rollback status values
            DB::table('orders')->where('status', 'pending_pickup')->update(['status' => 'confirmed']);
            DB::table('orders')->where('status', 'on_the_way')->update(['status' => 'out_for_delivery']);
            DB::table('orders')->whereIn('status', ['picked_up_from_customer', 'delivered_to_laundry'])->update(['status' => 'processing']);
            DB::table('orders')->where('status', 'picked_up_from_laundry')->update(['status' => 'ready_for_delivery']);

            // Step 3: Shrink back to the old status set
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'confirmed',
                'processing',
                'ready_for_delivery',
                'out_for_delivery',
                'delivered',
                'cancelled'
            ) NOT NULL DEFAULT 'confirmed'");
        }

        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'delivery_agent_id')) {
                $table->dropForeign(['delivery_agent_id']);
                $table->dropColumn('delivery_agent_id');
            }
        });
    }
};
