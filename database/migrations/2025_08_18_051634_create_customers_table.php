<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Customers Table Migration - E-commerce Integration
 * 
 * Simplified customer table for sales orders received from e-commerce.
 * Contains only essential fields needed for order display and tracking.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id('customer_id');
            
            // Basic Info
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            
            // Address
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('country')->default('Philippines');
            
            // Business Customer Support
            $table->enum('customer_type', ['individual', 'business'])->default('individual');
            $table->string('company_name')->nullable();
            
            // Status and Notes
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index(['email', 'status']);
            $table->index(['customer_type', 'status']);
            $table->index(['first_name', 'last_name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
