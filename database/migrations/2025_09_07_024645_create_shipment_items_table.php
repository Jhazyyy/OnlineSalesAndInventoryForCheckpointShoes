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
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id('shipment_item_id');
            
            // Relationships
            $table->unsignedBigInteger('shipment_id');
            $table->foreign('shipment_id')->references('shipment_id')->on('shipments')->onDelete('cascade');
            
            $table->unsignedBigInteger('sales_order_item_id')->nullable();
            $table->foreign('sales_order_item_id')->references('item_id')->on('sales_order_items')->onDelete('set null');
            
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            
            // Item Details
            $table->string('product_sku'); // Store SKU for reference
            $table->string('product_name'); // Store name at time of shipment
            $table->integer('quantity_shipped');
            $table->decimal('unit_price', 10, 2); // Price at time of shipment
            $table->decimal('line_total', 10, 2); // Calculated total for this line
            
            // Package Information
            $table->string('package_number')->nullable(); // Which package this item is in
            $table->decimal('item_weight', 8, 2)->nullable(); // Weight of this specific item
            $table->json('item_dimensions')->nullable(); // Dimensions if applicable
            
            // Condition and Status
            $table->enum('condition', ['new', 'used', 'refurbished', 'damaged'])->default('new');
            $table->enum('status', ['pending', 'packed', 'shipped', 'delivered', 'returned', 'damaged'])->default('pending');
            
            // Serial Numbers and Tracking
            $table->json('serial_numbers')->nullable(); // Array of serial numbers if applicable
            $table->text('notes')->nullable(); // Item-specific notes
            
            // Quality Control
            $table->boolean('quality_checked')->default(false);
            $table->string('checked_by')->nullable();
            $table->datetime('checked_at')->nullable();
            
            $table->timestamps();

            
            // Indexes
            $table->index(['shipment_id']);
            $table->index(['product_id']);
            $table->index(['status']);
            $table->index(['package_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
    }
};
