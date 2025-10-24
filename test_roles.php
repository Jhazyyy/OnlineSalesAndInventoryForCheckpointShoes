<?php

/**
 * Quick Test Script for Role-Based Access Control
 * 
 * Run this with: php artisan tinker
 * Then paste these commands one by one
 */

// Test 1: Check if roles exist
echo "=== Test 1: Checking Roles ===\n";
$roles = Spatie\Permission\Models\Role::all()->pluck('name');
echo "Available roles: " . $roles->implode(', ') . "\n\n";

// Test 2: Check if permissions exist  
echo "=== Test 2: Checking Permissions ===\n";
$permissions = Spatie\Permission\Models\Permission::all()->pluck('name');
echo "Total permissions: " . $permissions->count() . "\n";
echo "Sample permissions: " . $permissions->take(5)->implode(', ') . "\n\n";

// Test 3: Check admin user
echo "=== Test 3: Checking Admin User ===\n";
$admin = App\Models\User::where('email', 'admin@example.com')->first();
if ($admin) {
    echo "Admin found: " . $admin->name . "\n";
    echo "Admin roles: " . $admin->getRoleNames()->implode(', ') . "\n";
    echo "Is Admin: " . ($admin->isAdmin() ? 'Yes' : 'No') . "\n";
    echo "Has 'delete users' permission: " . ($admin->hasPermissionTo('delete users') ? 'Yes' : 'No') . "\n";
} else {
    echo "Admin user not found!\n";
}
echo "\n";

// Test 4: Check test user
echo "=== Test 4: Checking Test User ===\n";
$user = App\Models\User::where('email', 'test@example.com')->first();
if ($user) {
    echo "User found: " . $user->name . "\n";
    echo "User roles: " . $user->getRoleNames()->implode(', ') . "\n";
    echo "Is Admin: " . ($user->isAdmin() ? 'Yes' : 'No') . "\n";
    echo "Is User: " . ($user->isUser() ? 'Yes' : 'No') . "\n";
    echo "Has 'view products' permission: " . ($user->hasPermissionTo('view products') ? 'Yes' : 'No') . "\n";
    echo "Has 'delete users' permission: " . ($user->hasPermissionTo('delete users') ? 'Yes' : 'No') . "\n";
} else {
    echo "Test user not found!\n";
}
echo "\n";

// Test 5: Create a new user and check auto-role assignment
echo "=== Test 5: Testing Auto-Role Assignment ===\n";
$newUser = App\Models\User::create([
    'name' => 'New User',
    'first_name' => 'New',
    'last_name' => 'User',
    'email' => 'newuser@example.com',
    'password' => bcrypt('password'),
]);
echo "New user created: " . $newUser->name . "\n";
echo "Assigned roles: " . $newUser->getRoleNames()->implode(', ') . "\n";
echo "Expected: user role should be auto-assigned\n\n";

// Cleanup
$newUser->delete();
echo "Test user deleted (cleanup)\n";

echo "\n=== All Tests Complete ===\n";
