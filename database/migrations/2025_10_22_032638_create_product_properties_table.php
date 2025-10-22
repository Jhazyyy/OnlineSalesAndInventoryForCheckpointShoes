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
        Schema::create('product_properties', function (Blueprint $table) {
            $table->id('property_id');
            $table->unsignedBigInteger('product_id');
            $table->string('property_name')->comment('e.g., Size, Color, Style');
            $table->string('property_value')->comment('e.g., 42, Red, Casual');
            $table->integer('quantity')->default(0)->comment('Actual inventory quantity for this variant');
            $table->string('sku')->nullable()->unique()->comment('Stock Keeping Unit for this specific variant');
            $table->decimal('price_adjustment', 10, 2)->default(0)->comment('Price difference from base product price');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            // Foreign key constraint
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            
            // Indexes for better query performance
            $table->index(['product_id', 'is_active']);
            $table->index(['property_name', 'property_value']);
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_properties');
    }
};
