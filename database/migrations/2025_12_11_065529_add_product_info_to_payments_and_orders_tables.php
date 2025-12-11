<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add product info to payments table
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                if (!Schema::hasColumn('payments', 'product_id')) {
                    $table->unsignedBigInteger('product_id')->nullable()->after('user_id');
                    $table->foreign('product_id')->references('product_id')->on('products')->onDelete('set null');
                }
                if (!Schema::hasColumn('payments', 'product_name')) {
                    $table->string('product_name')->nullable()->after('product_id');
                }
                if (!Schema::hasColumn('payments', 'product_image')) {
                    $table->string('product_image')->nullable()->after('product_name');
                }
                if (!Schema::hasColumn('payments', 'quantity')) {
                    $table->integer('quantity')->default(1)->after('amount');
                }
            });
        }

        // Add product info to orders table
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                if (!Schema::hasColumn('orders', 'product_id')) {
                    $table->unsignedBigInteger('product_id')->nullable()->after('user_id');
                    $table->foreign('product_id')->references('product_id')->on('products')->onDelete('set null');
                }
                if (!Schema::hasColumn('orders', 'product_name')) {
                    $table->string('product_name')->nullable()->after('product_id');
                }
                if (!Schema::hasColumn('orders', 'product_image')) {
                    $table->string('product_image')->nullable()->after('product_name');
                }
                if (!Schema::hasColumn('orders', 'quantity')) {
                    $table->integer('quantity')->default(1)->after('amount');
                }
            });
        }
    }

    public function down(): void
    {
        // Remove columns from payments table
        if (Schema::hasTable('payments')) {
            Schema::table('payments', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropColumn(['product_id', 'product_name', 'product_image', 'quantity']);
            });
        }

        // Remove columns from orders table
        if (Schema::hasTable('orders')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['product_id']);
                $table->dropColumn(['product_id', 'product_name', 'product_image', 'quantity']);
            });
        }
    }
};