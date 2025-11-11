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
        Schema::create('tax_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->comment('Unique code for tax/discount');
            $table->string('name', 100)->comment('Name of the tax or discount');
            $table->enum('type', ['tax', 'discount'])->comment('Type: tax or discount');
            $table->enum('applicable_for', ['supplier', 'customer', 'both'])->default('both');
            $table->decimal('rate', 8, 4)->comment('Rate percentage (e.g., 12.5000 for 12.5%)');
            $table->enum('calculation_method', ['percentage', 'fixed'])->default('percentage')->comment('How the value is calculated');
            $table->decimal('fixed_amount', 15, 2)->nullable()->comment('Fixed amount if calculation_method is fixed');
            $table->text('description')->nullable()->comment('Description of the tax or discount');
            $table->enum('applies_to', ['all', 'specific'])->default('all')->comment('Application scope');
            $table->json('applicable_categories')->nullable()->comment('Category IDs if applies_to is specific');
            $table->json('applicable_products')->nullable()->comment('Product IDs if applies_to is specific');
            $table->boolean('is_compound')->default(false)->comment('Whether this is a compound tax/discount');
            $table->integer('priority')->default(0)->comment('Priority order for calculation');
            $table->boolean('is_active')->default(true)->comment('Active status');
            $table->date('valid_from')->nullable()->comment('Start date of validity');
            $table->date('valid_to')->nullable()->comment('End date of validity');
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes
            $table->index('type');
            $table->index('is_active');
            $table->index(['valid_from', 'valid_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_discounts');
    }
};
