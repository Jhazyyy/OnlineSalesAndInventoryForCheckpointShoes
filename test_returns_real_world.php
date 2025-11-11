<?php

/**
 * Real-world scenario test for sales returns
 * Simulates actual user workflow from creating a return to final processing
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Returns;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockMovement;
use App\Models\Customer;
use App\Models\SalesOrder;
use Illuminate\Support\Facades\DB;

echo "=== Real-World Sales Return Scenario Test ===\n\n";
echo "Scenario: Customer returns a defective product\n\n";

try {
    DB::beginTransaction();

    // Setup: Get real data
    $product = Product::first();
    $customer = Customer::first();
    
    if (!$product || !$customer) {
        echo "❌ Missing test data. Need at least 1 product and 1 customer.\n";
        exit(1);
    }

    echo "📦 Product: {$product->product_name}\n";
    echo "👤 Customer: {$customer->display_name}\n\n";

    // Record initial state
    $initialProductQty = $product->quantity;
    $inventory = Inventory::where('product_id', $product->product_id)->first();
    $initialInventoryQty = $inventory ? $inventory->quantity_on_hand : 0;

    echo "=== INITIAL STATE ===\n";
    echo "Product Quantity: {$initialProductQty}\n";
    echo "Inventory Quantity: {$initialInventoryQty}\n\n";

    // STEP 1: Customer initiates return
    echo "=== STEP 1: Customer Returns Product ===\n";
    echo "Customer calls to report defective product...\n";
    
    $return = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'quantity' => 2,
        'price' => 250.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Product defective - customer reported sole separation',
    ]);

    echo "✅ Return created: #{$return->return_id}\n";
    echo "   Status: {$return->return_status}\n";
    echo "   Quantity: {$return->quantity}\n";
    echo "   Reason: {$return->reason}\n\n";

    // Verify inventory hasn't changed yet
    $product->refresh();
    if ($product->quantity == $initialProductQty) {
        echo "✅ Inventory correctly unchanged while pending\n\n";
    } else {
        echo "❌ ERROR: Inventory changed while still pending!\n\n";
    }

    // STEP 2: Manager reviews and approves
    echo "=== STEP 2: Manager Reviews Return ===\n";
    echo "Manager inspects returned items and confirms defect...\n";
    
    if ($return->isPending()) {
        echo "✅ Return is in pending status, ready for approval\n";
        
        echo "Manager approves the return...\n";
        $approved = $return->approve();
        
        if ($approved) {
            echo "✅ Return approved successfully!\n\n";
        } else {
            echo "❌ Failed to approve return!\n\n";
        }
    }

    // STEP 3: Verify inventory updated
    echo "=== STEP 3: Verify Inventory Update ===\n";
    $product->refresh();
    $inventory = $inventory->fresh();
    
    $expectedProductQty = $initialProductQty + 2;
    $expectedInventoryQty = $initialInventoryQty + 2;
    
    $productUpdated = ($product->quantity == $expectedProductQty);
    $inventoryUpdated = ($inventory->quantity_on_hand == $expectedInventoryQty);
    
    echo "Product Quantity: {$product->quantity} (Expected: {$expectedProductQty}) " . 
         ($productUpdated ? "✅" : "❌") . "\n";
    echo "Inventory Quantity: {$inventory->quantity_on_hand} (Expected: {$expectedInventoryQty}) " .
         ($inventoryUpdated ? "✅" : "❌") . "\n\n";

    // STEP 4: Check stock movement audit trail
    echo "=== STEP 4: Audit Trail Verification ===\n";
    $movement = StockMovement::where('reference_type', 'sales_return')
        ->where('reference_id', $return->return_id)
        ->first();
    
    if ($movement) {
        echo "✅ Stock movement recorded:\n";
        echo "   Movement ID: #{$movement->movement_id}\n";
        echo "   Type: {$movement->movement_type}\n";
        echo "   Quantity Change: +{$movement->quantity_change}\n";
        echo "   Before: {$movement->quantity_before}\n";
        echo "   After: {$movement->quantity_after}\n";
        echo "   Unit Cost: ₱" . number_format($movement->unit_cost, 2) . "\n";
        echo "   Date: {$movement->movement_date->format('Y-m-d H:i:s')}\n\n";
    } else {
        echo "❌ No stock movement record found!\n\n";
    }

    // STEP 5: Process the return (mark as complete)
    echo "=== STEP 5: Process Return ===\n";
    echo "Warehouse receives and processes the returned items...\n";
    
    $qtyBeforeProcessing = $product->quantity;
    $processed = $return->markAsProcessed();
    
    if ($processed) {
        echo "✅ Return marked as processed\n";
        echo "   Status: {$return->return_status}\n";
        
        $product->refresh();
        if ($product->quantity == $qtyBeforeProcessing) {
            echo "✅ Inventory correctly unchanged during processing\n\n";
        } else {
            echo "❌ ERROR: Inventory changed during processing!\n\n";
        }
    } else {
        echo "❌ Failed to mark as processed\n\n";
    }

    // STEP 6: Try to approve again (should fail)
    echo "=== STEP 6: Test Double-Approval Prevention ===\n";
    $doubleApprove = $return->approve();
    
    if (!$doubleApprove) {
        echo "✅ Correctly prevented double approval\n";
        echo "   This ensures inventory can't be incorrectly increased twice\n\n";
    } else {
        echo "❌ ERROR: Double approval was allowed!\n\n";
    }

    // STEP 7: Create another return and reject it
    echo "=== STEP 7: Test Return Rejection ===\n";
    echo "Customer attempts return outside return window...\n";
    
    $rejectedReturn = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'quantity' => 1,
        'price' => 250.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now()->subDays(35), // 35 days ago
        'reason' => 'Customer wants to return - outside return window',
    ]);

    echo "✅ Return created: #{$rejectedReturn->return_id}\n";
    
    $qtyBeforeReject = $product->quantity;
    $rejected = $rejectedReturn->reject();
    
    if ($rejected) {
        echo "✅ Return rejected\n";
        echo "   Status: {$rejectedReturn->return_status}\n";
        
        $product->refresh();
        if ($product->quantity == $qtyBeforeReject) {
            echo "✅ Inventory correctly unchanged after rejection\n\n";
        } else {
            echo "❌ ERROR: Inventory changed after rejection!\n\n";
        }
    }

    // FINAL REPORT
    echo "=== FINAL REPORT ===\n\n";
    
    echo "Initial State:\n";
    echo "  Product Qty: {$initialProductQty}\n";
    echo "  Inventory Qty: {$initialInventoryQty}\n\n";
    
    echo "Transactions:\n";
    echo "  Return #1: +2 units (Approved & Processed)\n";
    echo "  Return #2: +0 units (Rejected)\n\n";
    
    $finalProductQty = $product->quantity;
    $finalInventoryQty = $inventory->quantity_on_hand;
    
    echo "Final State:\n";
    echo "  Product Qty: {$finalProductQty}\n";
    echo "  Inventory Qty: {$finalInventoryQty}\n\n";
    
    echo "Expected Change: +2 units\n";
    echo "Actual Change: +" . ($finalProductQty - $initialProductQty) . " units\n\n";
    
    // All checks
    $return->refresh(); // Refresh to get latest status
    $wasApproved = ($return->isProcessed() || $return->isApproved()); // Processed means it was approved first
    $allChecks = [
        'Return created' => ($return->return_id > 0),
        'Return was approved' => $wasApproved,
        'Product quantity updated' => ($finalProductQty == $initialProductQty + 2),
        'Inventory quantity updated' => ($finalInventoryQty == $initialInventoryQty + 2),
        'Stock movement recorded' => ($movement !== null),
        'Return processed' => $return->isProcessed(),
        'Double approval prevented' => (!$doubleApprove),
        'Rejection handled correctly' => ($rejected && $rejectedReturn->return_status === Returns::STATUS_REJECTED),
    ];
    
    echo "=== VERIFICATION CHECKLIST ===\n";
    foreach ($allChecks as $check => $passed) {
        echo ($passed ? "✅" : "❌") . " {$check}\n";
    }
    echo "\n";
    
    $allPassed = !in_array(false, $allChecks);
    
    if ($allPassed) {
        echo "🎉 SUCCESS! All real-world scenario tests passed!\n";
        echo "The sales return system is fully functional and integrated with inventory.\n";
    } else {
        echo "⚠️ WARNING: Some checks failed. Please review the output above.\n";
    }

    // Cleanup
    DB::rollBack();
    echo "\n✅ Database rolled back (test data removed)\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
