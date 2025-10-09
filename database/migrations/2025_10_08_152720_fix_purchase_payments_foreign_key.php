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
        Schema::table('purchase_payments', function (Blueprint $table) {
            // Drop the incorrect foreign key constraint
            $table->dropForeign(['purchase_order_id']);
            
            // Add the correct foreign key constraint
            $table->foreign('purchase_order_id')->references('order_id')->on('purchase_orders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_payments', function (Blueprint $table) {
            // Drop the correct foreign key constraint
            $table->dropForeign(['purchase_order_id']);
            
            // Add back the incorrect one (for rollback)
            $table->foreign('purchase_order_id')->references('purchase_order_id')->on('purchase_orders')->onDelete('set null');
        });
    }
};
