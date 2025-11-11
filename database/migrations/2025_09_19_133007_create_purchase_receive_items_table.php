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
        Schema::create('purchase_receive_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->foreignId('receive_id'); // ->constrained() commented out - add FK in separate migration->cascadeOnDelete()
            $table->foreignId('product_id'); // ->constrained() commented out - add FK in separate migration
            $table->foreignId('purchase_order_item_id')->nullable()->constrained('purchase_order_items', 'item_id');
            $table->integer('quantity_expected')->default(0);
            $table->integer('quantity_received')->default(0);
            $table->integer('quantity_damaged')->default(0);
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->boolean('update_product_price')->default(false)->comment('Flag to indicate whether to update product master price upon successful receipt');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->enum('condition', ['good', 'damaged', 'expired', 'partial'])->default('good');
            $table->boolean('is_short_closed')->default(false);
            $table->text('short_close_reason')->nullable();
            $table->text('item_notes')->nullable();
            $table->timestamps();
            
            $table->index(['receive_id', 'product_id']);
            $table->index(['product_id', 'condition']);
            $table->index('is_short_closed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_receive_items');
    }
};

