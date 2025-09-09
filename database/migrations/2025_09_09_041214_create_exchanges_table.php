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
        Schema::create('exchanges', function (Blueprint $table) {
            $table->id('exchange_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('sales_order_id')->nullable();
            $table->string('exchange_number')->unique();
            $table->string('exchange_type', 50)->default('product_exchange'); // product_exchange, refund_exchange, upgrade_exchange
            $table->string('status', 50)->default('pending'); // pending, approved, processing, completed, cancelled
            $table->string('reason')->nullable();
            $table->decimal('original_total_amount', 12, 2)->default(0);
            $table->decimal('new_total_amount', 12, 2)->default(0);
            $table->decimal('difference_amount', 12, 2)->default(0); // negative for refund, positive for additional payment
            $table->date('exchange_date');
            $table->date('requested_completion_date')->nullable();
            $table->date('actual_completion_date')->nullable();
            $table->text('notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->json('metadata')->nullable(); // For storing additional exchange-specific data
            $table->unsignedBigInteger('processed_by')->nullable(); // User who processed the exchange
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('cascade');
            $table->foreign('sales_order_id')->references('sales_order_id')->on('sales_orders')->onDelete('set null');
            $table->foreign('processed_by')->references('id')->on('users')->onDelete('set null');
            
            // Indexes for better performance
            $table->index(['status', 'exchange_date']);
            $table->index(['customer_id', 'exchange_date']);
            $table->index(['exchange_number']);
            $table->index(['exchange_type', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchanges');
    }
};
