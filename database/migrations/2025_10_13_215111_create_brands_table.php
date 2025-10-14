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
        Schema::create('brands', function (Blueprint $table) {
            $table->id();
            $table->string('brand_code')->unique(); // Optional unique code
            $table->string('name');                    // Brand name
            $table->text('description')->nullable();   // Optional description
            $table->string('logo')->nullable(); // Optional Logo
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
        Schema::dropIfExists('brands');
    }
};
