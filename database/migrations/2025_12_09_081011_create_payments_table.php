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
            $table->unsignedBigInteger('user_id'); // Customer who paid
            $table->string('name'); // Customer name
            $table->string('payment_image')->nullable(); // Screenshot upload
            $table->decimal('amount', 12, 2); // Amount paid
            $table->text('shipment_address'); // Shipping address for the product
            $table->string('status')->default('pending'); // pending, verified, completed, rejected
            $table->string('transaction_id')->nullable();
            $table->string('order_reference')->unique();
            $table->timestamps();
            
            // Foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            
            // Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('order_reference');
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