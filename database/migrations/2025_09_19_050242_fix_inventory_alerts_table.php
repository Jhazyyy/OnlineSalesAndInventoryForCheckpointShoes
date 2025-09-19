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
        Schema::table('inventory_alerts', function (Blueprint $table) {
            // Fix any null created_at values
            DB::table('inventory_alerts')
                ->whereNull('created_at')
                ->update(['created_at' => now()]);
                
            // Ensure status column exists and has proper values
            if (!Schema::hasColumn('inventory_alerts', 'status')) {
                $table->enum('status', ['active', 'acknowledged', 'resolved', 'dismissed'])->default('active')->after('alert_data');
            }
            
            // Update any records without status
            DB::table('inventory_alerts')
                ->whereNull('status')
                ->update(['status' => 'active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is for fixing data, no rollback needed
    }
};
