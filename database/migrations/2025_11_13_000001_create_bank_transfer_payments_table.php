<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * This table stores bank transfer payment submissions from customers.
     * When a customer chooses bank transfer as payment method, they upload proof
     * which admins review and confirm before inventory is deducted.
     */
    public function up(): void
    {
        Schema::create('bank_transfer_payments', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to sales_orders table
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')
                  ->references('order_id')
                  ->on('sales_orders')
                  ->onDelete('cascade');
            
            // Payment method identifier
            $table->string('payment_method')->default('bank_transfer');
            
            // Bank transfer details
            $table->string('bank_name', 255);
            $table->string('reference_no', 255)->unique();
            $table->decimal('amount', 15, 2);
            
            // Proof of payment (image path)
            $table->string('proof')->nullable()->comment('Path to uploaded payment proof image');
            
            // Payment status workflow: pending → confirmed/cancelled
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');
            
            // Who confirmed/cancelled and when
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            
            // Admin notes (reason for cancellation, etc.)
            $table->text('admin_notes')->nullable();
            
            $table->timestamps();
            
            // Indexes for faster queries
            $table->index('order_id');
            $table->index('status');
            $table->index('reference_no');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transfer_payments');
    }
};
