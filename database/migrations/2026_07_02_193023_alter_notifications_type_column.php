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
        // Convert any existing 'sms', 'payment_verified', and 'payment_rejected' types to 'system'
        DB::table('notifications')
            ->whereIn('type', ['sms', 'payment_verified', 'payment_rejected'])
            ->update(['type' => 'system']);

        // Modify the column type to ENUM on MySQL, and standard string on SQLite
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('system', 'email') DEFAULT 'system'");
        } else {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('type')->default('system')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert columns back to VARCHAR(255)
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE notifications MODIFY COLUMN type VARCHAR(255) NULL");
        } else {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('type')->nullable()->change();
            });
        }
    }
};
