<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('order_number')->unique();
            $table->enum('status', [
                'confirmed',
                'processing',
                'ready_for_delivery',
                'out_for_delivery',
                'delivered',
                'cancelled'
            ])->default('confirmed');
            $table->decimal('total_price', 10, 2);
            $table->decimal('weight', 8, 2)->nullable();
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->enum('payment_method', ['cash', 'zaad', 'edahab'])->nullable();
            $table->text('pickup_address');
            $table->text('delivery_address');
            $table->timestamp('pickup_time')->nullable();
            $table->timestamp('delivery_time')->nullable();
            $table->text('special_instructions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
