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
        Schema::create('product_supplier', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('supplier_id');
            $table->decimal('cost', 10, 2)->nullable()->comment('Cost from this supplier');
            $table->boolean('is_primary')->default(false)->comment('Primary/preferred supplier');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Foreign keys
            $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onDelete('cascade');
            
            // Unique constraint to prevent duplicate assignments
            $table->unique(['product_id', 'supplier_id']);
            
            // Indexes
            $table->index('product_id');
            $table->index('supplier_id');
            $table->index('is_primary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_supplier');
    }
};
