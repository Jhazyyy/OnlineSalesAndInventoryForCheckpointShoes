<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Notification;
use App\Services\InventoryThresholdService;

echo "============================================\n";
echo "NOTIFICATION SYSTEM TEST\n";
echo "============================================\n\n";

// Clean up old test data
echo "Cleaning up old test data...\n";
Notification::where('type', 'LIKE', 'inventory.%')->delete();

// Get or create a test product
$product = Product::first();

if (!$product) {
    echo "No products found. Please create a product first.\n";
    exit(1);
}

echo "Using product: {$product->product_name}\n";
echo "Current quantity: {$product->quantity}\n\n";

// Set thresholds for the product
echo "Setting thresholds...\n";
$product->enableThresholdFields();
$product->update([
    'reorder_level' => 50,
    'critical_level' => 20,
    'ceiling_level' => 500,
    'threshold_alerts_enabled' => true,
    'auto_reorder_enabled' => false,
]);
$product->resetFillable();

echo "Thresholds set:\n";
echo "  - Reorder Level: {$product->reorder_level}\n";
echo "  - Critical Level: {$product->critical_level}\n";
echo "  - Ceiling Level: {$product->ceiling_level}\n\n";

// Test scenario 1: Low stock alert
echo "TEST 1: Creating Low Stock Alert\n";
echo "Setting quantity to 40 (below reorder level of 50)...\n";
$product->quantity = 40;
$product->save();

$thresholdService = new InventoryThresholdService();
$alerts = $thresholdService->checkProductThresholds($product);
echo "Alerts created: " . $alerts->count() . "\n\n";

// Test scenario 2: Critical stock alert
echo "TEST 2: Creating Critical Stock Alert\n";
echo "Setting quantity to 15 (below critical level of 20)...\n";
$product->quantity = 15;
$product->save();

$alerts = $thresholdService->checkProductThresholds($product);
echo "Alerts created: " . $alerts->count() . "\n\n";

// Test scenario 3: Out of stock alert
echo "TEST 3: Creating Out of Stock Alert\n";
echo "Setting quantity to 0...\n";
$product->quantity = 0;
$product->save();

$alerts = $thresholdService->checkProductThresholds($product);
echo "Alerts created: " . $alerts->count() . "\n\n";

// Test scenario 4: Manual notification creation
echo "TEST 4: Creating Manual Notifications\n";
$notificationTypes = [
    [
        'title' => 'Critical Stock Level',
        'message' => "Product '{$product->product_name}' has reached critical stock level (0 units). Immediate reorder required!",
        'level' => 'danger',
        'type' => 'inventory.critical_stock',
    ],
    [
        'title' => 'Reorder Suggestion',
        'message' => "Product '{$product->product_name}' needs reordering. Suggested order quantity: 100 units.",
        'level' => 'warning',
        'type' => 'inventory.reorder_needed',
    ],
    [
        'title' => 'Purchase Order Created',
        'message' => 'Purchase Order PO-2025-001 has been created successfully',
        'level' => 'info',
        'type' => 'purchases.order_created',
        'link' => '/purchases/orders',
    ],
    [
        'title' => 'Sales Order Confirmed',
        'message' => 'Order SO-2025-001 has been confirmed successfully',
        'level' => 'success',
        'type' => 'sales.order_status_changed',
        'link' => '/sales/orders',
    ],
];

foreach ($notificationTypes as $notifData) {
    Notification::create($notifData);
    echo "Created: {$notifData['title']}\n";
}

echo "\n";

// Display summary
echo "============================================\n";
echo "SUMMARY\n";
echo "============================================\n";

$totalNotifications = Notification::count();
$inventoryNotifications = Notification::where('type', 'LIKE', 'inventory.%')->count();
$purchaseNotifications = Notification::where('type', 'LIKE', 'purchases.%')->count();
$salesNotifications = Notification::where('type', 'LIKE', 'sales.%')->count();
$unreadNotifications = Notification::whereNull('read_at')->count();

echo "Total Notifications: {$totalNotifications}\n";
echo "  - Inventory: {$inventoryNotifications}\n";
echo "  - Purchases: {$purchaseNotifications}\n";
echo "  - Sales: {$salesNotifications}\n";
echo "  - Unread: {$unreadNotifications}\n\n";

echo "Recent notifications:\n";
$recentNotifications = Notification::latest()->take(5)->get();
foreach ($recentNotifications as $notif) {
    $status = $notif->isUnread() ? '[UNREAD]' : '[READ]';
    echo "  {$status} {$notif->level}: {$notif->title}\n";
}

echo "\n============================================\n";
echo "TEST COMPLETED\n";
echo "============================================\n";
echo "Visit /notifications-list to view all notifications\n";
