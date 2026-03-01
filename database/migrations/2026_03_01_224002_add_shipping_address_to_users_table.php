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
        Schema::table('users', function (Blueprint $table) {
            $table->string('shipping_first_name')->nullable()->after('country');
            $table->string('shipping_last_name')->nullable()->after('shipping_first_name');
            $table->string('shipping_address_1')->nullable()->after('shipping_last_name');
            $table->string('shipping_address_2')->nullable()->after('shipping_address_1');
            $table->string('shipping_city', 100)->nullable()->after('shipping_address_2');
            $table->string('shipping_postcode', 20)->nullable()->after('shipping_city');
            $table->string('shipping_country', 2)->nullable()->after('shipping_postcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['shipping_first_name', 'shipping_last_name', 'shipping_address_1', 'shipping_address_2', 'shipping_city', 'shipping_postcode', 'shipping_country']);
        });
    }
};
