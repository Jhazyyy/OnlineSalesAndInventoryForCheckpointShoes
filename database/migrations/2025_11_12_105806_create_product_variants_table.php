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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id('variant_id');
            
            // Parent Product Reference (no FK constraint)
            $table->unsignedBigInteger('parent_product_id')->comment('References products.product_id');
            $table->index('parent_product_id');
            
            // Variant Identity
            $table->string('variant_sku')->unique()->comment('Unique SKU for this variant');
            $table->string('variant_name')->comment('Display name for variant, e.g., "Black - Size 7"');
            
            // Variant Attributes
            $table->string('color')->nullable()->comment('Color variant, e.g., Black, Tan');
            $table->string('size')->nullable()->comment('Size variant, e.g., 7, 8, 9, M, L, XL');
            $table->string('material')->nullable()->comment('Material variant, e.g., Nappa, Suede');
            $table->json('additional_attributes')->nullable()->comment('Other custom attributes as JSON');
            
            // Variant-Specific Data
            $table->decimal('price_adjustment', 10, 2)->default(0)->comment('Price difference from parent (+/-)');
            $table->string('barcode')->nullable()->unique();
            $table->string('image')->nullable()->comment('Variant-specific image');
            $table->integer('quantity')->default(0)->comment('Stock quantity for this variant');
            
            // Inventory Thresholds (variant-specific)
            $table->integer('reorder_level')->nullable();
            $table->integer('critical_level')->nullable();
            
            // Status
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Composite index for common queries
            $table->index(['parent_product_id', 'color', 'size']);
            $table->index(['parent_product_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
