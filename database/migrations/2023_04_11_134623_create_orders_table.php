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
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('order_status_id')->constrained('order_statuses')->onDelete('cascade');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('cascade');
            $table->uuid();
            $table->json('products');
            $table->string('address', 255);
            $table->float('delivery_fee', 8, 2)->nullable();
            $table->double('amount', 12, 2);
            $table->timestamps();
            $table->timestamp('shipped_at')->nullable();
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
