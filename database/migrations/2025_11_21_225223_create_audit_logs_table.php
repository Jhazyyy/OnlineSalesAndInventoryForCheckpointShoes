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
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id('audit_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_name')->nullable(); // Store user name for historical record
            $table->string('action'); // create, update, delete, login, logout, etc.
            $table->string('module'); // inventory, sales, purchases, users, etc.
            $table->string('record_type')->nullable(); // Model name (Product, SalesOrder, etc.)
            $table->unsignedBigInteger('record_id')->nullable(); // ID of the affected record
            $table->string('record_identifier')->nullable(); // Human-readable identifier (product name, order number, etc.)
            $table->text('description'); // Human-readable description of the action
            $table->json('old_values')->nullable(); // Previous values for updates
            $table->json('new_values')->nullable(); // New values for creates/updates
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->enum('severity', ['info', 'warning', 'critical'])->default('info');
            $table->timestamps();

            // Indexes for better query performance
            $table->index('user_id');
            $table->index('module');
            $table->index('action');
            $table->index('record_type');
            $table->index('created_at');
            $table->index(['module', 'action']);
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
