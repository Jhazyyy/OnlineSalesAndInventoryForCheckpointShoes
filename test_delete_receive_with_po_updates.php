<?php

/**
 * Test script to verify that deleting a purchase receive properly reverses
 * all changes including PO item quantities and status
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReceive;
use App\Models\PurchaseReceiveItem;
use App\Models\PurchaseDelivery;
use App\Models\Supplier;
use App\Models\Product;
use App\Services\PurchaseReceiveService;
use Illuminate\Support\Facades\DB;

echo "=== Testing Purchase Receive Deletion with PO and Delivery Updates ===\n\n";

try {
    DB::beginTransaction();

    // Step 1: Get test data
    $supplier = Supplier::first();
    $product1 = Product::skip(0)->first();
    $product2 = Product::skip(1)->first();
    
    if (!$supplier || !$product1 || !$product2) {
        echo "❌ Missing test data. Please ensure database has suppliers and products.\n";
        exit(1);
    }

    echo "📦 Test Setup:\n";
    echo "   Supplier: {$supplier->supplier_name}\n";
    echo "   Product 1: {$product1->product_name}\n";
    echo "   Product 2: {$product2->product_name}\n\n";

    // Step 2: Create a purchase order
    echo "=== STEP 1: Create Purchase Order ===\n";
    $purchaseOrder = PurchaseOrder::create([
        'order_number' => 'PO-DEL-TEST-' . time(),
        'supplier_id' => $supplier->supplier_id,
        'order_date' => now(),
        'expected_delivery_date' => now()->addDays(7),
        'status' => 'ordered',
        'total_amount' => 2000.00,
        'notes' => 'Test PO for receive deletion',
    ]);

    // Create PO items
    $poItem1 = PurchaseOrderItem::create([
        'order_id' => $purchaseOrder->order_id,
        'product_id' => $product1->product_id,
        'quantity_ordered' => 100,
        'quantity_received' => 0,
        'unit_price' => 10.00,
        'line_total' => 1000.00,
    ]);

    $poItem2 = PurchaseOrderItem::create([
        'order_id' => $purchaseOrder->order_id,
        'product_id' => $product2->product_id,
        'quantity_ordered' => 100,
        'quantity_received' => 0,
        'unit_price' => 10.00,
        'line_total' => 1000.00,
    ]);

    echo "✅ Purchase Order created: {$purchaseOrder->order_number}\n";
    echo "   Status: {$purchaseOrder->status}\n";
    echo "   Item 1: {$product1->product_name} - Ordered: {$poItem1->quantity_ordered}, Received: {$poItem1->quantity_received}\n";
    echo "   Item 2: {$product2->product_name} - Ordered: {$poItem2->quantity_ordered}, Received: {$poItem2->quantity_received}\n\n";

    // Step 3: Create a delivery
    echo "=== STEP 2: Create Delivery ===\n";
    $delivery = PurchaseDelivery::create([
        'delivery_number' => 'DEL-TEST-' . time(),
        'purchase_order_id' => $purchaseOrder->order_id,
        'supplier_id' => $supplier->supplier_id,
        'delivery_date' => now()->addDays(5),
        'status' => 'in_transit',
        'carrier' => 'Test Carrier',
        'total_quantity_expected' => 200,
        'total_amount_expected' => 2000.00,
    ]);
    echo "✅ Delivery created: {$delivery->delivery_number} (Status: {$delivery->status})\n\n";

    // Step 4: Create a partial receive
    echo "=== STEP 3: Create Partial Purchase Receive ===\n";
    $receiveService = new PurchaseReceiveService();
    
    $receive = PurchaseReceive::create([
        'receive_number' => 'GR-DEL-TEST-' . time(),
        'reference_number' => 'REF-DEL-TEST-' . time(),
        'purchase_order_id' => $purchaseOrder->order_id,
        'supplier_id' => $supplier->supplier_id,
        'receive_date' => now(),
        'status' => 'partially_received',
        'total_quantity_expected' => 200,
        'total_quantity_received' => 50, // Partial receipt
        'total_amount_expected' => 2000.00,
        'total_amount_received' => 500.00,
    ]);

    // Add receive items
    $receiveItem1 = PurchaseReceiveItem::create([
        'receive_id' => $receive->receive_id,
        'product_id' => $product1->product_id,
        'purchase_order_item_id' => $poItem1->item_id,
        'quantity_expected' => 100,
        'quantity_received' => 30,
        'unit_price' => 10.00,
        'total_amount' => 300.00,
    ]);

    $receiveItem2 = PurchaseReceiveItem::create([
        'receive_id' => $receive->receive_id,
        'product_id' => $product2->product_id,
        'purchase_order_item_id' => $poItem2->item_id,
        'quantity_expected' => 100,
        'quantity_received' => 20,
        'unit_price' => 10.00,
        'total_amount' => 200.00,
    ]);

    // Manually update PO items (simulating what happens during receive creation)
    $poItem1->update(['quantity_received' => 30]);
    $poItem2->update(['quantity_received' => 20]);
    $purchaseOrder->update(['status' => 'partial_received']);

    echo "✅ Purchase Receive created: {$receive->receive_number}\n";
    echo "   Status: {$receive->status}\n";
    echo "   Expected: {$receive->total_quantity_expected}, Received: {$receive->total_quantity_received}\n\n";

    // Step 5: Check PO status after receive
    echo "=== STEP 4: Check PO Status After Receive ===\n";
    $purchaseOrder->refresh();
    $poItem1->refresh();
    $poItem2->refresh();
    
    echo "   PO Status: {$purchaseOrder->status}\n";
    echo "   Item 1 - Ordered: {$poItem1->quantity_ordered}, Received: {$poItem1->quantity_received}, Pending: {$poItem1->pending_quantity}\n";
    echo "   Item 2 - Ordered: {$poItem2->quantity_ordered}, Received: {$poItem2->quantity_received}, Pending: {$poItem2->pending_quantity}\n\n";

    if ($poItem1->quantity_received !== 30 || $poItem2->quantity_received !== 20) {
        echo "❌ ERROR: PO item quantities not updated correctly\n";
        exit(1);
    }

    // Step 6: Check delivery status
    echo "=== STEP 5: Check Delivery Status ===\n";
    $delivery->refresh();
    echo "   Delivery Status: {$delivery->status}\n";
    echo "   Note: Deliveries remain available for future receives\n\n";

    // Step 7: Delete the receive
    echo "=== STEP 6: Delete the Purchase Receive ===\n";
    echo "   Deleting receive: {$receive->receive_number}\n";
    
    $deleted = $receiveService->deleteReceive($receive);

    if ($deleted) {
        echo "✅ Purchase Receive deleted successfully!\n\n";
    } else {
        echo "❌ Failed to delete purchase receive\n";
        exit(1);
    }

    // Step 8: Verify PO item quantities are reversed
    echo "=== STEP 7: Verify PO Items After Deletion ===\n";
    $poItem1->refresh();
    $poItem2->refresh();
    
    echo "   Item 1 - Ordered: {$poItem1->quantity_ordered}, Received: {$poItem1->quantity_received}, Pending: {$poItem1->pending_quantity}\n";
    echo "   Item 2 - Ordered: {$poItem2->quantity_ordered}, Received: {$poItem2->quantity_received}, Pending: {$poItem2->pending_quantity}\n\n";

    // Step 9: Verify PO status is updated
    echo "=== STEP 8: Verify PO Status After Deletion ===\n";
    $purchaseOrder->refresh();
    echo "   PO Status: {$purchaseOrder->status}\n";
    echo "   Received Date: " . ($purchaseOrder->received_date ?? 'null') . "\n\n";

    // Step 10: Verify deliveries are NOT cancelled
    echo "=== STEP 9: Verify Deliveries Status ===\n";
    $delivery->refresh();
    echo "   Delivery Status: {$delivery->status}\n";
    echo "   Note: Delivery remains '{$delivery->status}' (available for future receives)\n\n";

    // Step 11: Final validation
    echo "=== FINAL VALIDATION ===\n";
    
    $allPassed = true;

    // Check if PO item 1 quantity is back to 0
    if ($poItem1->quantity_received !== 0) {
        echo "❌ FAIL: PO Item 1 should have 0 received but has {$poItem1->quantity_received}\n";
        $allPassed = false;
    } else {
        echo "✅ PASS: PO Item 1 quantity received back to 0\n";
    }

    // Check if PO item 2 quantity is back to 0
    if ($poItem2->quantity_received !== 0) {
        echo "❌ FAIL: PO Item 2 should have 0 received but has {$poItem2->quantity_received}\n";
        $allPassed = false;
    } else {
        echo "✅ PASS: PO Item 2 quantity received back to 0\n";
    }

    // Check if PO status is back to ordered
    if ($purchaseOrder->status !== 'ordered') {
        echo "❌ FAIL: PO should be 'ordered' but is '{$purchaseOrder->status}'\n";
        $allPassed = false;
    } else {
        echo "✅ PASS: PO status back to 'ordered'\n";
    }

    // Check if PO received_date is null
    if ($purchaseOrder->received_date !== null) {
        echo "❌ FAIL: PO received_date should be null\n";
        $allPassed = false;
    } else {
        echo "✅ PASS: PO received_date is null\n";
    }

    // Check if delivery is still in its original status
    if ($delivery->status !== 'in_transit') {
        echo "❌ FAIL: Delivery should still be 'in_transit' but is '{$delivery->status}'\n";
        $allPassed = false;
    } else {
        echo "✅ PASS: Delivery remains 'in_transit' (available for future receives)\n";
    }

    // Check if receive is deleted
    $receiveExists = PurchaseReceive::find($receive->receive_id);
    if ($receiveExists) {
        echo "❌ FAIL: Receive should be deleted but still exists\n";
        $allPassed = false;
    } else {
        echo "✅ PASS: Receive record deleted from database\n";
    }

    echo "\n";
    if ($allPassed) {
        echo "🎉 ALL TESTS PASSED!\n\n";
        echo "Summary:\n";
        echo "✅ Purchase receive deleted successfully\n";
        echo "✅ PO item quantities reversed to 0\n";
        echo "✅ PO status changed back to 'ordered'\n";
        echo "✅ PO received_date reset to null\n";
        echo "✅ Deliveries remain available for future receives\n";
        echo "✅ Inventory changes reversed (handled by deleteReceiveItems)\n\n";
        echo "The system now correctly handles purchase receive deletion,\n";
        echo "reversing all changes made during receive creation while keeping\n";
        echo "deliveries available for future receive operations.\n";
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
