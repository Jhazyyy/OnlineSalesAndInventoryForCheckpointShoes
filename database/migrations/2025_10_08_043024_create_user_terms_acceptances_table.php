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
        Schema::create('user_terms_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('terms_id'); // ->constrained() commented out - add FK in separate migration->onDelete('cascade')
            $table->string('version_accepted', 20);
            $table->timestamp('accepted_at');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->json('acceptance_metadata')->nullable(); // Store additional data like device info, location, etc.
            $table->timestamps();
            
            $table->index('user_id');
            $table->index('terms_id');
            $table->index('accepted_at');
            $table->unique(['user_id', 'terms_id', 'version_accepted']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_terms_acceptances');
    }
};

