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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_key', 50)->nullable()->after('shipping_method');
            $table->string('relay_point_code', 100)->nullable()->after('shipping_key');
            $table->string('relay_network', 50)->nullable()->after('relay_point_code');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_key', 'relay_point_code', 'relay_network']);
        });
    }
};
