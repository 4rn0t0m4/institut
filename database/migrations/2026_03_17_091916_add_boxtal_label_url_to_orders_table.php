<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'boxtal_label_url')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('boxtal_label_url', 500)->nullable()->after('boxtal_shipping_order_id');
            });
        }
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('boxtal_label_url');
        });
    }
};
