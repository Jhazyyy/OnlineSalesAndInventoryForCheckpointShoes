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
        Schema::create('purchase_returns', function (Blueprint $table) {
            $table->id('return_id');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('supplier_id');
            $table->integer('quantity');
            $table->decimal('price', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->date('return_date');
            $table->enum('return_status', ['pending', 'approved', 'rejected', 'processed', 'refunded'])->default('pending');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            // $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
            // $table->foreign('product_id')->references('product_id')->on('products')->onDelete('set null');
            // $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
            // $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes
            $table->index(['return_status', 'return_date']);
            $table->index(['supplier_id', 'return_date']);
            $table->index(['product_id', 'return_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_returns');
    }
};

