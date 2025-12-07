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
        // Update the cost_calculation_method field to support new automatic costing methods
        DB::statement("ALTER TABLE products MODIFY COLUMN cost_calculation_method VARCHAR(50) DEFAULT 'manual' COMMENT 'Method: manual (default), weighted_average (auto from purchases), latest_purchase (last purchase price), standard, fifo, lifo'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE products MODIFY COLUMN cost_calculation_method VARCHAR(50) DEFAULT 'standard' COMMENT 'Method: standard, average, fifo, lifo'");
    }
};
