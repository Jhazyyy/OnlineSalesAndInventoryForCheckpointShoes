<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Column already exists in the create migration, just add unique constraint
            if (Schema::hasColumn('purchase_orders', 'reference_number')) {
                $table->unique('reference_number', 'purchase_orders_reference_number_unique');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            // Only drop the unique constraint, not the column since it's part of the create migration
            try {
                $table->dropUnique('purchase_orders_reference_number_unique');
            } catch (\Exception $e) {
                // Index might not exist, ignore the error
            }
        });
    }
};
