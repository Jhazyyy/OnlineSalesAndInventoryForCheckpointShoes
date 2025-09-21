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
            // Last supplier that provided this product
            $table->unsignedBigInteger('last_supplier_id')->nullable()->after('preferred_supplier_id');
            $table->foreign('last_supplier_id')->references('supplier_id')->on('suppliers')->onDelete('set null');
            
            // When was this product last received from supplier
            $table->timestamp('last_received_at')->nullable()->after('last_supplier_id');
            
            // Last purchase price from supplier (for cost tracking)
            $table->decimal('last_purchase_price', 10, 2)->nullable()->after('last_received_at');
            
            // Index for supplier-based queries
            $table->index(['last_supplier_id'], 'idx_last_supplier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['last_supplier_id']);
            $table->dropIndex('idx_last_supplier');
            $table->dropColumn([
                'last_supplier_id',
                'last_received_at', 
                'last_purchase_price'
            ]);
        });
    }
};