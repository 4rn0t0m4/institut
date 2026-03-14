<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->index('product_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index('is_active');
            $table->index('stock_status');
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('user_id');
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->index('parent_id');
        });

        Schema::table('discount_rules', function (Blueprint $table) {
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
            $table->dropIndex(['stock_status']);
        });

        Schema::table('product_reviews', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('product_categories', function (Blueprint $table) {
            $table->dropIndex(['parent_id']);
        });

        Schema::table('discount_rules', function (Blueprint $table) {
            $table->dropIndex(['is_active']);
        });
    }
};
