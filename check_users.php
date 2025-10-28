<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Users in Railway Database ===\n\n";

$users = App\Models\User::all();

echo "Total users: " . $users->count() . "\n\n";

foreach ($users as $user) {
    echo "Email: " . $user->email . "\n";
    echo "Name: " . $user->name . "\n";
    echo "Is Active: " . ($user->is_active ? 'Yes' : 'No') . "\n";
    echo "Status: " . ($user->status ?? 'N/A') . "\n";
    echo "Roles: " . $user->roles->pluck('name')->join(', ') . "\n";
    echo "Password Hash: " . substr($user->password, 0, 20) . "...\n";
    echo "---\n\n";
}

// Test login with admin credentials
echo "=== Testing Admin Login ===\n";
$adminEmail = 'admin@example.com';
$adminPassword = 'admin123';

$admin = App\Models\User::where('email', $adminEmail)->first();
if ($admin) {
    echo "Admin user found in database\n";
    echo "Email: " . $admin->email . "\n";
    echo "Is Active: " . ($admin->is_active ? 'Yes' : 'No') . "\n";
    
    if (Hash::check($adminPassword, $admin->password)) {
        echo "✅ Password 'admin123' is CORRECT\n";
    } else {
        echo "❌ Password 'admin123' does NOT match\n";
    }
} else {
    echo "❌ Admin user NOT found in database\n";
}

echo "\n=== Testing Test User Login ===\n";
$testEmail = 'test@example.com';
$testPassword = 'password';

$testUser = App\Models\User::where('email', $testEmail)->first();
if ($testUser) {
    echo "Test user found in database\n";
    echo "Email: " . $testUser->email . "\n";
    echo "Is Active: " . ($testUser->is_active ? 'Yes' : 'No') . "\n";
    
    if (Hash::check($testPassword, $testUser->password)) {
        echo "✅ Password 'password' is CORRECT\n";
    } else {
        echo "❌ Password 'password' does NOT match\n";
    }
} else {
    echo "❌ Test user NOT found in database\n";
}
