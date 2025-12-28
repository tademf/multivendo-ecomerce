<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('national_id')->nullable();
            $table->string('profile_picture')->nullable();
            $table->string('account_number', 50)->unique()->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->boolean('is_verified')->default(0);
            $table->rememberToken();
            $table->timestamps();
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active');
        });
    }

    public function down(): void {
        Schema::dropIfExists('users');
    }
};
