<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();

            // 📸 Single Media Files
            $table->string('image')->nullable();           // ONE image
            $table->string('video')->nullable();           // ONE video URL or file path

            // 📝 Content
            $table->text('description');                   // Required

            // 💰 Payment
            $table->string('payment_proof');               // Required - ONE payment receipt

            // 📅 Dates
            $table->timestamp('published_at')->nullable(); // Starts now
            $table->timestamp('expired_at')->nullable();   // Calculated from duration

            // ✅ Status
            $table->enum('status', ['pending', 'approved', 'rejected', 'expired'])
                ->default('pending');

            // 👤 User
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            // ⏱️ Timestamps
            $table->timestamps();

            // 📊 Indexes
            $table->index('user_id');
            $table->index('status');
            $table->index('published_at');
            $table->index('expired_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};
