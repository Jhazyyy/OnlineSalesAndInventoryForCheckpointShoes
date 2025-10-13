<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create test user with default "Test User" name (no first_name/last_name)
        // This will display as "Test User" via the accessor
        User::factory()->create([
            'name' => 'Test User',
            'first_name' => null,
            'last_name' => null,
            'email' => 'test@example.com',
        ]);
        

        
        // Seed suppliers
        // $this->call(SupplierSeeder::class);
        
        // // Seed customers
        $this->call(CustomerSeeder::class);

        // Seed products
        // $this->call(ProductSeeder::class);

        // Seed users
        $this->call(UserSeeder::class);
    }
}
