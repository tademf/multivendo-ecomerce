<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('stock_histories', function (Blueprint $table) {
            $table->id();
            
            // FIX: Specify custom primary key 'product_id'
            $table->foreignId('product_id')
                  ->constrained('products', 'product_id') // ADD THIS
                  ->onDelete('cascade');
            
            $table->enum('type', ['sale', 'restock', 'adjustment', 'damage']);
            $table->integer('quantity');
            $table->integer('stock_before');
            $table->integer('stock_after');
            $table->text('notes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
            
            // Optional: Add indexes for better queries
            $table->index('product_id');
            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('stock_histories');
    }
};