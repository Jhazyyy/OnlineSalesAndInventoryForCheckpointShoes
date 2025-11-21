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
        // First update any 'approved' statuses to 'ordered' since we're removing 'approved'
        DB::statement("UPDATE purchase_orders SET status = 'ordered' WHERE status = 'approved'");
        
        // Then modify the enum to replace 'approved' with 'completed'
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('pending', 'ordered', 'cancelled', 'completed') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'completed' statuses back to 'ordered'
        DB::statement("UPDATE purchase_orders SET status = 'ordered' WHERE status = 'completed'");
        
        // Revert the enum back to include 'approved' instead of 'completed'
        DB::statement("ALTER TABLE purchase_orders MODIFY COLUMN status ENUM('pending', 'approved', 'ordered', 'cancelled') NOT NULL DEFAULT 'pending'");
    }
};
