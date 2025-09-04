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
        Schema::create('packages', function (Blueprint $table) {
            $table->id('package_id');
            $table->string('package_name');
            $table->string('package_type')->default('standard'); // standard, custom, bundle
            $table->text('description')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->string('dimensions')->nullable(); // LxWxH format
            $table->decimal('price', 10, 2);
            $table->integer('quantity')->default(0);
            $table->string('tracking_code')->nullable()->unique();
            $table->enum('status', ['active', 'inactive', 'discontinued'])->default('active');
            $table->string('image')->nullable();
            $table->json('contents')->nullable(); // Array of product IDs and quantities
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
