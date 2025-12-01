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
        // Add soft deletes to products table
        if (!Schema::hasColumn('products', 'deleted_at')) {
            Schema::table('products', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to sales table
        if (!Schema::hasColumn('sales', 'deleted_at')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to customers table
        if (!Schema::hasColumn('customers', 'deleted_at')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to purchases table
        if (!Schema::hasColumn('purchases', 'deleted_at')) {
            Schema::table('purchases', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to inventories table
        if (!Schema::hasColumn('inventories', 'deleted_at')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to sales_orders table
        if (!Schema::hasColumn('sales_orders', 'deleted_at')) {
            Schema::table('sales_orders', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to invoices table
        if (!Schema::hasColumn('invoices', 'deleted_at')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to suppliers table
        if (!Schema::hasColumn('suppliers', 'deleted_at')) {
            Schema::table('suppliers', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to purchase_orders table
        if (!Schema::hasColumn('purchase_orders', 'deleted_at')) {
            Schema::table('purchase_orders', function (Blueprint $table) {
                $table->softDeletes();
            });
        }

        // Add soft deletes to returns table
        if (!Schema::hasColumn('returns', 'deleted_at')) {
            Schema::table('returns', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('sales', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('purchases', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('inventories', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('sales_orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('returns', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
