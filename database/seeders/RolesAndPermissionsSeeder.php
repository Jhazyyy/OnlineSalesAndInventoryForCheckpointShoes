<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates admin and user roles with appropriate permissions
     * Supports multi-user simultaneous access through Spatie Permission package
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions for various modules
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Product management
            'view products',
            'create products',
            'edit products',
            'delete products',
            
            // Inventory management
            'view inventory',
            'create inventory',
            'edit inventory',
            'delete inventory',
            'manage stock',
            
            // Sales management
            'view sales',
            'create sales',
            'edit sales',
            'delete sales',
            'process refunds',
            
            // Customer management
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            
            // Supplier management
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            
            // Purchase management
            'view purchases',
            'create purchases',
            'edit purchases',
            'delete purchases',
            
            // Reports
            'view reports',
            'export reports',
            
            // Settings
            'view settings',
            'manage settings',
            
            // Categories & Brands
            'view categories',
            'manage categories',
            'view brands',
            'manage brands',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Admin role and assign all permissions
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions(Permission::all());

        // Create User role with limited permissions
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([
            'view products',
            'view inventory',
            'view sales',
            'create sales',
            'view customers',
            'create customers',
            'view suppliers',
            'view reports',
            'view categories',
            'view brands',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Admin role has all permissions.');
        $this->command->info('User role has limited view and create permissions.');
    }
}
