<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::insert([
            ['category_code' => 'SHOE', 'name' => 'Shoes', 'description' => 'All types of shoes', 'is_active' => true],
            ['category_code' => 'GAR', 'name' => 'Garments', 'description' => 'Clothing and apparel', 'is_active' => true],
            ['category_code' => 'ACC', 'name' => 'Accessories', 'description' => 'Bags, belts, etc.', 'is_active' => true],
        ]);
    }
}
