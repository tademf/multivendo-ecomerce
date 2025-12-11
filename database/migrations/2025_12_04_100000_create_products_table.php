<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            
            // Basic product information
            $table->string('name');
            $table->string('reference')->unique();
            $table->text('description')->nullable();
            
            // Product type and pricing
            $table->enum('product_type', ['one-time', 'onstock'])->default('one-time');
            $table->decimal('price', 10, 2)->nullable()->comment('Base price for one-time products');
            
            // Price tiers for onstock products (JSON array)
            $table->json('price_tiers')->nullable()->comment('JSON: [{min:1, max:10, price:50}, ...]');
            
            // Stock management
            $table->integer('stock')->default(0);
            $table->integer('minimum_stock')->default(10);
            $table->boolean('needs_restock')->default(false);
            
            // Image and media
            $table->string('image')->nullable();
            $table->json('additional_images')->nullable()->comment('JSON array of additional product images');
            
            // Product status and visibility
            $table->enum('status', ['active', 'inactive', 'out_of_stock', 'draft'])->default('active');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_best_seller')->default(false);
            $table->boolean('is_new_arrival')->default(false);
            
            // Discount and promotions
            $table->decimal('discount_price', 10, 2)->nullable();
            $table->date('discount_start')->nullable();
            $table->date('discount_end')->nullable();
            $table->decimal('discount_percentage', 5, 2)->nullable()->comment('Percentage discount (0-100)');
            
            // SEO and URL
            $table->string('slug')->unique()->nullable();
            
            // Product dimensions and weight (for shipping)
            $table->decimal('weight', 8, 2)->nullable()->comment('Weight in kg');
            $table->decimal('length', 8, 2)->nullable()->comment('Length in cm');
            $table->decimal('width', 8, 2)->nullable()->comment('Width in cm');
            $table->decimal('height', 8, 2)->nullable()->comment('Height in cm');
            
            // Product specifications (JSON for flexible specs)
            $table->json('specifications')->nullable()->comment('JSON: {key: value} pairs');
            
            // Variants support
            $table->boolean('has_variants')->default(false);
            
            // Analytics and tracking
            $table->integer('view_count')->default(0);
            $table->integer('sold_count')->default(0);
            $table->integer('wishlist_count')->default(0);
            
            // Shipping information
            $table->boolean('requires_shipping')->default(true);
            $table->boolean('free_shipping')->default(false);
            
            // Tax
            $table->decimal('tax_rate', 5, 2)->default(0)->comment('Tax percentage (0-100)');
            
            // Inventory management
            $table->boolean('manage_stock')->default(true);
            $table->boolean('allow_backorders')->default(false);
            $table->integer('low_stock_threshold')->default(5)->comment('Alert when stock reaches this');
            
            // Product relationships
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained('categories', 'category_id')
                  ->onDelete('set null');
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Comprehensive indexes for better performance
            $table->index('name');
            $table->index('reference');
            $table->index('slug');
            $table->index('category_id');
            $table->index('user_id');
            $table->index('status');
            $table->index('product_type');
            $table->index('price');
            $table->index('stock');
            $table->index('is_featured');
            $table->index('is_best_seller');
            $table->index('is_new_arrival');
            $table->index(['status', 'product_type']);
            $table->index(['category_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('products');
    }
};