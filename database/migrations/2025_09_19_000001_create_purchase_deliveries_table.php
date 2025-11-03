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
        Schema::create('purchase_deliveries', function (Blueprint $table) {
            $table->id('delivery_id');
            $table->string('delivery_number')->unique();
            $table->string('reference_number')->nullable();
            
            // Foreign Keys
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            
            // Delivery Information
            $table->string('carrier')->nullable(); // FedEx, UPS, DHL, etc.
            $table->string('tracking_number')->nullable();
            $table->string('service_type')->nullable(); // Standard, Express, Overnight
            
            // Dates
            $table->date('delivery_date');
            $table->date('scheduled_delivery_date')->nullable();
            $table->date('actual_delivery_date')->nullable();
            $table->dateTime('picked_up_at')->nullable();
            $table->dateTime('delivered_at')->nullable();
            
            // Status
            $table->enum('status', [
                'scheduled',
                'in_transit',
                'out_for_delivery',
                'delivered',
                'delayed',
                'failed',
                'cancelled'
            ])->default('scheduled');
            
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // Address and Contact
            $table->text('delivery_address')->nullable();
            $table->string('recipient_name')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->string('recipient_email')->nullable();
            
            // Package Information
            $table->integer('total_packages')->default(1);
            $table->decimal('total_weight', 10, 2)->nullable();
            $table->json('package_dimensions')->nullable();
            
            // Costs
            $table->decimal('shipping_cost', 15, 2)->default(0);
            $table->decimal('insurance_cost', 15, 2)->default(0);
            $table->decimal('additional_fees', 15, 2)->default(0);
            $table->decimal('total_shipping_cost', 15, 2)->default(0);
            
            // Insurance and Special Handling
            $table->boolean('is_insured')->default(false);
            $table->decimal('insurance_value', 15, 2)->nullable();
            $table->boolean('requires_signature')->default(false);
            $table->boolean('is_fragile')->default(false);
            
            // Quantities and Amounts
            $table->integer('total_quantity_expected')->default(0);
            $table->integer('total_quantity_delivered')->default(0);
            $table->integer('total_quantity_damaged')->default(0);
            $table->decimal('total_amount_expected', 15, 2)->default(0);
            $table->decimal('total_amount_delivered', 15, 2)->default(0);
            
            // Notes
            $table->text('delivery_notes')->nullable();
            $table->text('internal_notes')->nullable();
            $table->text('damage_notes')->nullable();
            $table->text('special_instructions')->nullable();
            
            // Tracking History
            $table->json('tracking_history')->nullable();
            
            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index('delivery_date');
            $table->index('scheduled_delivery_date');
            $table->index('actual_delivery_date');
            $table->index('status');
            $table->index('carrier');
            $table->index('tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_deliveries');
    }
};

