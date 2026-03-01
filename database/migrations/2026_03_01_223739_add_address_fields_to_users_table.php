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
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone', 30)->nullable()->after('last_name');
            $table->string('address_1')->nullable()->after('phone');
            $table->string('address_2')->nullable()->after('address_1');
            $table->string('city', 100)->nullable()->after('address_2');
            $table->string('postcode', 20)->nullable()->after('city');
            $table->string('country', 2)->default('FR')->after('postcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['first_name', 'last_name', 'phone', 'address_1', 'address_2', 'city', 'postcode', 'country']);
        });
    }
};
