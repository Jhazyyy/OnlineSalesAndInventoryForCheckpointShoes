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
        Schema::create('terms_and_conditions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->enum('type', ['terms_of_service', 'privacy_policy', 'data_privacy', 'refund_policy', 'shipping_policy', 'cookie_policy'])->default('terms_of_service');
            $table->longText('content');
            $table->string('version', 20);
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_acceptance')->default(true);
            $table->timestamp('effective_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('change_summary')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index('type');
            $table->index('is_active');
            $table->index('version');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms_and_conditions');
    }
};

