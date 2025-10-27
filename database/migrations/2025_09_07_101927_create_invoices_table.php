<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DEPRECATED: Invoice Management Migration
 * 
 * Invoices are now managed by the e-commerce application.
 * This migration is kept for backward compatibility with existing data.
 * Do not use invoice functionality - it has been removed from the system.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id('invoice_id');
            $table->string('invoice_number')->unique();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('sales_order_id')->nullable();
            $table->date('invoice_date');
            $table->date('due_date')->nullable();
            $table->enum('status', ['draft', 'sent', 'paid', 'overdue', 'cancelled'])->default('draft');
            $table->enum('payment_status', ['pending', 'partial', 'paid', 'refunded'])->default('pending');
            $table->enum('payment_method', ['cash', 'card', 'bank_transfer', 'check'])->nullable();
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->date('payment_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('terms_conditions')->nullable();
            $table->text('billing_address')->nullable();
            $table->integer('payment_terms')->nullable()->comment('Payment terms in days');
            $table->timestamps();
            
            // Foreign key constraints
            // $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            // $table->foreign('sales_order_id')->references('order_id')->on('sales_orders')->onDelete('set null');
            
            // Indexes
            $table->index(['customer_id']);
            $table->index(['sales_order_id']);
            $table->index(['status']);
            $table->index(['payment_status']);
            $table->index(['invoice_date']);
            $table->index(['due_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

