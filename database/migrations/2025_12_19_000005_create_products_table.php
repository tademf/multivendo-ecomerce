<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('name');
            $table->string('reference')->unique()->nullable();
            $table->text('description')->nullable();
            $table->enum('product_type', ['one-time', 'onstock'])->default('one-time');
            $table->decimal('price', 10, 2)->nullable()->comment('Base price for one-time products');
            $table->json('price_tiers')->nullable()->comment('JSON: [{price: 50, stock: 10}, ...]');
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->string('additional_image')->nullable();
            $table->enum('status', ['active', 'inactive', 'out_of_stock', 'draft'])->default('active');
            $table->integer('sold_count')->default(0);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories', 'category_id')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('name');
            $table->index('reference');
            $table->index('user_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('product_type');
            $table->index('price');
            $table->index('stock');
            $table->index(['status', 'product_type']);
            $table->index(['category_id', 'status']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void {
        Schema::dropIfExists('products');
    }
};