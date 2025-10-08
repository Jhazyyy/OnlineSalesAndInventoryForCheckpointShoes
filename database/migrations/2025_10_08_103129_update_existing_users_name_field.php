<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing users' name field based on first_name and last_name
        $users = DB::table('users')->get();
        
        foreach ($users as $user) {
            $firstName = $user->first_name ?? '';
            $lastName = $user->last_name ?? '';
            $fullName = trim($firstName . ' ' . $lastName);
            
            // If both are empty or result is empty, set to "Test User"
            $name = $fullName ?: 'Test User';
            
            DB::table('users')
                ->where('id', $user->id)
                ->update(['name' => $name]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this migration as it's just updating data
    }
};
