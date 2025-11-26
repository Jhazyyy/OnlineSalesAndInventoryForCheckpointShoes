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
            $table->decimal('markup_percentage', 10, 2)->nullable()->after('price')->comment('Markup percentage to add to cost');
            $table->decimal('markup_price', 10, 2)->nullable()->after('markup_percentage')->comment('Calculated selling price with markup applied');
            $table->enum('price_source', ['manual', 'markup', 'costing'])->default('manual')->after('markup_price')->comment('Source of selling price: manual, markup, or costing');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['markup_percentage', 'markup_price', 'price_source']);
        });
    }
};
