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
            $table->foreignId('purchase_order_id'); // ->constrained() commented out - add FK in separate migration
            $table->foreignId('supplier_id'); // ->constrained() commented out - add FK in separate migration
            $table->unsignedBigInteger('delivery_id')->nullable();
            // $table->foreign('delivery_id')->references('delivery_id')->on('purchase_deliveries')->onDelete('set null');

            $table->date('receive_date');
            $table->enum('status', ['in_transit', 'received', 'partially_received', 'damaged', 'cancelled'])->default('in_transit');
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
