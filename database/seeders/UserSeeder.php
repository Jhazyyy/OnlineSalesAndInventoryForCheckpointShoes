<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a single user
        User::factory()->create();

        // Create multiple users
        // User::factory()->count(10)->create();

        // Create an unverified user
        User::factory()->unverified()->create();

        // Create a user with custom attributes
        User::factory()->create([
            'email' => 'custom@example.com',
            'first_name' => 'John',
        ]);
    }
}
