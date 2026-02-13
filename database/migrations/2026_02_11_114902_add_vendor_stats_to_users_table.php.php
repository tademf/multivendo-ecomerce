<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Vendor statistics
            $table->decimal('average_rating', 2, 1)
                  ->default(0.0)
                  ->nullable()
                  ->after('is_verified')
                  ->comment('Average of all ratings (1-5)');
            
            $table->integer('total_ratings')
                  ->default(0)
                  ->nullable()
                  ->after('average_rating')
                  ->comment('Total number of ratings received');
            
            $table->integer('total_sold')
                  ->default(0)
                  ->nullable()
                  ->after('total_ratings')
                  ->comment('Total products sold across all listings');
            
            $table->integer('total_products')
                  ->default(0)
                  ->nullable()
                  ->after('total_sold')
                  ->comment('Total active products');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'average_rating',
                'total_ratings',
                'total_sold',
                'total_products'
            ]);
        });
    }
};