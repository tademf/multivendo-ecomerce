<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id('category_id');
            $table->string('name');
            $table->text('description')->nullable(); // FIXED: Made nullable
            $table->timestamps();
            
            // Optional: Add index
            $table->index('name');
        });
        
        // REMOVE THIS: Data insertion should be in Seeder
        // DB::table('categories')->insert([...]);
    }

    public function down()
    {
        Schema::dropIfExists('categories');
    }
};