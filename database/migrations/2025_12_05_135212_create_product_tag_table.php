<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
    
        Schema::create('product_tag', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to products table
            // Since products table uses 'product_id' as PK, specify it:
            $table->foreignId('product_id')
                  ->constrained('products', 'product_id') // FIX: Specify custom PK
                  ->onDelete('cascade');
            
            // Foreign key to tags table
            // Since tags table uses 'tag_id' as PK, specify it:
            $table->foreignId('tag_id')
                  ->constrained('tags', 'tag_id') // FIX: Specify custom PK
                  ->onDelete('cascade');
            
            $table->timestamps();
            
            // Prevent duplicate relationships
            $table->unique(['product_id', 'tag_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_tag');
        // REMOVE: Schema::dropIfExists('tags'); // Tags should remain
    }
};