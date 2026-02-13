<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_ratings', function (Blueprint $table) {
            $table->id('rating_id');
            
            // Who rated
            $table->foreignId('customer_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            // Who is being rated
            $table->foreignId('vendor_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            // Order reference
            $table->foreignId('order_id')
                  ->constrained('shipments')
                  ->onDelete('cascade');
            
            // Rating details
            $table->tinyInteger('rating')
                  ->comment('1-5 stars')
                  ->check('rating >= 1 AND rating <= 5');
            
            $table->text('review')->nullable();
            
            // Status
            $table->boolean('is_approved')->default(true);
            $table->boolean('is_edited')->default(false);
            
            $table->timestamps();
            $table->softDeletes();

            // Indexes for fast queries
            $table->index(['vendor_id', 'rating']);
            $table->index(['customer_id', 'created_at']);
            $table->unique(['order_id', 'customer_id', 'vendor_id'], 'unique_rating');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_ratings');
    }
};