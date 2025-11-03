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
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id('movement_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('movement_type', ['sale', 'purchase', 'return', 'adjustment', 'transfer_in', 'transfer_out', 'audit', 'waste', 'production', 'initial_stock']);
            $table->integer('quantity_before');
            $table->integer('quantity_change'); // Can be positive or negative
            $table->integer('quantity_after');
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->decimal('total_value', 15, 2)->nullable();
            $table->string('reference_type')->nullable(); // 'sale', 'purchase', 'return', etc.
            $table->unsignedBigInteger('reference_id')->nullable(); // ID from related table
            $table->string('location_from')->nullable();
            $table->string('location_to')->nullable();
            $table->text('notes')->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('confirmed');
            $table->timestamp('movement_date')->nullable();
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['product_id', 'movement_date']);
            $table->index(['movement_type', 'movement_date']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('movement_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};

