<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

echo "Fixing admin user roles...\n";

$admin = User::where('email', 'admin@example.com')->first();
if ($admin) {
    // Remove all roles
    $admin->roles()->detach();
    
    // Assign only admin role
    $admin->assignRole('admin');
    
    echo "Admin user updated!\n";
    echo "Roles: " . $admin->getRoleNames()->implode(', ') . "\n";
} else {
    echo "Admin user not found!\n";
}
