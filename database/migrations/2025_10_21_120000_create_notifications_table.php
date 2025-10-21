<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('type')->nullable();
            $table->string('title');
            $table->text('message')->nullable();
            $table->string('level')->default('info'); // info, success, warning, danger
            $table->string('link')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
