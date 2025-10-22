<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

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
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
        ]);

        // $adminRole = Role::create(['name' => 'admin']);
        // $userRole = Role::create(['name' => 'user']);

        // $admin = User::create([
        // 'name' => 'Admin User',
        // 'email' => 'admin@example.com',
        // 'password' => bcrypt('password'),
        // ]);
        // $admin->assignRole($adminRole);



        
        // Seed suppliers
        // $this->call(SupplierSeeder::class);
        
        // // Seed customers
        // $this->call(CustomerSeeder::class);

        // Seed products
        // $this->call(ProductSeeder::class);

        // Seed users
        // $this->call(UserSeeder::class);

        // $this->call(CategorySeeder::class);

        // Seed example notifications
        // $this->call(NotificationSeeder::class);
    }
}
