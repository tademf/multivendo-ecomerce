<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('product_id')->nullable();
            $table->string('product_name')->nullable();
            $table->string('product_image')->nullable();
            $table->string('name');
            $table->string('payment_image')->nullable();
            $table->decimal('amount', 12, 2);
            $table->integer('quantity')->default(1);
            $table->text('shipment_address');
            $table->string('status')->default('pending');
            $table->string('order_reference')->unique();
            $table->timestamps();
            $table->boolean('is_discounted')->default(0);
            $table->foreignId('discount_id')->nullable()->constrained('discounts', 'discount_id')->onDelete('set null');
            $table->decimal('original_price', 10, 2)->nullable();
            $table->string('discount_name')->nullable();
            
            $table->index('user_id');
            $table->index('status');
            $table->index('order_reference');
            $table->index('product_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('payments');
    }
};