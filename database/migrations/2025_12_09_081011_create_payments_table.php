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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            
            // User and Product Info
            $table->unsignedBigInteger('user_id');
            $table->string('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_image')->nullable();
            
            // Customer Info
            $table->string('name');
            
            // Payment Info
            $table->string('payment_image')->nullable();
            $table->decimal('amount', 12, 2);
            $table->integer('quantity')->default(1);
            
            // Shipping
            $table->text('shipment_address');
            
            // Status
            $table->string('status')->default('pending');
            $table->string('order_reference')->unique();
            
            $table->timestamps();
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('order_reference');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};