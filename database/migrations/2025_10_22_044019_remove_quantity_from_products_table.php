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
            // Drop indexes that reference the quantity column
            $table->dropIndex('idx_reorder_threshold');
            $table->dropIndex('idx_critical_threshold');
            
            // Drop the quantity column
            $table->dropColumn('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add back the quantity column
            $table->integer('quantity')->after('product_category');
            
            // Recreate the indexes
            $table->index(['reorder_level', 'quantity'], 'idx_reorder_threshold');
            $table->index(['critical_level', 'quantity'], 'idx_critical_threshold');
        });
    }
};
