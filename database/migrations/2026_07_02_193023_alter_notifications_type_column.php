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
        // Convert any existing types that are not 'system' or 'email' (including nulls) to 'system'
        DB::table('notifications')
            ->whereNotIn('type', ['system', 'email'])
            ->orWhereNull('type')
            ->update(['type' => 'system']);

        // Modify the column type to ENUM on MySQL, and standard string on SQLite
        if (DB::connection()->getDriverName() === 'mysql') {
            $columnType = '';
            $result = DB::select("SHOW COLUMNS FROM notifications LIKE 'type'");
            if (!empty($result)) {
                $columnType = strtolower($result[0]->Type);
            }

            if (strpos($columnType, 'enum') === false) {
                DB::statement("ALTER TABLE notifications MODIFY COLUMN type ENUM('system', 'email') DEFAULT 'system'");
            }
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
            $columnType = '';
            $result = DB::select("SHOW COLUMNS FROM notifications LIKE 'type'");
            if (!empty($result)) {
                $columnType = strtolower($result[0]->Type);
            }

            if (strpos($columnType, 'varchar') === false) {
                DB::statement("ALTER TABLE notifications MODIFY COLUMN type VARCHAR(255) NULL");
            }
        } else {
            Schema::table('notifications', function (Blueprint $table) {
                $table->string('type')->nullable()->change();
            });
        }
    }
};
