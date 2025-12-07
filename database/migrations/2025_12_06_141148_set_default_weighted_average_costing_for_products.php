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
        // Update all products that have manual costing or no costing method set
        // to use weighted_average costing from purchase orders
        // This ensures Cost of Goods Sold (COGS) reflects actual purchase costs
        DB::table('products')
            ->where(function($query) {
                $query->where('cost_calculation_method', 'manual')
                      ->orWhereNull('cost_calculation_method')
                      ->orWhere('cost_calculation_method', '');
            })
            ->update([
                'cost_calculation_method' => 'weighted_average',
                'updated_at' => now()
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to manual costing
        DB::table('products')
            ->where('cost_calculation_method', 'weighted_average')
            ->update([
                'cost_calculation_method' => 'manual',
                'updated_at' => now()
            ]);
    }
};
