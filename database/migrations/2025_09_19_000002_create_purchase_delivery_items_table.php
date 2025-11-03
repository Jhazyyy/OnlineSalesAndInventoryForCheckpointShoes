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
        Schema::create('purchase_delivery_items', function (Blueprint $table) {
            $table->id('item_id');
            
            // Foreign Keys
            $table->unsignedBigInteger('delivery_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('purchase_order_item_id')->nullable();
            
            // Quantities
            $table->integer('quantity_expected')->default(0);
            $table->integer('quantity_delivered')->default(0);
            $table->integer('quantity_damaged')->default(0);
            $table->integer('quantity_missing')->default(0);
            
            // Pricing
            $table->decimal('unit_price', 15, 2)->default(0);
            $table->decimal('line_total', 15, 2)->default(0);
            
            // Condition and Quality
            $table->enum('condition', ['good', 'damaged', 'partial', 'missing'])->default('good');
            $table->text('item_notes')->nullable();
            
            // Audit
            $table->timestamps();
            
            // Indexes
            $table->index('delivery_id');
            $table->index('product_id');
            $table->index('condition');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_delivery_items');
    }
};

