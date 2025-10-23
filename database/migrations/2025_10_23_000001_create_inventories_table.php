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
        Schema::create('inventories', function (Blueprint $table) {
            $table->id('inventory_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('property_id')->nullable();
            $table->string('sku')->nullable();
            $table->string('location')->nullable();
            $table->integer('quantity_on_hand')->default(0);
            $table->integer('quantity_reserved')->default(0);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->timestamp('last_movement_at')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'property_id']);
            $table->index(['sku']);
            $table->index(['location']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};
