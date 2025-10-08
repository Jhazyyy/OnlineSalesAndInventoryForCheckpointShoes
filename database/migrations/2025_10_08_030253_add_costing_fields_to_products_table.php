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
        Schema::table('products', function (Blueprint $table) {
            // Base selling price (already exists, but we'll add related cost fields)
            
            // Cost components
            $table->decimal('raw_material_cost', 10, 2)->nullable()->after('price')
                ->comment('Cost of raw materials per unit');
            $table->decimal('labor_cost', 10, 2)->nullable()->after('raw_material_cost')
                ->comment('Direct labor cost per unit');
            $table->decimal('overhead_cost', 10, 2)->nullable()->after('labor_cost')
                ->comment('Overhead/indirect costs per unit');
            $table->decimal('manufacturing_cost', 10, 2)->nullable()->after('overhead_cost')
                ->comment('Total manufacturing cost (calculated)');
            
            // Additional costs
            $table->decimal('shipping_cost_per_unit', 10, 2)->nullable()->after('manufacturing_cost')
                ->comment('Average shipping/freight cost per unit');
            $table->decimal('tax_amount_per_unit', 10, 2)->nullable()->after('shipping_cost_per_unit')
                ->comment('Tax amount per unit');
            $table->decimal('handling_cost', 10, 2)->nullable()->after('tax_amount_per_unit')
                ->comment('Handling and packaging cost per unit');
            
            // Total cost and margins
            $table->decimal('total_cost', 10, 2)->nullable()->after('handling_cost')
                ->comment('Total cost per unit (all costs included)');
            $table->decimal('profit_margin', 10, 2)->nullable()->after('total_cost')
                ->comment('Profit margin percentage');
            $table->decimal('profit_amount', 10, 2)->nullable()->after('profit_margin')
                ->comment('Profit amount per unit (price - total_cost)');
            
            // Costing metadata
            $table->string('cost_calculation_method')->default('standard')->after('profit_amount')
                ->comment('Method: standard, average, fifo, lifo');
            $table->timestamp('last_cost_update')->nullable()->after('cost_calculation_method')
                ->comment('Last time costs were updated');
            $table->text('cost_notes')->nullable()->after('last_cost_update')
                ->comment('Notes about costing calculations');
            
            // Add indexes for reporting
            $table->index('total_cost');
            $table->index('profit_margin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['total_cost']);
            $table->dropIndex(['profit_margin']);
            
            $table->dropColumn([
                'raw_material_cost',
                'labor_cost',
                'overhead_cost',
                'manufacturing_cost',
                'shipping_cost_per_unit',
                'tax_amount_per_unit',
                'handling_cost',
                'total_cost',
                'profit_margin',
                'profit_amount',
                'cost_calculation_method',
                'last_cost_update',
                'cost_notes'
            ]);
        });
    }
};
