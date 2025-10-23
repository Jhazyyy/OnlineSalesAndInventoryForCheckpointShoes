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
        Schema::create('shipments', function (Blueprint $table) {
            $table->id('shipment_id');
            $table->string('shipment_number')->unique();
            
            // Relationships
            $table->unsignedBigInteger('sales_order_id');
            // $table->foreign('sales_order_id')->references('order_id')->on('sales_orders')->onDelete('cascade');
            
            // Shipment Details
            $table->string('carrier')->nullable(); // UPS, FedEx, DHL, etc.
            $table->string('service_type')->nullable(); // Ground, Express, Overnight, etc.
            $table->string('tracking_number')->nullable()->unique();
            $table->string('reference_number')->nullable();
            
            // Status and Priority
            $table->enum('status', ['pending', 'preparing', 'shipped', 'in_transit', 'out_for_delivery', 'delivered', 'exception', 'returned', 'cancelled'])->default('pending');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // Dates
            $table->date('shipment_date')->nullable();
            $table->date('expected_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->datetime('picked_up_at')->nullable();
            $table->datetime('delivered_at')->nullable();
            
            // Addresses
            $table->text('shipping_address');
            $table->text('billing_address')->nullable();
            $table->text('return_address')->nullable();
            
            // Recipient Information
            $table->string('recipient_name');
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_email')->nullable();
            
            // Package Information
            $table->integer('total_packages')->default(1);
            $table->decimal('total_weight', 8, 2)->nullable(); // in kg
            $table->json('package_dimensions')->nullable(); // JSON array of dimensions
            
            // Costs
            $table->decimal('shipping_cost', 10, 2)->default(0.00);
            $table->decimal('insurance_cost', 10, 2)->default(0.00);
            $table->decimal('additional_fees', 10, 2)->default(0.00);
            $table->decimal('total_shipping_cost', 10, 2)->default(0.00);
            
            // Insurance and Special Services
            $table->boolean('is_insured')->default(false);
            $table->decimal('insurance_value', 10, 2)->default(0.00);
            $table->boolean('requires_signature')->default(false);
            $table->boolean('is_fragile')->default(false);
            $table->boolean('is_perishable')->default(false);
            
            // Notes and Instructions
            $table->text('special_instructions')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->text('internal_notes')->nullable();
            
            // Tracking Updates (JSON array of tracking history)
            $table->json('tracking_history')->nullable();
            
            // Additional metadata
            $table->string('created_by')->nullable(); // User who created the shipment
            $table->string('updated_by')->nullable(); // Last user who updated
            
            $table->timestamps();
            
            // Indexes for better performance
            $table->index(['status']);
            $table->index(['shipment_date']);
            $table->index(['carrier']);
            $table->index(['expected_delivery_date']);
            $table->index(['priority']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

