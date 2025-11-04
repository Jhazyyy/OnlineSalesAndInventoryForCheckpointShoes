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
        Schema::table('purchase_receive_items', function (Blueprint $table) {
            $table->boolean('update_product_price')->default(false)->after('unit_price')
                ->comment('Flag to indicate whether to update product master price upon successful receipt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_receive_items', function (Blueprint $table) {
            $table->dropColumn('update_product_price');
        });
    }
};
