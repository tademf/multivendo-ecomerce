<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('additional_images', function (Blueprint $table) {
            $table->id('additional_image_id');
            $table->foreignId('product_id')
                  ->constrained('products', 'product_id')
                  ->onDelete('cascade');
            $table->string('image_path');
            $table->integer('display_order')->default(0);
            $table->boolean('is_selected')->default(false)->comment('For when customer selects an image to buy');
            $table->timestamps();
            
            $table->index('product_id');
            $table->index('display_order');
            $table->index(['product_id', 'display_order']);
            $table->index('is_selected');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('additional_images');
    }
};