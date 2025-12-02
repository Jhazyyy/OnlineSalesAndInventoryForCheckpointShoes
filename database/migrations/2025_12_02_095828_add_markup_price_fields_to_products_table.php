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
            // Check if price_source exists, if so add after it, otherwise add after price
            if (Schema::hasColumn('products', 'price_source')) {
                $table->unsignedBigInteger('markup_price_id')->nullable()->after('price_source');
            } else {
                $table->unsignedBigInteger('markup_price_id')->nullable()->after('price');
            }
            $table->foreign('markup_price_id')->references('id')->on('markup_prices')->onDelete('set null');
            
            // Add pricing_method enum
            if (Schema::hasColumn('products', 'price_source')) {
                $table->enum('pricing_method', ['manual', 'costing', 'markup'])->default('manual')
                      ->comment('Pricing method: manual (fixed price), costing (calculated from costs), or markup (applied from markup_prices)')
                      ->after('price_source');
            } else {
                $table->enum('pricing_method', ['manual', 'costing', 'markup'])->default('manual')
                      ->comment('Pricing method: manual (fixed price), costing (calculated from costs), or markup (applied from markup_prices)')
                      ->after('price');
            }
        });
        
        // Drop old price_source column if it exists
        if (Schema::hasColumn('products', 'price_source')) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropColumn('price_source');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Drop new columns
            $table->dropForeign(['markup_price_id']);
            $table->dropColumn(['markup_price_id', 'pricing_method']);
        });
    }
};
