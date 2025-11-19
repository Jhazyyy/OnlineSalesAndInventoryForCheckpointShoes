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
        Schema::create('stock_names', function (Blueprint $table) {
            $table->id();
            $table->string('stock_code')->unique(); // Unique code for stock name
            $table->string('name');                    // Stock name
            $table->text('description')->nullable();   // Optional description
            $table->boolean('is_active')->default(true); // For enabling/disabling
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_names');
    }
};
