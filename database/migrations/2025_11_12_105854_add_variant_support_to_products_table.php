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
            // Add variant support columns
            $table->boolean('is_parent')->default(false)->after('product_id')
                ->comment('True if this product has variants (parent product)');
            $table->boolean('has_variants')->default(false)->after('is_parent')
                ->comment('Indicates if product has child variants');
            
            // Add index for variant queries
            $table->index(['is_parent', 'has_variants']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['is_parent', 'has_variants']);
            $table->dropColumn(['is_parent', 'has_variants']);
        });
    }
};
