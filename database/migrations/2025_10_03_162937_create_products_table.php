<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            // Core Product Information
            $table->id('product_id');
            $table->string('product_name');
            $table->string('stock_name')->nullable();
            $table->string('sku')->unique();
            $table->string('barcode')->nullable()->unique();
            $table->string('size')->nullable();
            $table->string('color')->nullable();
            $table->string('product_brand')->nullable();
            $table->string('product_category')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('price', 10, 2)->nullable()->comment('base price'); 
            $table->enum('price_source', ['manual', 'costing'])->default('manual')->comment('Source of selling price: manual or costing');
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            //Stock Name
            $table->unsignedBigInteger('stock_name_id')->nullable();
            $table->foreign('stock_name_id')->references('id')->on('stock_names')->onDelete('set null');

            // Supplier Tracking
            $table->unsignedBigInteger('preferred_supplier_id')->nullable();
            $table->unsignedBigInteger('last_supplier_id')->nullable();
            $table->timestamp('last_received_at')->nullable();
            $table->decimal('last_purchase_price', 10, 2)->nullable();
            
            // Product Movement Tracking
            $table->string('movement_category')->nullable()->comment('fast, slow, non-moving, or null for uncategorized');
            $table->integer('total_sales_quantity')->default(0)->comment('Total quantity sold over tracked period');
            $table->decimal('movement_velocity', 10, 2)->nullable()->comment('Sales per day average');
            $table->integer('days_since_last_sale')->nullable()->comment('Number of days since last sale');
            $table->date('last_sale_date')->nullable()->comment('Date of most recent sale');
            $table->date('movement_analysis_start_date')->nullable()->comment('Start date for movement calculation');
            $table->date('movement_analysis_end_date')->nullable()->comment('End date for movement calculation');
            $table->timestamp('last_movement_check')->nullable()->comment('Last time movement was calculated');
            
            // Product Costing
            $table->decimal('raw_material_cost', 10, 2)->nullable()->comment('Cost of raw materials per unit');
            $table->decimal('labor_cost', 10, 2)->nullable()->comment('Direct labor cost per unit');
            $table->decimal('overhead_cost', 10, 2)->nullable()->comment('Overhead/indirect costs per unit');
            $table->decimal('manufacturing_cost', 10, 2)->nullable()->comment('Total manufacturing cost (calculated)');
            $table->decimal('shipping_cost_per_unit', 10, 2)->nullable()->comment('Average shipping/freight cost per unit');
            $table->decimal('tax_amount_per_unit', 10, 2)->nullable()->comment('Tax amount per unit');
            $table->decimal('handling_cost', 10, 2)->nullable()->comment('Handling and packaging cost per unit');
            $table->decimal('total_cost', 10, 2)->nullable()->comment('Total cost per unit (all costs included)');
            $table->decimal('profit_margin', 10, 2)->nullable()->comment('Profit margin percentage');
            $table->decimal('profit_amount', 10, 2)->nullable()->comment('Profit amount per unit (price - total_cost)');
            $table->string('cost_calculation_method')->default('standard')->comment('Method: standard, average, fifo, lifo');
            $table->timestamp('last_cost_update')->nullable()->comment('Last time costs were updated');
            $table->text('cost_notes')->nullable()->comment('Notes about costing calculations');

            // Indexes for performance
            $table->index('last_supplier_id');
            $table->index('threshold_alerts_enabled');
            $table->index('movement_category');
            $table->index('last_sale_date');
            
            // Soft Deletes
            $table->softDeletes();
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

