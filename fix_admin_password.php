<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Fixing Admin Password ===\n\n";

$admin = App\Models\User::where('email', 'admin@example.com')->first();

if ($admin) {
    echo "Current password hash: " . $admin->password . "\n";
    
    // Check if password is plain text
    if ($admin->password === 'password' || !str_starts_with($admin->password, '$2y$')) {
        echo "❌ Password is NOT properly hashed\n";
        echo "Fixing password...\n\n";
        
        $admin->password = bcrypt('admin123');
        $admin->save();
        
        echo "✅ Admin password has been fixed!\n";
        echo "New hash: " . substr($admin->password, 0, 30) . "...\n\n";
        
        // Test the new password
        if (Hash::check('admin123', $admin->password)) {
            echo "✅ Password verification successful!\n";
            echo "You can now log in with:\n";
            echo "Email: admin@example.com\n";
            echo "Password: admin123\n";
        }
    } else {
        echo "✅ Password is already properly hashed\n";
        echo "Hash: " . substr($admin->password, 0, 30) . "...\n";
    }
} else {
    echo "❌ Admin user not found!\n";
}
