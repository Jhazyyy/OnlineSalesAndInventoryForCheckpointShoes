<?php

/**
 * Test script to verify pending quantities calculation
 * Run this to check if the data is being calculated correctly
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PurchaseOrderItem;
use App\Models\PurchaseDeliveryItem;
use App\Models\PurchaseReceiveItem;

echo "Testing Pending Quantities Calculation\n";
echo "========================================\n\n";

try {
    // 1. From Purchase Orders - pending items (ordered but not received)
    echo "1. Checking Purchase Orders...\n";
    $pendingFromPurchaseOrders = PurchaseOrderItem::whereHas('order', function($query) {
        $query->whereIn('status', ['ordered', 'partial_received', 'approved']);
    })
    ->selectRaw('SUM(quantity_ordered - quantity_received) as pending_qty')
    ->value('pending_qty') ?? 0;
    
    echo "   - Orders with status (ordered, partial_received, approved): " . 
         PurchaseOrderItem::whereHas('order', function($query) {
             $query->whereIn('status', ['ordered', 'partial_received', 'approved']);
         })->count() . " items\n";
    echo "   - Pending quantity: " . number_format($pendingFromPurchaseOrders) . "\n\n";
    
    // 2. From Purchase Deliveries - items in transit or scheduled
    echo "2. Checking Purchase Deliveries...\n";
    $pendingFromDeliveries = PurchaseDeliveryItem::whereHas('delivery', function($query) {
        $query->whereIn('status', ['scheduled', 'in_transit', 'picked_up']);
    })
    ->selectRaw('SUM(quantity_expected - quantity_delivered) as pending_qty')
    ->value('pending_qty') ?? 0;
    
    echo "   - Deliveries with status (scheduled, in_transit, picked_up): " . 
         PurchaseDeliveryItem::whereHas('delivery', function($query) {
             $query->whereIn('status', ['scheduled', 'in_transit', 'picked_up']);
         })->count() . " items\n";
    echo "   - Pending quantity: " . number_format($pendingFromDeliveries) . "\n\n";
    
    // 3. From Purchase Receives - items expected but not yet received
    echo "3. Checking Purchase Receives (Goods Receipts)...\n";
    $pendingFromReceives = PurchaseReceiveItem::whereHas('purchaseReceive', function($query) {
        $query->whereIn('status', ['in_transit', 'pending']);
    })
    ->selectRaw('SUM(quantity_expected - quantity_received) as pending_qty')
    ->value('pending_qty') ?? 0;
    
    echo "   - Receives with status (in_transit, pending): " . 
         PurchaseReceiveItem::whereHas('purchaseReceive', function($query) {
             $query->whereIn('status', ['in_transit', 'pending']);
         })->count() . " items\n";
    echo "   - Pending quantity: " . number_format($pendingFromReceives) . "\n\n";
    
    // Total
    $totalPending = max(0, $pendingFromPurchaseOrders + $pendingFromDeliveries + $pendingFromReceives);
    
    echo "========================================\n";
    echo "TOTAL QUANTITY TO BE RECEIVED: " . number_format($totalPending) . "\n";
    echo "========================================\n\n";
    
    echo "Breakdown:\n";
    echo "  - From Purchase Orders:    " . number_format($pendingFromPurchaseOrders) . "\n";
    echo "  - From Deliveries:         " . number_format($pendingFromDeliveries) . "\n";
    echo "  - From Goods Receipts:     " . number_format($pendingFromReceives) . "\n";
    
    echo "\n✓ Test completed successfully!\n";
    echo "\nThis data should now be visible on the dashboard.\n";
    
} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
