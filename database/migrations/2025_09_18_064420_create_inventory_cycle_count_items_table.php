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
        Schema::create('inventory_cycle_count_items', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('cycle_count_id');
            // $table->foreign('cycle_count_id')->references('id')->on('inventory_cycle_counts')->onDelete('cascade');
            
            $table->unsignedBigInteger('product_id');
            // $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            
            // System recorded quantities at time of count
            $table->integer('system_quantity');
            $table->decimal('system_unit_cost', 10, 2);
            $table->decimal('system_total_value', 15, 2);
            
            // Physically counted quantities
            $table->integer('counted_quantity')->nullable();
            $table->decimal('counted_unit_cost', 10, 2)->nullable();
            $table->decimal('counted_total_value', 15, 2)->nullable();
            
            // Variance calculations
            $table->integer('quantity_variance')->nullable()->comment('Counted - System quantity');
            $table->decimal('value_variance', 15, 2)->nullable()->comment('Monetary variance');
            $table->decimal('variance_percentage', 5, 2)->nullable();
            
            $table->enum('status', ['pending', 'counted', 'verified', 'adjusted'])->default('pending')->index();
            $table->enum('variance_type', ['none', 'overage', 'shortage'])->nullable();
            
            // Count details
            $table->unsignedBigInteger('counted_by')->nullable();
            // $table->foreign('counted_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('counted_at')->nullable();
            
            $table->unsignedBigInteger('verified_by')->nullable();
            // $table->foreign('verified_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            
            $table->text('notes')->nullable();
            $table->text('discrepancy_reason')->nullable();
            
            // Adjustment tracking
            $table->boolean('requires_adjustment')->default(false);
            $table->boolean('adjustment_applied')->default(false);
            $table->timestamp('adjustment_applied_at')->nullable();
            
            $table->timestamps();
            
            // Composite unique index
            $table->unique(['cycle_count_id', 'product_id']);
            
            // Performance indexes
            $table->index(['status', 'counted_at']);
            $table->index(['requires_adjustment']);
            $table->index(['variance_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_cycle_count_items');
    }
};

