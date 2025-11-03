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
        Schema::create('exchange_items', function (Blueprint $table) {
            $table->id('exchange_item_id');
            $table->unsignedBigInteger('exchange_id');
            $table->string('item_type', 20); // 'original' or 'new'
            $table->unsignedBigInteger('product_id');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 12, 2);
            $table->string('condition', 50)->nullable(); // new, used, damaged, etc.
            $table->text('notes')->nullable();
            $table->timestamps();
            
            // Indexes
            $table->index(['exchange_id', 'item_type']);
            $table->index(['product_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exchange_items');
    }
};

