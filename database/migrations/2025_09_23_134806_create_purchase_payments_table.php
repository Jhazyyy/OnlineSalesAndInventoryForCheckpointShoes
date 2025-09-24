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
        Schema::create('purchase_payments', function (Blueprint $table) {
            $table->id('payment_id');
            $table->unsignedBigInteger('supplier_id');
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->string('payment_number')->unique();
            $table->string('bill_number')->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('unused_amount', 12, 2)->default(0);
            $table->decimal('bank_charges', 10, 2)->default(0);
            $table->date('payment_date');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'check', 'online', 'gcash', 'other'])->default('cash');
            $table->enum('payment_mode', ['cash', 'bank_transfer', 'icici_bank', 'standard_chartered', 'yes_bank', 'kotak_bank', 'other'])->default('cash');
            $table->string('bank_account')->nullable();
            $table->string('reference_number')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'refunded'])->default('pending');
            $table->text('notes')->nullable();
            $table->string('paid_by')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onDelete('cascade');
            $table->foreign('purchase_order_id')->references('purchase_order_id')->on('purchase_orders')->onDelete('set null');
            
            // Indexes
            $table->index(['supplier_id', 'payment_date']);
            $table->index(['status', 'payment_date']);
            $table->index('payment_method');
            $table->index('payment_mode');
            $table->index('bill_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_payments');
    }
};
