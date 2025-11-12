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
        // Add variant_id to sales_order_items
        Schema::table('sales_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sales_order_items', 'variant_id')) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
                $table->index('variant_id');
            }
        });

        // Add variant_id to purchase_order_items
        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_items', 'variant_id')) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
                $table->index('variant_id');
            }
        });

        // Add variant_id to stock_movements
        Schema::table('stock_movements', function (Blueprint $table) {
            if (!Schema::hasColumn('stock_movements', 'variant_id')) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
                $table->index(['variant_id', 'movement_date']);
            }
        });

        // Add variant_id to inventories
        Schema::table('inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventories', 'variant_id')) {
                $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');
                $table->index(['product_id', 'variant_id']);
                $table->index('variant_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove variant_id from sales_order_items
        Schema::table('sales_order_items', function (Blueprint $table) {
            $table->dropIndex(['variant_id']);
            $table->dropColumn('variant_id');
        });

        // Remove variant_id from purchase_order_items
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropIndex(['variant_id']);
            $table->dropColumn('variant_id');
        });

        // Remove variant_id from stock_movements
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropIndex(['variant_id', 'movement_date']);
            $table->dropColumn('variant_id');
        });

        // Remove variant_id from inventories
        Schema::table('inventories', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'variant_id']);
            $table->dropIndex(['variant_id']);
            $table->dropColumn('variant_id');
        });
    }
};
