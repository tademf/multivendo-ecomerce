<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->string('name');
            $table->decimal('amount', 12, 2);
            $table->text('shipment_address');
            $table->string('payment_image')->nullable();
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])->default('pending');
            $table->text('cancellation_reason')->nullable();
            $table->string('order_number')->unique();
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('payment_id')->references('id')->on('payments')->onDelete('set null');
            
            // Indexes
            $table->index('user_id');
            $table->index('payment_id');
            $table->index('status');
            $table->index('order_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};