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
     * Creates super admin, admin, and user roles with appropriate permissions
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
            'manage admins', // Super Admin only
            
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

        // Create Super Admin role with all permissions (highest level)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdminRole->syncPermissions(Permission::all());

        // Create Admin role with all permissions except managing other admins
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([
            'view users',
            'create users',
            'edit users',
            'delete users',
            'view products',
            'create products',
            'edit products',
            'delete products',
            'view inventory',
            'create inventory',
            'edit inventory',
            'delete inventory',
            'manage stock',
            'view sales',
            'create sales',
            'edit sales',
            'delete sales',
            'process refunds',
            'view customers',
            'create customers',
            'edit customers',
            'delete customers',
            'view suppliers',
            'create suppliers',
            'edit suppliers',
            'delete suppliers',
            'view purchases',
            'create purchases',
            'edit purchases',
            'delete purchases',
            'view reports',
            'export reports',
            'view settings',
            'manage settings',
            'view categories',
            'manage categories',
            'view brands',
            'manage brands',
        ]);

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
        $this->command->info('Super Admin role has all permissions (including managing admins).');
        $this->command->info('Admin role has all permissions except managing other admins.');
        $this->command->info('User role has limited view and create permissions.');
    }
}
