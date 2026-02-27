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
        Schema::create('discount_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_active')->default(true);
            $table->enum('type', ['all_products', 'category', 'cart_value', 'quantity'])->default('all_products');
            $table->enum('discount_type', ['percentage', 'flat'])->default('percentage');
            $table->decimal('discount_amount', 10, 2)->default(0);
            // Cible (catégories, produits)
            $table->json('target_categories')->nullable(); // IDs de product_categories
            $table->json('target_products')->nullable();   // IDs de products
            // Conditions panier
            $table->decimal('min_cart_value', 10, 2)->nullable();
            $table->decimal('max_cart_value', 10, 2)->nullable();
            // Conditions quantité
            $table->integer('min_quantity')->nullable();
            $table->integer('max_quantity')->nullable();
            // Planification
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('stackable')->default(false); // cumulable avec d'autres remises
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount_rules');
    }
};
