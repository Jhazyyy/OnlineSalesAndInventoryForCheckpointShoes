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
        Schema::create('sales_order_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->foreignId('order_id');
            $table->foreignId('product_id');
            $table->unsignedBigInteger('variant_id')->nullable()->comment('References product_variants.variant_id if item is a variant');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2);
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['order_id']);
            $table->index(['product_id']);
            $table->index(['variant_id']);
            // Remove unique constraint to allow same product with different variants
            // $table->unique(['order_id', 'product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_order_items');
    }
};

