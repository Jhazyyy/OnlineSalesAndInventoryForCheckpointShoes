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
        Schema::create('purchase_receives', function (Blueprint $table) {
            $table->id('receive_id');
            $table->string('receive_number', 50)->unique();
            $table->string('reference_number', 100)->unique();
            
            // Foreign Keys
            $table->foreignId('purchase_order_id');
            $table->foreignId('supplier_id');
            $table->unsignedBigInteger('delivery_id')->nullable();

            $table->date('receive_date');
            $table->enum('status', ['in_transit', 'received', 'partially_received', 'damaged', 'cancelled'])->default('in_transit');
            $table->boolean('is_short_closed')->default(false);
            $table->text('short_close_reason')->nullable();
            $table->timestamp('short_closed_at')->nullable();
            $table->unsignedBigInteger('short_closed_by')->nullable();
            $table->integer('total_quantity_expected')->default(0);
            $table->integer('total_quantity_received')->default(0);
            $table->decimal('total_amount_expected', 12, 2)->default(0);
            $table->decimal('total_amount_received', 12, 2)->default(0);
            $table->text('receiving_notes')->nullable();
            $table->text('damage_notes')->nullable();
            $table->string('receiver_name', 255)->nullable();
            $table->text('delivery_address')->nullable();
            $table->timestamps();

            $table->index(['status', 'receive_date']);
            $table->index(['purchase_order_id', 'receive_date']);
            $table->index(['supplier_id', 'receive_date']);
            $table->index(['delivery_id', 'receive_date']);
            $table->index('is_short_closed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_receives');
    }
};
