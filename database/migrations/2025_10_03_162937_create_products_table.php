<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name');
            $table->string('product_brand')->nullable();
            $table->string('product_category')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('price', 10, 2); 
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            
            // Movement categorization
            $table->string('movement_category')->nullable()
                ->comment('fast, slow, non-moving, or null for uncategorized');
            
            // Movement metrics
            $table->integer('total_sales_quantity')->default(0)
                ->comment('Total quantity sold over tracked period');
            $table->decimal('movement_velocity', 10, 2)->nullable()
                ->comment('Sales per day average');
            $table->integer('days_since_last_sale')->nullable()
                ->comment('Number of days since last sale');
            $table->date('last_sale_date')->nullable()
                ->comment('Date of most recent sale');
            
            // Movement analysis period
            $table->date('movement_analysis_start_date')->nullable()
                ->comment('Start date for movement calculation');
            $table->date('movement_analysis_end_date')->nullable()
                ->comment('End date for movement calculation');
            $table->timestamp('last_movement_check')->nullable()
                ->comment('Last time movement was calculated');
            
            // Promotional flags
            $table->boolean('is_promotional')->default(false)
                ->comment('Product marked for promotion');
            $table->text('promotional_reason')->nullable()
                ->comment('Reason for promotion (e.g., slow moving, overstocked)');
            
            // Cost components
            $table->decimal('raw_material_cost', 10, 2)->nullable()
                ->comment('Cost of raw materials per unit');
            $table->decimal('labor_cost', 10, 2)->nullable()
                ->comment('Direct labor cost per unit');
            $table->decimal('overhead_cost', 10, 2)->nullable()
                ->comment('Overhead/indirect costs per unit');
            $table->decimal('manufacturing_cost', 10, 2)->nullable()
                ->comment('Total manufacturing cost (calculated)');
            
            // Additional costs
            $table->decimal('shipping_cost_per_unit', 10, 2)->nullable()
                ->comment('Average shipping/freight cost per unit');
            $table->decimal('tax_amount_per_unit', 10, 2)->nullable()
                ->comment('Tax amount per unit');
            $table->decimal('handling_cost', 10, 2)->nullable()
                ->comment('Handling and packaging cost per unit');
            
            // Total cost and margins
            $table->decimal('total_cost', 10, 2)->nullable()
                ->comment('Total cost per unit (all costs included)');
            $table->decimal('profit_margin', 10, 2)->nullable()
                ->comment('Profit margin percentage');
            $table->decimal('profit_amount', 10, 2)->nullable()
                ->comment('Profit amount per unit (price - total_cost)');
            
            // Costing metadata
            $table->string('cost_calculation_method')->default('standard')
                ->comment('Method: standard, average, fifo, lifo');
            $table->timestamp('last_cost_update')->nullable()
                ->comment('Last time costs were updated');
            $table->text('cost_notes')->nullable()
                ->comment('Notes about costing calculations');
            
            $table->timestamps();

            // Preferred supplier (must come before 'last_supplier_id')
            $table->unsignedBigInteger('preferred_supplier_id')->nullable();
            // $table->foreign('preferred_supplier_id')->references('supplier_id')->on('suppliers')->onDelete('set null');

            // Last supplier
            $table->unsignedBigInteger('last_supplier_id')->nullable();
            // $table->foreign('last_supplier_id')->references('supplier_id')->on('suppliers')->onDelete('set null');

            // Date of last receipt
            $table->timestamp('last_received_at')->nullable();

            // Last purchase price
            $table->decimal('last_purchase_price', 10, 2)->nullable();

            // Thresholds
            $table->integer('reorder_level')->nullable()->comment('Minimum level before reorder is triggered');
            $table->integer('critical_level')->nullable()->comment('Critical stock level requiring immediate attention');
            $table->integer('ceiling_level')->nullable()->comment('Maximum stock level (overstocking threshold)');
            $table->integer('floor_level')->nullable()->comment('Absolute minimum acceptable stock level');

            // Threshold config
            $table->boolean('auto_reorder_enabled')->default(false)->comment('Enable automatic reorder suggestions');
            $table->boolean('threshold_alerts_enabled')->default(true)->comment('Enable threshold-based alerts');

            // Lead time and EOQ
            $table->integer('lead_time_days')->nullable()->comment('Lead time in days for restocking');
            $table->integer('economic_order_quantity')->nullable()->comment('EOQ - optimal order quantity');

            // Last threshold check
            $table->timestamp('last_threshold_check')->nullable();

            // Indexes
            $table->index(['last_supplier_id'], 'idx_last_supplier');
            $table->index(['threshold_alerts_enabled'], 'idx_alerts_enabled');
            $table->index('movement_category');
            $table->index('last_sale_date');
            $table->index('is_promotional');
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

