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
        Schema::create('inventory_cycle_counts', function (Blueprint $table) {
            $table->id();
            $table->string('cycle_count_number')->unique()->comment('Unique identifier for the cycle count');
            
            $table->enum('status', ['planned', 'in_progress', 'completed', 'cancelled'])->default('planned')->index();
            $table->enum('count_type', ['full', 'partial', 'abc_analysis', 'random_sample'])->default('partial');
            
            $table->date('scheduled_date');
            $table->date('started_date')->nullable();
            $table->date('completed_date')->nullable();
            
            $table->unsignedBigInteger('created_by');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            
            $table->unsignedBigInteger('assigned_to')->nullable();
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            
            $table->text('description')->nullable();
            $table->text('notes')->nullable();
            
            // Count summary
            $table->integer('total_items')->default(0);
            $table->integer('counted_items')->default(0);
            $table->integer('discrepancies')->default(0);
            $table->decimal('accuracy_percentage', 5, 2)->nullable();
            
            // Variance analysis
            $table->decimal('total_variance_value', 15, 2)->default(0)->comment('Total monetary value of variances');
            $table->integer('positive_variances')->default(0);
            $table->integer('negative_variances')->default(0);
            
            $table->timestamps();
            
            // Indexes
            $table->index(['status', 'scheduled_date']);
            $table->index(['assigned_to', 'status']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventory_cycle_counts');
    }
};
