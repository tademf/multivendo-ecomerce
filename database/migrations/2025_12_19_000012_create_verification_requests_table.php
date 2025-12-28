<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('verification_requests', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('national_id')->unique();
            $table->string('national_id_image')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('rejection_reason')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            
            $table->index('status');
            $table->index('national_id');
            $table->index('user_id');
        });
    }

    public function down(): void {
        Schema::dropIfExists('verification_requests');
    }
};
