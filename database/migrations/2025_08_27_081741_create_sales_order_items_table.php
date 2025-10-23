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
            $table->foreignId('order_id'); // ->constrained() commented out - add FK in separate migration->onDelete('cascade')
            $table->foreignId('product_id'); // ->constrained() commented out - add FK in separate migration->onDelete('cascade')
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('line_total', 10, 2); // (quantity * unit_price) - discount_amount
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['order_id']);
            $table->index(['product_id']);
            $table->unique(['order_id', 'product_id']); // Prevent duplicate products in same order
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

