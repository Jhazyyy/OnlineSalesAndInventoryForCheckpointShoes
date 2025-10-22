<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchase_receives', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_receives', 'reference_number')) {
                $table->string('reference_number', 100)->unique()->after('receive_number');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchase_receives', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_receives', 'reference_number')) {
                $table->dropUnique(['reference_number']);
                $table->dropColumn('reference_number');
            }
        });
    }
};
