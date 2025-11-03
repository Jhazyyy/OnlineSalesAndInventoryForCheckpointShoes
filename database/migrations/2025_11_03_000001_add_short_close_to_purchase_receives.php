<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add short close functionality to purchase receives and items.
     * This allows users to mark a purchase receive as complete even when
     * the full expected quantity was not received from the supplier.
     */
    public function up(): void
    {
        // Add short close fields to purchase_receives table
        Schema::table('purchase_receives', function (Blueprint $table) {
            $table->boolean('is_short_closed')->default(false)->after('status');
            $table->text('short_close_reason')->nullable()->after('is_short_closed');
            $table->timestamp('short_closed_at')->nullable()->after('short_close_reason');
            $table->unsignedBigInteger('short_closed_by')->nullable()->after('short_closed_at');
            
            $table->index('is_short_closed');
        });

        // Add short close fields to purchase_receive_items table
        Schema::table('purchase_receive_items', function (Blueprint $table) {
            $table->boolean('is_short_closed')->default(false)->after('condition');
            $table->text('short_close_reason')->nullable()->after('is_short_closed');
            
            $table->index('is_short_closed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_receives', function (Blueprint $table) {
            $table->dropIndex(['is_short_closed']);
            $table->dropColumn(['is_short_closed', 'short_close_reason', 'short_closed_at', 'short_closed_by']);
        });

        Schema::table('purchase_receive_items', function (Blueprint $table) {
            $table->dropIndex(['is_short_closed']);
            $table->dropColumn(['is_short_closed', 'short_close_reason']);
        });
    }
};
