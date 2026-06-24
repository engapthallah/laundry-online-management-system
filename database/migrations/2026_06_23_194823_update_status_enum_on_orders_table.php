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
        if (DB::getDriverName() !== 'sqlite') {
            // Convert old statuses first
            DB::table('orders')
                ->where('status', 'pending')
                ->update(['status' => 'confirmed']);

            DB::table('orders')
                ->where('status', 'washing')
                ->update(['status' => 'processing']);

            DB::table('orders')
                ->where('status', 'drying')
                ->update(['status' => 'processing']);

            DB::table('orders')
                ->where('status', 'ironing')
                ->update(['status' => 'processing']);

            DB::table('orders')
                ->where('status', 'folding')
                ->update(['status' => 'processing']);

            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'confirmed',
                'processing',
                'ready_for_delivery',
                'out_for_delivery',
                'delivered',
                'cancelled'
            ) NOT NULL DEFAULT 'confirmed'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM(
                'pending',
                'confirmed',
                'washing',
                'drying',
                'ironing',
                'folding',
                'ready_for_delivery',
                'out_for_delivery',
                'delivered',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'");
        }
    }
};