<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');
            $table->string('product_name');
            $table->string('product_brand')->nullable();
            $table->string('product_category')->nullable();
            $table->integer('quantity');
            $table->decimal('price', 10, 2); 
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            // Preferred supplier (must come before 'last_supplier_id')
            $table->unsignedBigInteger('preferred_supplier_id')->nullable();
            $table->foreign('preferred_supplier_id')->references('supplier_id')->on('suppliers')->onDelete('set null');

            // Last supplier
            $table->unsignedBigInteger('last_supplier_id')->nullable();
            $table->foreign('last_supplier_id')->references('supplier_id')->on('suppliers')->onDelete('set null');

            // Date of last receipt
            $table->timestamp('last_received_at')->nullable();

            // Last purchase price
            $table->decimal('last_purchase_price', 10, 2)->nullable();

            // Thresholds
            $table->integer('reorder_level')->nullable()->comment('Minimum level before reorder is triggered');
            $table->integer('critical_level')->nullable()->comment('Critical stock level requiring immediate attention');
            $table->integer('ceiling_level')->nullable()->comment('Maximum stock level (overstocking threshold)');
            $table->integer('floor_level')->nullable()->comment('Absolute minimum acceptable stock level');

            // Threshold config
            $table->boolean('auto_reorder_enabled')->default(false)->comment('Enable automatic reorder suggestions');
            $table->boolean('threshold_alerts_enabled')->default(true)->comment('Enable threshold-based alerts');

            // Lead time and EOQ
            $table->integer('lead_time_days')->nullable()->comment('Lead time in days for restocking');
            $table->integer('economic_order_quantity')->nullable()->comment('EOQ - optimal order quantity');

            // Last threshold check
            $table->timestamp('last_threshold_check')->nullable();

            // Indexes
            $table->index(['last_supplier_id'], 'idx_last_supplier');
            $table->index(['reorder_level', 'quantity'], 'idx_reorder_threshold');
            $table->index(['critical_level', 'quantity'], 'idx_critical_threshold');
            $table->index(['threshold_alerts_enabled'], 'idx_alerts_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
