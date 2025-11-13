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
            // EOQ calculation fields
            $table->decimal('annual_demand', 10, 2)->nullable()->after('economic_order_quantity')->comment('Annual demand quantity for EOQ calculation');
            $table->decimal('ordering_cost', 10, 2)->nullable()->after('annual_demand')->comment('Cost per order for EOQ calculation');
            $table->decimal('holding_cost_per_unit', 10, 2)->nullable()->after('ordering_cost')->comment('Annual holding cost per unit for EOQ calculation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['annual_demand', 'ordering_cost', 'holding_cost_per_unit']);
        });
    }
};
