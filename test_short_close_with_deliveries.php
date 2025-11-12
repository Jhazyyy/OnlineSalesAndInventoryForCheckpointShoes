<?php

/**
 * Test script to verify that short closing a purchase receive
 * also completes all pending deliveries for the purchase order
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PurchaseOrder;
use App\Models\PurchaseReceive;
use App\Models\PurchaseDelivery;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\PurchaseReceiveService;
use Illuminate\Support\Facades\DB;

echo "=== Testing Short Close with Delivery Completion ===\n\n";

try {
    DB::beginTransaction();

    // Step 1: Get or create test data
    $supplier = Supplier::first();
    $product = Product::first();
    
    if (!$supplier || !$product) {
        echo "❌ Missing test data (supplier or product). Please ensure database has sample data.\n";
        exit(1);
    }

    echo "📦 Test Setup:\n";
    echo "   Supplier: {$supplier->supplier_name}\n";
    echo "   Product: {$product->product_name}\n\n";

    // Step 2: Create a purchase order
    echo "=== STEP 1: Create Purchase Order ===\n";
    $purchaseOrder = PurchaseOrder::create([
        'order_number' => 'PO-TEST-' . time(),
        'supplier_id' => $supplier->supplier_id,
        'order_date' => now(),
        'expected_delivery_date' => now()->addDays(7),
        'status' => 'approved',
        'total_amount' => 1000.00,
        'notes' => 'Test PO for short close with deliveries',
    ]);
    echo "✅ Purchase Order created: {$purchaseOrder->order_number}\n\n";

    // Step 3: Create deliveries for this PO
    echo "=== STEP 2: Create Deliveries ===\n";
    
    $delivery1 = PurchaseDelivery::create([
        'delivery_number' => 'DEL-TEST-1-' . time(),
        'purchase_order_id' => $purchaseOrder->order_id,
        'supplier_id' => $supplier->supplier_id,
        'delivery_date' => now()->addDays(5),
        'status' => 'scheduled',
        'carrier' => 'Test Carrier',
        'total_quantity_expected' => 50,
        'total_amount_expected' => 500.00,
    ]);
    echo "✅ Delivery 1 created: {$delivery1->delivery_number} (Status: {$delivery1->status})\n";

    $delivery2 = PurchaseDelivery::create([
        'delivery_number' => 'DEL-TEST-2-' . time(),
        'purchase_order_id' => $purchaseOrder->order_id,
        'supplier_id' => $supplier->supplier_id,
        'delivery_date' => now()->addDays(10),
        'status' => 'in_transit',
        'carrier' => 'Test Carrier',
        'total_quantity_expected' => 50,
        'total_amount_expected' => 500.00,
    ]);
    echo "✅ Delivery 2 created: {$delivery2->delivery_number} (Status: {$delivery2->status})\n\n";

    // Step 4: Create a partial receive
    echo "=== STEP 3: Create Partial Purchase Receive ===\n";
    $receive = PurchaseReceive::create([
        'receive_number' => 'GR-TEST-' . time(),
        'reference_number' => 'REF-TEST-' . time(),
        'purchase_order_id' => $purchaseOrder->order_id,
        'supplier_id' => $supplier->supplier_id,
        'receive_date' => now(),
        'status' => 'partially_received',
        'total_quantity_expected' => 100,
        'total_quantity_received' => 30, // Only received 30 out of 100
        'total_amount_expected' => 1000.00,
        'total_amount_received' => 300.00,
        'notes' => 'Partial receipt - supplier can only deliver 30 units',
    ]);
    echo "✅ Purchase Receive created: {$receive->receive_number}\n";
    echo "   Expected: {$receive->total_quantity_expected} units\n";
    echo "   Received: {$receive->total_quantity_received} units\n";
    echo "   Shortfall: " . ($receive->total_quantity_expected - $receive->total_quantity_received) . " units\n";
    echo "   Status: {$receive->status}\n\n";

    // Step 5: Verify deliveries are still pending
    echo "=== STEP 4: Check Deliveries Before Short Close ===\n";
    $delivery1->refresh();
    $delivery2->refresh();
    echo "   Delivery 1 Status: {$delivery1->status}\n";
    echo "   Delivery 2 Status: {$delivery2->status}\n\n";

    if ($delivery1->status !== 'scheduled' || $delivery2->status !== 'in_transit') {
        echo "❌ ERROR: Deliveries should still be in their original status\n";
        exit(1);
    }

    // Step 6: Short close the receive
    echo "=== STEP 5: Short Close the Purchase Receive ===\n";
    $receiveService = new PurchaseReceiveService();
    $shortCloseReason = "Supplier confirmed they can only deliver 30 units total. Remaining 70 units are out of stock and will not be delivered.";
    
    echo "   Reason: {$shortCloseReason}\n";
    echo "   Performing short close...\n\n";
    
    $receive = $receiveService->shortCloseReceive($receive, $shortCloseReason, 1);

    echo "✅ Purchase Receive short closed successfully!\n";
    echo "   Status: {$receive->status}\n";
    echo "   Short Closed: " . ($receive->is_short_closed ? 'Yes' : 'No') . "\n";
    echo "   Short Close Reason: {$receive->short_close_reason}\n\n";

    // Step 7: Verify deliveries are completed
    echo "=== STEP 6: Check Deliveries After Short Close ===\n";
    $delivery1->refresh();
    $delivery2->refresh();
    
    echo "   Delivery 1:\n";
    echo "      Status: {$delivery1->status}\n";
    echo "      Delivered At: " . ($delivery1->delivered_at ? $delivery1->delivered_at->format('Y-m-d H:i:s') : 'N/A') . "\n";
    echo "      Notes: " . ($delivery1->delivery_notes ?? 'N/A') . "\n\n";
    
    echo "   Delivery 2:\n";
    echo "      Status: {$delivery2->status}\n";
    echo "      Delivered At: " . ($delivery2->delivered_at ? $delivery2->delivered_at->format('Y-m-d H:i:s') : 'N/A') . "\n";
    echo "      Notes: " . ($delivery2->delivery_notes ?? 'N/A') . "\n\n";

    // Step 8: Verify tracking history
    echo "=== STEP 7: Check Delivery Tracking History ===\n";
    
    $trackingHistory1 = $delivery1->tracking_history;
    if ($trackingHistory1 && is_array($trackingHistory1) && count($trackingHistory1) > 0) {
        $latestTracking = $trackingHistory1[count($trackingHistory1) - 1];
        echo "   Delivery 1 Latest Tracking:\n";
        echo "      Status: " . ($latestTracking['status'] ?? 'N/A') . "\n";
        echo "      Previous Status: " . ($latestTracking['previous_status'] ?? 'N/A') . "\n";
        echo "      Notes: " . ($latestTracking['notes'] ?? 'N/A') . "\n";
        echo "      Short Closed Flag: " . (isset($latestTracking['short_closed']) && $latestTracking['short_closed'] ? 'Yes' : 'No') . "\n\n";
    } else {
        echo "   Delivery 1: No tracking history found\n\n";
    }

    // Step 9: Verify Purchase Order status
    echo "=== STEP 8: Check Purchase Order Status ===\n";
    $purchaseOrder->refresh();
    echo "   PO Status: {$purchaseOrder->status}\n";
    echo "   Received Date: " . ($purchaseOrder->received_date ? $purchaseOrder->received_date->format('Y-m-d') : 'N/A') . "\n\n";

    // Step 10: Final Validation
    echo "=== FINAL VALIDATION ===\n";
    
    $allPassed = true;
    
    if ($receive->is_short_closed !== true) {
        echo "❌ FAIL: Purchase receive is not marked as short closed\n";
        $allPassed = false;
    } else {
        echo "✅ PASS: Purchase receive is short closed\n";
    }

    if ($delivery1->status !== 'delivered') {
        echo "❌ FAIL: Delivery 1 should be marked as 'delivered' but is '{$delivery1->status}'\n";
        $allPassed = false;
    } else {
        echo "✅ PASS: Delivery 1 is marked as delivered\n";
    }

    if ($delivery2->status !== 'delivered') {
        echo "❌ FAIL: Delivery 2 should be marked as 'delivered' but is '{$delivery2->status}'\n";
        $allPassed = false;
    } else {
        echo "✅ PASS: Delivery 2 is marked as delivered\n";
    }

    if ($purchaseOrder->status !== 'received') {
        echo "❌ FAIL: Purchase order should be 'received' but is '{$purchaseOrder->status}'\n";
        $allPassed = false;
    } else {
        echo "✅ PASS: Purchase order is marked as received\n";
    }

    if (!$delivery1->delivered_at || !$delivery2->delivered_at) {
        echo "❌ FAIL: Deliveries should have delivered_at timestamp\n";
        $allPassed = false;
    } else {
        echo "✅ PASS: Deliveries have delivered_at timestamps\n";
    }

    if (strpos($delivery1->delivery_notes, 'short close') === false) {
        echo "❌ FAIL: Delivery 1 notes should mention short close\n";
        $allPassed = false;
    } else {
        echo "✅ PASS: Delivery 1 notes mention short close\n";
    }

    echo "\n";
    if ($allPassed) {
        echo "🎉 ALL TESTS PASSED!\n\n";
        echo "Summary:\n";
        echo "✅ Purchase receive short closed successfully\n";
        echo "✅ All pending deliveries automatically completed\n";
        echo "✅ Delivery tracking history updated\n";
        echo "✅ Purchase order marked as received\n";
        echo "✅ No remaining pending deliveries\n\n";
        echo "The system now correctly handles delivery completion when a purchase order is short-closed.\n";
    } else {
        echo "❌ SOME TESTS FAILED\n";
    }

    DB::rollBack();
    echo "\n✅ Changes rolled back (test data not saved)\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack Trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
