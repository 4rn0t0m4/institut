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
        Schema::create('product_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('group_id')->nullable()->constrained('product_addon_field_groups')->nullOnDelete();
            $table->string('name');
            $table->string('label');
            $table->enum('type', ['text', 'textarea', 'select', 'radio', 'checkbox', 'file', 'swatch', 'price'])->default('text');
            $table->decimal('price', 10, 2)->default(0);
            $table->enum('price_type', ['flat', 'percentage'])->default('flat');
            $table->boolean('required')->default(false);
            $table->json('options')->nullable(); // for select/radio/checkbox/swatch
            $table->json('settings')->nullable(); // allowed_types, max_size, etc. for file fields
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Pivot: addons assigned to products or categories
        Schema::create('product_addon_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('addon_id')->constrained('product_addons')->cascadeOnDelete();
            $table->morphs('assignable'); // product or product_category
            $table->enum('mode', ['include', 'exclude'])->default('include');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_addons');
    }
};
