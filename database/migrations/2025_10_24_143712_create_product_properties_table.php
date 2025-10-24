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
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->string('property_name')->nullable(); // e.g., 'size', 'color'
            $table->string('property_value')->nullable(); // e.g., '42', 'Red'
            $table->integer('quantity')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('sku')->nullable()->unique();
            $table->string('barcode')->nullable()->unique();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('product_id')
                  ->references('product_id')
                  ->on('products')
                  ->onDelete('cascade');
            
            // Index for faster queries
            $table->index(['product_id', 'is_active']);
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
