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
        Schema::create('inventory_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            // $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            
            $table->enum('alert_type', ['low_stock', 'critical_stock', 'overstock', 'out_of_stock', 'reorder_needed'])->index();
            $table->enum('severity', ['info', 'warning', 'critical', 'urgent'])->default('warning');
            
            $table->string('message', 500);
            $table->json('alert_data')->nullable()->comment('Additional alert context (current quantity, thresholds, etc.)');
            
            $table->enum('status', ['active', 'acknowledged', 'resolved', 'dismissed'])->default('active')->index();
            
            $table->unsignedBigInteger('acknowledged_by')->nullable();
            // $table->foreign('acknowledged_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('acknowledged_at')->nullable();
            
            $table->unsignedBigInteger('resolved_by')->nullable();
            // $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            
            $table->text('resolution_notes')->nullable();
            
            // Auto-dismissal settings
            $table->boolean('auto_dismiss')->default(true)->comment('Automatically dismiss when condition is resolved');
            $table->timestamp('expires_at')->nullable()->comment('Alert expiration time');
            
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['product_id', 'alert_type']);
            $table->index(['status', 'created_at']);
            $table->index(['severity', 'status']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_alerts');
    }
};

