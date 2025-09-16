<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a variety of customers for testing
        
        // 1. Create 20 regular customers (mix of individual and business)
        Customer::factory(20)->create();
        
        // 2. Create 10 individual customers from Philippines
        Customer::factory(10)
            ->individual()
            ->philippines()
            ->create();
        
        // 3. Create 8 business customers
        Customer::factory(8)
            ->business()
            ->create();
        
        // 4. Create 5 VIP customers
        Customer::factory(5)
            ->vip()
            ->create();
        
        // 5. Create 6 customers from Metro Manila
        Customer::factory(6)
            ->metroManila()
            ->create();
        
        // 6. Create 4 young adult customers
        Customer::factory(4)
            ->individual()
            ->youngAdult()
            ->withAvatar()
            ->create();
        
        // 7. Create 3 senior customers
        Customer::factory(3)
            ->individual()
            ->senior()
            ->create();
        
        // 8. Create 5 business customers with complete info
        Customer::factory(5)
            ->withBusinessInfo()
            ->philippines()
            ->create();
        
        // 9. Create 2 inactive customers
        Customer::factory(2)
            ->inactive()
            ->create();
        
        $this->command->info('Created ' . Customer::count() . ' customers successfully!');
    }
}
