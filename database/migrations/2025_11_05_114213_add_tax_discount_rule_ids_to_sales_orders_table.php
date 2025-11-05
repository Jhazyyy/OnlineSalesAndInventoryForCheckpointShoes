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
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('tax_rule_id')->nullable()->after('discount_amount');
            $table->unsignedBigInteger('discount_rule_id')->nullable()->after('tax_rule_id');
            
            // Add foreign key constraints
            $table->foreign('tax_rule_id')->references('id')->on('tax_discounts')->onDelete('set null');
            $table->foreign('discount_rule_id')->references('id')->on('tax_discounts')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropForeign(['tax_rule_id']);
            $table->dropForeign(['discount_rule_id']);
            $table->dropColumn(['tax_rule_id', 'discount_rule_id']);
        });
    }
};
