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
            // Inventory threshold levels
            $table->integer('reorder_level')->nullable()->after('quantity')->comment('Minimum level before reorder is triggered');
            $table->integer('critical_level')->nullable()->after('reorder_level')->comment('Critical stock level requiring immediate attention');
            $table->integer('ceiling_level')->nullable()->after('critical_level')->comment('Maximum stock level (overstocking threshold)');
            $table->integer('floor_level')->nullable()->after('ceiling_level')->comment('Absolute minimum acceptable stock level');
            
            // Threshold configuration flags
            $table->boolean('auto_reorder_enabled')->default(false)->after('floor_level')->comment('Enable automatic reorder suggestions');
            $table->boolean('threshold_alerts_enabled')->default(true)->after('auto_reorder_enabled')->comment('Enable threshold-based alerts');
            
            // Preferred supplier for auto-reorder
            $table->unsignedBigInteger('preferred_supplier_id')->nullable()->after('threshold_alerts_enabled');
            $table->foreign('preferred_supplier_id')->references('supplier_id')->on('suppliers')->onDelete('set null');
            
            // Lead time and order quantities
            $table->integer('lead_time_days')->nullable()->after('preferred_supplier_id')->comment('Lead time in days for restocking');
            $table->integer('economic_order_quantity')->nullable()->after('lead_time_days')->comment('EOQ - optimal order quantity');
            
            // Last threshold check
            $table->timestamp('last_threshold_check')->nullable()->after('economic_order_quantity');
            
            // Indexes for performance
            $table->index(['reorder_level', 'quantity'], 'idx_reorder_threshold');
            $table->index(['critical_level', 'quantity'], 'idx_critical_threshold');
            $table->index(['threshold_alerts_enabled'], 'idx_alerts_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['preferred_supplier_id']);
            $table->dropIndex('idx_reorder_threshold');
            $table->dropIndex('idx_critical_threshold');
            $table->dropIndex('idx_alerts_enabled');
            
            $table->dropColumn([
                'reorder_level',
                'critical_level',
                'ceiling_level',
                'floor_level',
                'auto_reorder_enabled',
                'threshold_alerts_enabled',
                'preferred_supplier_id',
                'lead_time_days',
                'economic_order_quantity',
                'last_threshold_check'
            ]);
        });
    }
};
