<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('products')->where('manage_stock', false)->update(['manage_stock' => true]);

        Schema::table('products', function (Blueprint $table) {
            $table->boolean('manage_stock')->default(true)->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('manage_stock')->default(false)->change();
        });
    }
};
