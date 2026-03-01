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
            $table->boolean('gift_wrap')->default(false)->after('customer_note');
            $table->string('gift_type')->nullable()->after('gift_wrap');
            $table->text('gift_message')->nullable()->after('gift_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['gift_wrap', 'gift_type', 'gift_message']);
        });
    }
};
