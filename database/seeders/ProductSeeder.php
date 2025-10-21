<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Product::factory(5)->create(); // Create 50 products for testing

        Product::factory()
            ->count(5)
            ->create([
                'product_category' => 'Electronics',
                'product_brand' => 'TechCorp',
            ]);

        Product::factory()
            ->count(5)
            ->create([
                'product_category' => 'Home Appliances',
                'product_brand' => 'HomeEase',
            ]);

        Product::factory()
            ->count(5)
            ->create([
                'product_category' => 'Accessories',
                'product_brand' => 'StylePlus',
            ]);
        Product::factory()
            ->count(5)
            ->create([
                'product_category' => 'Gadgets',
                'product_brand' => 'GadgetWorld',
            ]);
        Product::factory()
            ->count(5)
            ->create([
                'product_category' => 'Office Supplies',
                'product_brand' => 'OfficePro',
            ]);


        // Create some out-of-stock products
        Product::factory()
            ->count(3)
            ->create([
                'quantity' => 0,
                'product_category' => 'Electronics',
                'product_brand' => 'TechCorp',
            ]);
        Product::factory()
            ->count(2)
            ->create([
                'quantity' => 0,
                'product_category' => 'Home Appliances',
                'product_brand' => 'HomeEase',
            ]);

        $this->command->info('Created ' . Product::count() . ' products successfully!');
    }
}