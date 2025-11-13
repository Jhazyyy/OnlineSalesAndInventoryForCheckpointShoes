<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Notification;

echo "=== NOTIFICATION SYSTEM STATUS ===\n\n";

$total = Notification::count();
$unread = Notification::whereNull('read_at')->count();
$inventory = Notification::where('type', 'LIKE', 'inventory.%')->count();
$sales = Notification::where('type', 'LIKE', 'sales.%')->count();
$purchases = Notification::where('type', 'LIKE', 'purchases.%')->count();

echo "Total Notifications: {$total}\n";
echo "Unread: {$unread}\n";
echo "Inventory: {$inventory}\n";
echo "Sales: {$sales}\n";
echo "Purchases: {$purchases}\n";

echo "\nRecent Notifications:\n";
$recent = Notification::latest()->take(5)->get();
foreach ($recent as $n) {
    $status = $n->isUnread() ? '[UNREAD]' : '[READ]';
    $time = $n->created_at->diffForHumans();
    echo "  {$status} {$n->level}: {$n->title} ({$time})\n";
}

echo "\n=== SYSTEM READY ===\n";
echo "Visit /notifications-list to view all notifications\n";
