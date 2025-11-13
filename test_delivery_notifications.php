<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PurchaseDelivery;
use App\Models\Shipment;
use App\Models\Notification;
use App\Services\PurchaseDeliveryService;
use App\Services\ShipmentService;

echo "============================================\n";
echo "DELIVERY NOTIFICATION TEST\n";
echo "============================================\n\n";

// Clean up old test notifications
echo "Cleaning up old delivery notifications...\n";
Notification::where('type', 'LIKE', 'delivery.%')->delete();
Notification::where('type', 'LIKE', 'shipment.%')->delete();
echo "✓ Cleanup complete\n\n";

// Test 1: Purchase Delivery Notifications
echo "=== TEST 1: Purchase Delivery Notifications ===\n";

$purchaseDelivery = PurchaseDelivery::first();

if ($purchaseDelivery) {
    echo "Testing with delivery: {$purchaseDelivery->delivery_number}\n";
    echo "Current status: {$purchaseDelivery->status}\n\n";
    
    $deliveryService = new PurchaseDeliveryService();
    
    // Test different status changes
    $statuses = [
        'scheduled' => 'Scheduled',
        'in_transit' => 'In Transit',
        'out_for_delivery' => 'Out for Delivery',
        'delivered' => 'Delivered',
    ];
    
    foreach ($statuses as $status => $label) {
        try {
            echo "Changing status to: {$label}...\n";
            $deliveryService->changeStatus($purchaseDelivery, $status, "Test notification for {$label}");
            echo "✓ Status changed and notification created\n\n";
            sleep(1); // Small delay for different timestamps
        } catch (\Exception $e) {
            echo "✗ Error: {$e->getMessage()}\n\n";
        }
    }
} else {
    echo "No purchase deliveries found. Skipping purchase delivery test.\n\n";
}

// Test 2: Shipment Notifications (Sales)
echo "=== TEST 2: Shipment Notifications (Sales) ===\n";

$shipment = Shipment::first();

if ($shipment) {
    echo "Testing with shipment: {$shipment->shipment_number}\n";
    echo "Current status: {$shipment->status}\n\n";
    
    $shipmentService = new ShipmentService();
    
    // Test different shipment status changes
    $shipmentStatuses = [
        'preparing' => 'Preparing',
        'shipped' => 'Shipped',
        'in_transit' => 'In Transit',
        'out_for_delivery' => 'Out for Delivery',
        'delivered' => 'Delivered',
    ];
    
    foreach ($shipmentStatuses as $status => $label) {
        try {
            echo "Changing status to: {$label}...\n";
            $shipmentService->changeShipmentStatus($shipment, $status);
            echo "✓ Status changed and notification created\n\n";
            sleep(1); // Small delay for different timestamps
        } catch (\Exception $e) {
            echo "✗ Error: {$e->getMessage()}\n\n";
        }
    }
} else {
    echo "No shipments found. Skipping shipment test.\n\n";
}

// Display summary
echo "============================================\n";
echo "SUMMARY\n";
echo "============================================\n";

$totalNotifications = Notification::count();
$deliveryNotifications = Notification::where('type', 'LIKE', 'delivery.%')->count();
$shipmentNotifications = Notification::where('type', 'LIKE', 'shipment.%')->count();
$unreadNotifications = Notification::whereNull('read_at')->count();

echo "Total Notifications: {$totalNotifications}\n";
echo "  - Delivery Notifications: {$deliveryNotifications}\n";
echo "  - Shipment Notifications: {$shipmentNotifications}\n";
echo "  - Unread: {$unreadNotifications}\n\n";

echo "Recent delivery/shipment notifications:\n";
$recentNotifications = Notification::where(function($q) {
    $q->where('type', 'LIKE', 'delivery.%')
      ->orWhere('type', 'LIKE', 'shipment.%');
})->latest()->take(10)->get();

foreach ($recentNotifications as $notif) {
    $status = $notif->isUnread() ? '[UNREAD]' : '[READ]';
    $time = $notif->created_at->diffForHumans();
    echo "  {$status} {$notif->level}: {$notif->title} ({$time})\n";
}

echo "\n============================================\n";
echo "TEST COMPLETED\n";
echo "============================================\n";
echo "Visit /notifications-list to view all notifications\n";
echo "The notification icon should show the count: {$unreadNotifications}\n";
