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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable()->after('name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('username')->nullable()->unique()->after('email');
            $table->string('profile_photo')->nullable()->after('password');
            $table->string('name');
            $table->string('status')->default('active')->after('email_verified_at'); // active, inactive, suspended
            $table->timestamp('last_login_at')->nullable()->after('status');
            $table->integer('login_count')->default(0)->after('last_login_at');
            $table->boolean('is_active')->default(true)->after('login_count');
            $table->string('role')->default('user')->after('is_active'); // admin, manager, user, viewer
            $table->text('bio')->nullable()->after('profile_photo');
            $table->string('department')->nullable()->after('bio');
            $table->string('position')->nullable()->after('department');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->id('id');
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
