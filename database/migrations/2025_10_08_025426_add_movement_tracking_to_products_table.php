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
        Schema::table('products', function (Blueprint $table) {
            // Movement categorization
            $table->string('movement_category')->nullable()->after('description')
                ->comment('fast, slow, non-moving, or null for uncategorized');
            
            // Movement metrics
            $table->integer('total_sales_quantity')->default(0)->after('movement_category')
                ->comment('Total quantity sold over tracked period');
            $table->decimal('movement_velocity', 10, 2)->nullable()->after('total_sales_quantity')
                ->comment('Sales per day average');
            $table->integer('days_since_last_sale')->nullable()->after('movement_velocity')
                ->comment('Number of days since last sale');
            $table->date('last_sale_date')->nullable()->after('days_since_last_sale')
                ->comment('Date of most recent sale');
            
            // Movement analysis period
            $table->date('movement_analysis_start_date')->nullable()->after('last_sale_date')
                ->comment('Start date for movement calculation');
            $table->date('movement_analysis_end_date')->nullable()->after('movement_analysis_start_date')
                ->comment('End date for movement calculation');
            $table->timestamp('last_movement_check')->nullable()->after('movement_analysis_end_date')
                ->comment('Last time movement was calculated');
            
            // Promotional flags
            $table->boolean('is_promotional')->default(false)->after('last_movement_check')
                ->comment('Product marked for promotion');
            $table->text('promotional_reason')->nullable()->after('is_promotional')
                ->comment('Reason for promotion (e.g., slow moving, overstocked)');
            
            // Add indexes for better query performance
            $table->index('movement_category');
            $table->index('last_sale_date');
            $table->index('is_promotional');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['movement_category']);
            $table->dropIndex(['last_sale_date']);
            $table->dropIndex(['is_promotional']);
            
            $table->dropColumn([
                'movement_category',
                'total_sales_quantity',
                'movement_velocity',
                'days_since_last_sale',
                'last_sale_date',
                'movement_analysis_start_date',
                'movement_analysis_end_date',
                'last_movement_check',
                'is_promotional',
                'promotional_reason'
            ]);
        });
    }
};
