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
        // First, seed roles and permissions
        $this->call(RolesAndPermissionsSeeder::class);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@example.com',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
                'is_active' => true,
                'status' => 'active',
            ]
        );
        
        // Remove any existing roles and assign only admin role
        if (!$admin->hasRole('admin') || $admin->roles->count() > 1) {
            $admin->roles()->detach();
            $admin->assignRole('admin');
        }

        // Create test user with default "Test User" name (no first_name/last_name)
        // This will display as "Test User" via the accessor
        // Ensure idempotent seeding for the default test user
        $testUser = User::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'first_name' => 'Test',
                'last_name' => 'User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'role' => 'user',
                'is_active' => true,
                'status' => 'active',
            ]
        );
        
        // Ensure only user role is assigned
        if (!$testUser->hasRole('user') || $testUser->roles->count() > 1) {
            $testUser->roles()->detach();
            $testUser->assignRole('user');
        }


        
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

        // Initialize default system settings so Settings pages work out of the box
        $this->call(SystemSettingsSeeder::class);
    }
}
