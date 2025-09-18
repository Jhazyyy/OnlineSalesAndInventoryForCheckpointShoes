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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('category', 50)->index(); // 'general', 'inventory', 'sales', 'tax', etc.
            $table->string('key', 100)->index(); // Setting key name
            $table->longText('value')->nullable(); // Setting value (JSON or string)
            $table->string('data_type', 20)->default('string'); // 'string', 'number', 'boolean', 'json'
            $table->text('description')->nullable(); // Setting description
            $table->boolean('is_public')->default(false); // Can be accessed by non-admin users
            $table->boolean('is_active')->default(true); // Setting is active
            $table->timestamps();
            
            // Composite unique index for category + key
            $table->unique(['category', 'key'], 'settings_category_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
