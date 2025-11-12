<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id('return_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('sales_order_id')->nullable();
            $table->integer('quantity')->unsigned()->default(1);
            $table->string('return_status')->default('pending');
            $table->text('reason')->nullable();
            $table->timestamp('return_date')->nullable();
            $table->decimal('price', 10, 2)->unsigned();
            $table->timestamps();

            $table->index('return_status');
            $table->index('return_date');
            $table->index(['product_id', 'return_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
