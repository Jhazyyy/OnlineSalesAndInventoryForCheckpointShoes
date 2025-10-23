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
        Schema::create('inventory_audit_logs', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('product_id');
            // $table->foreign('product_id')->references('product_id')->on('products')->onDelete('cascade');
            
            $table->enum('action_type', [
                'threshold_update', 'alert_generated', 'alert_updated', 'alert_acknowledged', 'alert_resolved',
                'cycle_count_started', 'cycle_count_completed', 'adjustment_applied',
                'reorder_suggested', 'auto_reorder_triggered', 'stock_level_changed'
            ])->index();
            
            $table->string('description', 500);
            
            // Before and after values for tracking changes
            $table->json('old_values')->nullable()->comment('Previous state before change');
            $table->json('new_values')->nullable()->comment('New state after change');
            
            // Quantity tracking
            $table->integer('old_quantity')->nullable();
            $table->integer('new_quantity')->nullable();
            $table->integer('quantity_change')->nullable();
            
            // User and system tracking
            $table->unsignedBigInteger('user_id')->nullable();
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->string('user_name')->nullable()->comment('Snapshot of user name at time of action');
            
            // Context information
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('source', 50)->nullable()->comment('System, Manual, API, Job, etc.');
            
            // Related entities (nullable since not all audit logs have related entities)
            $table->nullableMorphs('related', 'audit_related_index'); // Can relate to alerts, cycle counts, etc.
            
            // Batch tracking for related actions
            $table->uuid('batch_id')->nullable()->index()->comment('Groups related audit entries');
            
            $table->timestamps();
            
            // Indexes for performance and queries
            $table->index(['product_id', 'action_type', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['action_type', 'created_at']);
            $table->index(['created_at']); // For time-based queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_audit_logs');
    }
};

