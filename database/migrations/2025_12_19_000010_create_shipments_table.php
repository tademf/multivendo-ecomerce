<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_image')->nullable();
            $table->string('name');
            $table->decimal('amount', 12, 2);
            $table->integer('quantity')->default(1);
            $table->text('shipment_address');
            $table->string('payment_image')->nullable();
            $table->string('status')->default('pending');
            $table->text('cancellation_reason')->nullable();
            $table->string('order_number');
            $table->foreignId('payment_id')->nullable()->constrained('payments')->onDelete('set null');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->nullable()->constrained('products', 'product_id')->onDelete('set null');
            $table->timestamps();
            $table->softDeletes();
            $table->boolean('is_discounted')->default(0);
            $table->foreignId('discount_id')->nullable()->constrained('discounts', 'discount_id')->onDelete('set null');
            $table->decimal('original_price', 10, 2)->nullable();
            $table->string('discount_name')->nullable();
        });
    }

    public function down(): void {
        Schema::dropIfExists('shipments');
    }
};