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
            // Remove inventory threshold columns
            $table->dropColumn([
                'reorder_level',
                'critical_level',
                'ceiling_level',
                'floor_level',
                'auto_reorder_enabled',
                'threshold_alerts_enabled',
                'last_threshold_check',
                'lead_time_days',
                'economic_order_quantity'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Restore inventory threshold columns
            $table->integer('reorder_level')->nullable()->comment('Minimum level before reorder is triggered');
            $table->integer('critical_level')->nullable()->comment('Critical stock level requiring immediate attention');
            $table->integer('ceiling_level')->nullable()->comment('Maximum stock level (overstocking threshold)');
            $table->integer('floor_level')->nullable()->comment('Absolute minimum stock level');
            $table->boolean('auto_reorder_enabled')->nullable()->comment('Enable automatic reorder for this product');
            $table->boolean('threshold_alerts_enabled')->default(true)->comment('Enable threshold-based alerts');
            $table->integer('lead_time_days')->nullable()->comment('Days between order placement and receipt');
            $table->integer('economic_order_quantity')->nullable()->comment('Optimal order quantity (EOQ)');
            $table->timestamp('last_threshold_check')->nullable();
        });
    }
};
