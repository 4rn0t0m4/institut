<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('number', 50)->unique();
            $table->decimal('amount', 10, 2);
            $table->string('reason')->nullable();
            $table->boolean('stripe_refunded')->default(false);
            $table->string('stripe_refund_id')->nullable();
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('refunded_at')->nullable()->after('shipped_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('credit_notes');

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('refunded_at');
        });
    }
};
