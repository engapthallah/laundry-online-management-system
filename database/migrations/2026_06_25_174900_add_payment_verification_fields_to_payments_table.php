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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('sender_name')->nullable()->after('transaction_reference');
            $table->string('wallet_phone')->nullable()->after('sender_name');
            $table->unsignedBigInteger('verified_by')->nullable()->after('wallet_phone');
            $table->timestamp('verified_at')->nullable()->after('verified_by');

            $table->foreign('verified_by')->references('id')->on('users')->nullOnDelete();

            if (DB::getDriverName() !== 'sqlite') {
                $table->dropUnique('payments_transaction_reference_unique');
            }
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending','paid','failed','refunded','pending_verification','verified') NOT NULL DEFAULT 'pending'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (DB::getDriverName() !== 'sqlite') {
                $table->dropForeign(['verified_by']);
                $table->unique('transaction_reference');
            }
            $table->dropColumn(['sender_name', 'wallet_phone', 'verified_by', 'verified_at']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            DB::statement("ALTER TABLE orders MODIFY COLUMN payment_status ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending'");
        }
    }
};
