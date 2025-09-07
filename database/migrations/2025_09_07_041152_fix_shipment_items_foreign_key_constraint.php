<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table to fix foreign key constraint
        // First, let's disable foreign key checks
        DB::statement('PRAGMA foreign_keys=OFF');
        
        // Create a temporary table with the correct foreign key
        Schema::create('shipment_items_temp', function (Blueprint $table) {
            $table->id('shipment_item_id');
            $table->unsignedBigInteger('shipment_id');
            $table->unsignedBigInteger('sales_order_item_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->string('product_sku');
            $table->string('product_name');
            $table->integer('quantity_shipped');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('line_total', 10, 2);
            $table->string('package_number')->nullable();
            $table->decimal('item_weight', 8, 3)->nullable();
            $table->json('item_dimensions')->nullable();
            $table->enum('condition', ['new', 'used', 'refurbished', 'damaged'])->default('new');
            $table->enum('status', ['pending', 'picked', 'packed', 'shipped', 'delivered', 'returned', 'damaged'])->default('pending');
            $table->json('serial_numbers')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('quality_checked')->default(false);
            $table->string('checked_by')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
            
            // Foreign key constraints with correct references
            $table->foreign('shipment_id')->references('shipment_id')->on('shipments')->onDelete('cascade');
            $table->foreign('sales_order_item_id')->references('item_id')->on('sales_order_items')->onDelete('set null');
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
        });
        
        // Copy data from the old table to the new one
        DB::statement('INSERT INTO shipment_items_temp SELECT * FROM shipment_items');
        
        // Drop the old table
        Schema::dropIfExists('shipment_items');
        
        // Rename the temporary table
        Schema::rename('shipment_items_temp', 'shipment_items');
        
        // Re-enable foreign key checks
        DB::statement('PRAGMA foreign_keys=ON');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This would be complex to reverse, so we'll leave it as is
        // The old constraint was incorrect anyway
    }
};
