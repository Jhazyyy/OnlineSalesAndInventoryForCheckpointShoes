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
        // Fix any null movement_date values in stock_movements
        DB::table('stock_movements')
            ->whereNull('movement_date')
            ->update(['movement_date' => now()]);
            
        // Fix any null created_at values in stock_movements  
        DB::table('stock_movements')
            ->whereNull('created_at')
            ->update(['created_at' => now()]);
            
        // Fix any null updated_at values in stock_movements
        DB::table('stock_movements')
            ->whereNull('updated_at')
            ->update(['updated_at' => now()]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is for fixing data, no rollback needed
    }
};
