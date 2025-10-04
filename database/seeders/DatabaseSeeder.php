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

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
        
        // Product::factory()->create([
        //     'product_name' => 'Sakong',
        //     'product_brand' => 'Shoe',
        //     'quantity' => 12,
        //     'price' => 1500.60,
        // ]);

        // Product::factory()->count(6)->create();
        
        // Seed suppliers
        $this->call(SupplierSeeder::class);
        
        // // Seed customers
        $this->call(CustomerSeeder::class);

        // Seed products
        $this->call(ProductSeeder::class);
    }
}
