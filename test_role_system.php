<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

echo "\n=== Role-Based Access Control Test ===\n\n";

// Test 1: Check roles
echo "1. Checking Roles:\n";
$roles = Role::all();
foreach ($roles as $role) {
    echo "   - {$role->name} ({$role->permissions->count()} permissions)\n";
}

// Test 2: Check users
echo "\n2. Checking Users:\n";
$users = User::all();
foreach ($users as $user) {
    $userRoles = $user->getRoleNames()->implode(', ');
    echo "   - {$user->email}: {$user->name} [Roles: {$userRoles}]\n";
}

// Test 3: Check admin user specifically
echo "\n3. Admin User Details:\n";
$admin = User::where('email', 'admin@example.com')->first();
if ($admin) {
    echo "   Email: {$admin->email}\n";
    echo "   Name: {$admin->name}\n";
    echo "   Roles: " . $admin->getRoleNames()->implode(', ') . "\n";
    echo "   Is Admin: " . ($admin->isAdmin() ? 'Yes' : 'No') . "\n";
    echo "   Has 'delete users' permission: " . ($admin->hasPermissionTo('delete users') ? 'Yes' : 'No') . "\n";
    echo "   Total permissions: " . $admin->getAllPermissions()->count() . "\n";
} else {
    echo "   Admin user not found!\n";
}

// Test 4: Check test user specifically
echo "\n4. Test User Details:\n";
$testUser = User::where('email', 'test@example.com')->first();
if ($testUser) {
    echo "   Email: {$testUser->email}\n";
    echo "   Name: {$testUser->name}\n";
    echo "   Roles: " . $testUser->getRoleNames()->implode(', ') . "\n";
    echo "   Is User: " . ($testUser->isUser() ? 'Yes' : 'No') . "\n";
    echo "   Has 'view products' permission: " . ($testUser->hasPermissionTo('view products') ? 'Yes' : 'No') . "\n";
    echo "   Has 'delete users' permission: " . ($testUser->hasPermissionTo('delete users') ? 'Yes' : 'No') . "\n";
    echo "   Total permissions: " . $testUser->getAllPermissions()->count() . "\n";
} else {
    echo "   Test user not found!\n";
}

echo "\n=== Test Complete ===\n\n";
echo "Login Credentials:\n";
echo "Admin: admin@example.com / admin123\n";
echo "User: test@example.com / password\n\n";
