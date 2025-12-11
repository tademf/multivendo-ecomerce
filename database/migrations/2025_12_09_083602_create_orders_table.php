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
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('payment_id')->nullable(); // Link to payment
            $table->string('name');
            $table->decimal('amount', 12, 2);
            $table->text('shipment_address');
            $table->string('payment_image')->nullable();
            $table->string('status')->default('pending'); // pending, processing, shipped, delivered, cancelled
            $table->text('cancellation_reason')->nullable();
            $table->string('order_number')->unique();
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
            
            $table->index('user_id');
            $table->index('status');
            $table->index('order_number');
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