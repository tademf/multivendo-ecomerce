<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('verification_requests', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('national_id')->unique(); // National ID number
            $table->string('national_id_image')->nullable(); // Path to uploaded ID image
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('rejection_reason')->nullable(); // If rejected, store reason
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index('status');
            $table->index('national_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verification_requests');
    }
};