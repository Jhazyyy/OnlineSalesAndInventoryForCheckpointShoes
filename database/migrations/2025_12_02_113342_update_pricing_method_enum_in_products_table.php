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
        // First, update any products with 'costing' to 'manual'
        DB::table('products')
            ->where('pricing_method', 'costing')
            ->update(['pricing_method' => 'manual']);
        
        // For MySQL: Drop and recreate the enum column
        DB::statement("ALTER TABLE products MODIFY COLUMN pricing_method ENUM('manual', 'markup') NOT NULL DEFAULT 'manual'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore the original enum with costing
        DB::statement("ALTER TABLE products MODIFY COLUMN pricing_method ENUM('manual', 'costing', 'markup') NOT NULL DEFAULT 'manual'");
    }
};
