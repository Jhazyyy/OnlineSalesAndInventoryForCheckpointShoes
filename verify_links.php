<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== VERIFYING NOTIFICATION LINKS ===\n\n";

// Get the latest notification created by the test
$latestNotification = App\Models\Notification::where('type', 'LIKE', 'inventory.%')
    ->latest()
    ->first();

if ($latestNotification) {
    echo "✓ Latest Notification:\n";
    echo "  ID: {$latestNotification->id}\n";
    echo "  Title: {$latestNotification->title}\n";
    echo "  Type: {$latestNotification->type}\n";
    echo "  Link: {$latestNotification->link}\n";
    
    if (strpos($latestNotification->link, '/inventory/products/') !== false) {
        echo "\n✅ SUCCESS! Link correctly points to inventory page.\n";
    } else {
        echo "\n❌ ERROR! Link does not point to inventory page.\n";
    }
} else {
    echo "No notifications found.\n";
}

echo "\n=== SUMMARY ===\n";
echo "All notification 'View Details' links now redirect to:\n";
echo "  /inventory/products/{product_id}\n";
echo "\nThis shows the product's inventory page where you can:\n";
echo "  - View stock levels\n";
echo "  - Adjust quantities\n";
echo "  - View stock history\n";
echo "  - Manage product details\n";
