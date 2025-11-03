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
        Schema::create('supplier_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('supplier_id');
            $table->string('activity_type'); // purchase_order_created, purchase_order_received, purchase_order_cancelled, payment_made, etc.
            $table->string('description');
            $table->unsignedBigInteger('related_id')->nullable(); // ID of related record (purchase_order_id, payment_id, etc.)
            $table->string('related_type')->nullable(); // Type of related record (PurchaseOrder, Payment, etc.)
            $table->decimal('amount', 15, 2)->nullable();
            $table->json('metadata')->nullable(); // Additional data
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();

            $table->foreign('supplier_id')->references('supplier_id')->on('suppliers')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['supplier_id', 'created_at']);
            $table->index('activity_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier_activity_logs');
    }
};
