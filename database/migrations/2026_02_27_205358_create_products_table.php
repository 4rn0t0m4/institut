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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('product_categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->decimal('sale_price', 10, 2)->nullable();
            $table->string('sku')->nullable()->unique();
            $table->integer('stock_quantity')->nullable();
            $table->boolean('manage_stock')->default(false);
            $table->enum('stock_status', ['instock', 'outofstock', 'onbackorder'])->default('instock');
            $table->decimal('weight', 8, 3)->nullable();
            $table->json('dimensions')->nullable(); // {length, width, height}
            $table->boolean('is_virtual')->default(false);
            $table->boolean('is_downloadable')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->unsignedBigInteger('featured_image_id')->nullable();
            $table->json('gallery_image_ids')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
