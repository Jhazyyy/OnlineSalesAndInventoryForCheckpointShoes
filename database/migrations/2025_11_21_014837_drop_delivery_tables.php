<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remove delivery_id from purchase_receives (check if column exists first)
        if (Schema::hasColumn('purchase_receives', 'delivery_id')) {
            // Drop foreign key using raw SQL to avoid errors if it doesn't exist
            DB::statement('ALTER TABLE purchase_receives DROP FOREIGN KEY IF EXISTS purchase_receives_delivery_id_foreign');
            
            Schema::table('purchase_receives', function (Blueprint $table) {
                $table->dropColumn('delivery_id');
            });
        }

        // Drop purchase_delivery_items table
        Schema::dropIfExists('purchase_delivery_items');

        // Drop purchase_deliveries table
        Schema::dropIfExists('purchase_deliveries');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate purchase_deliveries table
        Schema::create('purchase_deliveries', function (Blueprint $table) {
            $table->id('delivery_id');
            $table->string('delivery_number')->unique();
            $table->unsignedBigInteger('purchase_order_id');
            $table->string('status')->default('scheduled');
            $table->date('scheduled_date');
            $table->date('actual_delivery_date')->nullable();
            $table->string('tracking_number')->nullable();
            $table->text('delivery_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('purchase_order_id')
                ->references('order_id')
                ->on('purchase_orders')
                ->onDelete('cascade');
        });

        // Recreate purchase_delivery_items table
        Schema::create('purchase_delivery_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->unsignedBigInteger('delivery_id');
            $table->unsignedBigInteger('purchase_order_item_id');
            $table->integer('quantity_shipped');
            $table->integer('quantity_received')->default(0);
            $table->timestamps();

            $table->foreign('delivery_id')
                ->references('delivery_id')
                ->on('purchase_deliveries')
                ->onDelete('cascade');

            $table->foreign('purchase_order_item_id')
                ->references('item_id')
                ->on('purchase_order_items')
                ->onDelete('cascade');
        });

        // Recreate delivery_id in purchase_receives
        Schema::table('purchase_receives', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_id')->nullable()->after('purchase_order_id');
            $table->foreign('delivery_id')
                ->references('delivery_id')
                ->on('purchase_deliveries')
                ->onDelete('set null');
        });
    }
};
