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
        Schema::create('purchase_receive_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->foreignId('receive_id')->constrained('purchase_receives', 'receive_id')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products', 'product_id');
            $table->foreignId('purchase_order_item_id')->nullable()->constrained('purchase_order_items', 'item_id');
            $table->integer('quantity_expected')->default(0);
            $table->integer('quantity_received')->default(0);
            $table->integer('quantity_damaged')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('condition', ['good', 'damaged', 'expired', 'partial'])->default('good');
            $table->text('item_notes')->nullable();
            $table->timestamps();
            
            $table->index(['receive_id', 'product_id']);
            $table->index(['product_id', 'condition']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_receive_items');
    }
};
