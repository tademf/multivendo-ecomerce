<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('national_id')->nullable();
            $table->string('profile_picture')->nullable();
            $table->timestamp('verified_at')->nullable();
            // $table->foreignId('category_id')->nullable()->constrained()->onDelete('set null');
            // $table->foreignId('tag_id')->nullable()->constrained()->onDelete('set null');
            // $table->foreignId('product_id')->nullable()->constrained()->onDelete('set null');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('users');
    }
};