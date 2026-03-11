<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('team_recommendation')->nullable()->after('description');
            $table->text('benefits')->nullable()->after('team_recommendation');
            $table->text('usage_instructions')->nullable()->after('benefits');
            $table->text('composition')->nullable()->after('usage_instructions');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['team_recommendation', 'benefits', 'usage_instructions', 'composition']);
        });
    }
};
