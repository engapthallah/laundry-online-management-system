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
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending','paid','failed','refunded','pending_verification','verified','rejected') NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','completed','failed','refunded','rejected') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending','paid','failed','refunded','pending_verification','verified') NOT NULL DEFAULT 'pending'");
            DB::statement("ALTER TABLE payments MODIFY COLUMN status ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending'");
        }
    }
};
