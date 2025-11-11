<?php

/**
 * Comprehensive test for sales returns inventory integration
 * Tests: create, approve, reject, bulk approve, and edge cases
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Returns;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\StockMovement;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

echo "=== Comprehensive Sales Return Testing ===\n\n";

$allTestsPassed = true;

try {
    DB::beginTransaction();

    // Get test data
    $product = Product::first();
    $customer = Customer::first();
    
    if (!$product) {
        echo "❌ No products found. Please add products to test.\n";
        exit(1);
    }

    echo "📦 Test Product: {$product->product_name} (ID: {$product->product_id})\n";
    $initialProductQty = $product->quantity;
    echo "   Initial Product Quantity: {$initialProductQty}\n";

    $inventory = Inventory::where('product_id', $product->product_id)->first();
    $initialInventoryQty = $inventory ? $inventory->quantity_on_hand : 0;
    echo "   Initial Inventory Quantity: {$initialInventoryQty}\n\n";

    // TEST 1: Create and Approve Single Return
    echo "=== TEST 1: Create and Approve Single Return ===\n";
    $return1 = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer?->customer_id,
        'quantity' => 10,
        'price' => 150.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Test return 1',
    ]);

    echo "✅ Created return #{$return1->return_id}\n";

    $approved1 = $return1->approve();
    if (!$approved1) {
        echo "❌ Failed to approve return 1\n";
        $allTestsPassed = false;
    } else {
        $product->refresh();
        $inventory = $inventory->fresh();
        
        $expectedQty = $initialProductQty + 10;
        $actualQty = $product->quantity;
        
        if ($actualQty == $expectedQty) {
            echo "✅ Product quantity updated correctly: {$actualQty}\n";
        } else {
            echo "❌ Product quantity mismatch. Expected: {$expectedQty}, Got: {$actualQty}\n";
            $allTestsPassed = false;
        }

        $stockMovement1 = StockMovement::where('reference_type', 'sales_return')
            ->where('reference_id', $return1->return_id)
            ->first();
        
        if ($stockMovement1) {
            echo "✅ Stock movement recorded\n";
        } else {
            echo "❌ Stock movement not recorded\n";
            $allTestsPassed = false;
        }
    }
    echo "\n";

    // TEST 2: Try to Approve Already Approved Return (should fail)
    echo "=== TEST 2: Prevent Double Approval ===\n";
    $doubleApprove = $return1->approve();
    if (!$doubleApprove) {
        echo "✅ Correctly prevented double approval\n";
    } else {
        echo "❌ Failed to prevent double approval\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // TEST 3: Reject a Pending Return
    echo "=== TEST 3: Reject Pending Return ===\n";
    $return2 = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer?->customer_id,
        'quantity' => 5,
        'price' => 100.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Test return 2 - to be rejected',
    ]);

    $product->refresh();
    $qtyBeforeReject = $product->quantity;
    
    $rejected = $return2->reject();
    if ($rejected) {
        echo "✅ Return rejected successfully\n";
        
        $product->refresh();
        $qtyAfterReject = $product->quantity;
        
        if ($qtyBeforeReject == $qtyAfterReject) {
            echo "✅ Product quantity unchanged after rejection: {$qtyAfterReject}\n";
        } else {
            echo "❌ Product quantity changed after rejection\n";
            $allTestsPassed = false;
        }
    } else {
        echo "❌ Failed to reject return\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // TEST 4: Bulk Approve Returns
    echo "=== TEST 4: Bulk Approve Returns ===\n";
    $bulkReturns = [];
    $bulkTotalQty = 0;
    
    for ($i = 0; $i < 3; $i++) {
        $qty = 3;
        $bulkTotalQty += $qty;
        
        $bulkReturn = Returns::create([
            'product_id' => $product->product_id,
            'customer_id' => $customer?->customer_id,
            'quantity' => $qty,
            'price' => 120.00,
            'return_status' => Returns::STATUS_PENDING,
            'return_date' => now(),
            'reason' => "Bulk test return " . ($i + 1),
        ]);
        $bulkReturns[] = $bulkReturn;
    }
    
    echo "✅ Created " . count($bulkReturns) . " pending returns\n";
    
    $product->refresh();
    $qtyBeforeBulk = $product->quantity;
    
    $approvedCount = 0;
    foreach ($bulkReturns as $bulkReturn) {
        if ($bulkReturn->approve()) {
            $approvedCount++;
        }
    }
    
    echo "✅ Approved {$approvedCount}/" . count($bulkReturns) . " returns\n";
    
    $product->refresh();
    $qtyAfterBulk = $product->quantity;
    $expectedBulkQty = $qtyBeforeBulk + $bulkTotalQty;
    
    if ($qtyAfterBulk == $expectedBulkQty) {
        echo "✅ Bulk approval updated quantity correctly: {$qtyAfterBulk}\n";
    } else {
        echo "❌ Bulk approval quantity mismatch. Expected: {$expectedBulkQty}, Got: {$qtyAfterBulk}\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // TEST 5: Verify Inventory Table Consistency
    echo "=== TEST 5: Verify Inventory Table Consistency ===\n";
    $product->refresh();
    $inventory = Inventory::where('product_id', $product->product_id)->first();
    
    if ($inventory) {
        $productQty = $product->quantity;
        $inventoryQty = $inventory->quantity_on_hand;
        
        if ($productQty == $inventoryQty) {
            echo "✅ Product and Inventory quantities match: {$productQty}\n";
        } else {
            echo "❌ Product ({$productQty}) and Inventory ({$inventoryQty}) quantities don't match\n";
            $allTestsPassed = false;
        }
    } else {
        echo "⚠️ No inventory record found\n";
    }
    echo "\n";

    // TEST 6: Verify Stock Movements
    echo "=== TEST 6: Verify Stock Movement Records ===\n";
    $allReturns = array_merge([$return1], $bulkReturns);
    $movementCount = 0;
    
    foreach ($allReturns as $testReturn) {
        $movement = StockMovement::where('reference_type', 'sales_return')
            ->where('reference_id', $testReturn->return_id)
            ->first();
        
        if ($movement) {
            $movementCount++;
        }
    }
    
    $expectedMovements = count($allReturns);
    if ($movementCount == $expectedMovements) {
        echo "✅ All {$movementCount} stock movements recorded correctly\n";
    } else {
        echo "❌ Stock movement mismatch. Expected: {$expectedMovements}, Found: {$movementCount}\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // TEST 7: Mark Approved Return as Processed
    echo "=== TEST 7: Mark as Processed ===\n";
    $product->refresh();
    $qtyBeforeProcessed = $product->quantity;
    
    $processed = $return1->markAsProcessed();
    if ($processed && $return1->isProcessed()) {
        echo "✅ Return marked as processed\n";
        
        $product->refresh();
        $qtyAfterProcessed = $product->quantity;
        
        if ($qtyBeforeProcessed == $qtyAfterProcessed) {
            echo "✅ Quantity unchanged when marking as processed: {$qtyAfterProcessed}\n";
        } else {
            echo "❌ Quantity changed when marking as processed\n";
            $allTestsPassed = false;
        }
    } else {
        echo "❌ Failed to mark as processed\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // FINAL SUMMARY
    echo "=== FINAL SUMMARY ===\n";
    $totalReturned = 10 + (3 * 3); // return1 (10) + 3 bulk returns (3 each)
    $finalProductQty = $product->quantity;
    $expectedFinalQty = $initialProductQty + $totalReturned;
    
    echo "Initial Quantity: {$initialProductQty}\n";
    echo "Total Returned & Approved: {$totalReturned}\n";
    echo "Expected Final Quantity: {$expectedFinalQty}\n";
    echo "Actual Final Quantity: {$finalProductQty}\n\n";

    if ($finalProductQty == $expectedFinalQty && $allTestsPassed) {
        echo "🎉 ALL TESTS PASSED! Sales returns are fully integrated with inventory.\n";
    } else {
        echo "⚠️ SOME TESTS FAILED. Please review the output above.\n";
    }

    // Rollback to keep database clean
    DB::rollBack();
    echo "\n✅ Database rolled back (test data removed)\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "\n❌ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
