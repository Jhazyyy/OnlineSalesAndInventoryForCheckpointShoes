<?php

/**
 * Test Script: Return Double Approval Prevention
 * 
 * This script tests if a return can be approved multiple times,
 * which would cause duplicate inventory updates.
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Returns;
use App\Models\Product;
use App\Models\Customer;
use App\Models\StockMovement;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║         RETURN DOUBLE APPROVAL PREVENTION TEST                      ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

$allTestsPassed = true;

try {
    DB::beginTransaction();

    // SETUP
    echo "=== SETUP: Creating Test Data ===\n";
    $product = Product::create([
        'product_name' => 'Test Shoe - Double Approval ' . uniqid(),
        'sku' => 'TEST-DA-' . uniqid(),
        'description' => 'Test product for double approval test',
        'category_id' => 1,
        'price' => 150.00,
        'quantity' => 100,
        'size' => '9',
        'color' => 'Black',
        'product_image' => 'test.jpg'
    ]);
    echo "✓ Created test product: {$product->product_name}\n";
    echo "  Initial quantity: {$product->quantity}\n";

    $customer = Customer::first();
    echo "\n";

    $initialProductQty = $product->quantity;

    // TEST 1: Create and Approve Return
    echo "=== TEST 1: Create and Approve Return (First Time) ===\n";
    $return = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'quantity' => 10,
        'price' => 150.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Testing double approval prevention',
    ]);

    echo "✓ Return created (ID: {$return->return_id})\n";
    echo "  Status: {$return->return_status}\n";
    echo "  Quantity: {$return->quantity}\n\n";

    // First approval
    $product->refresh();
    $qtyBeforeFirstApproval = $product->quantity;
    
    $firstApproval = $return->approve();
    $return->refresh();
    
    if ($firstApproval && $return->isApproved()) {
        echo "✅ First approval successful\n";
        echo "   Status: {$return->return_status}\n";
        
        $product->refresh();
        $qtyAfterFirstApproval = $product->quantity;
        $expectedQty = $initialProductQty + 10;
        
        if ($qtyAfterFirstApproval == $expectedQty) {
            echo "✅ Inventory updated correctly\n";
            echo "   Before: {$qtyBeforeFirstApproval}, After: {$qtyAfterFirstApproval}, Expected: {$expectedQty}\n";
        } else {
            echo "❌ Inventory not updated correctly\n";
            echo "   Before: {$qtyBeforeFirstApproval}, After: {$qtyAfterFirstApproval}, Expected: {$expectedQty}\n";
            $allTestsPassed = false;
        }
    } else {
        echo "❌ Failed to approve return\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // TEST 2: Try to Approve Again (Should Fail)
    echo "=== TEST 2: Attempt to Approve Again (Should Fail) ===\n";
    $product->refresh();
    $qtyBeforeSecondApproval = $product->quantity;
    
    $secondApproval = $return->approve();
    $return->refresh();
    
    if (!$secondApproval) {
        echo "✅ Correctly prevented double approval\n";
        echo "   Return status remains: {$return->return_status}\n";
        
        $product->refresh();
        $qtyAfterSecondApproval = $product->quantity;
        
        if ($qtyAfterSecondApproval == $qtyBeforeSecondApproval) {
            echo "✅ Inventory correctly unchanged after second approval attempt\n";
            echo "   Quantity remains: {$qtyAfterSecondApproval}\n";
        } else {
            echo "❌ CRITICAL: Inventory was updated again!\n";
            echo "   Before: {$qtyBeforeSecondApproval}, After: {$qtyAfterSecondApproval}\n";
            echo "   This means inventory was incorrectly increased by {$return->quantity} units a second time!\n";
            $allTestsPassed = false;
        }
    } else {
        echo "❌ CRITICAL ERROR: Double approval was allowed!\n";
        echo "   This is a serious bug that causes inventory discrepancies\n";
        $allTestsPassed = false;
        
        $product->refresh();
        echo "   Inventory after double approval: {$product->quantity}\n";
    }
    echo "\n";

    // TEST 3: Check Stock Movements
    echo "=== TEST 3: Verify Stock Movements ===\n";
    $stockMovements = StockMovement::where('reference_type', 'sales_return')
        ->where('reference_id', $return->return_id)
        ->get();
    
    $movementCount = $stockMovements->count();
    
    if ($movementCount == 1) {
        echo "✅ Exactly one stock movement recorded\n";
    } else {
        echo "❌ Incorrect number of stock movements: {$movementCount}\n";
        echo "   Expected: 1 (only from first approval)\n";
        if ($movementCount > 1) {
            echo "   This indicates duplicate inventory updates!\n";
        }
        $allTestsPassed = false;
    }
    echo "\n";

    // TEST 4: Final State Verification
    echo "=== TEST 4: Final State Verification ===\n";
    $product->refresh();
    $finalProductQty = $product->quantity;
    $expectedFinalQty = $initialProductQty + 10;

    echo "State Summary:\n";
    echo "  Initial Quantity: {$initialProductQty}\n";
    echo "  Return Quantity: 10\n";
    echo "  Expected Final: {$expectedFinalQty}\n";
    echo "  Actual Final: {$finalProductQty}\n";
    echo "  Stock Movements: {$movementCount}\n\n";

    if ($finalProductQty == $expectedFinalQty) {
        echo "✅ Final inventory quantity is correct\n";
    } else {
        echo "❌ Final inventory quantity is INCORRECT\n";
        $difference = $finalProductQty - $expectedFinalQty;
        echo "   Difference: {$difference} units\n";
        $allTestsPassed = false;
    }
    echo "\n";

    DB::rollBack();
    echo "✓ Test data cleaned up\n\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ EXCEPTION: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}\n";
    echo "   Line: {$e->getLine()}\n\n";
    $allTestsPassed = false;
}

// Final Result
echo "╔══════════════════════════════════════════════════════════════════════╗\n";
if ($allTestsPassed) {
    echo "║                        🎉 ALL TESTS PASSED! 🎉                       ║\n";
    echo "║                                                                      ║\n";
    echo "║   ✅ Double approval is prevented                                   ║\n";
    echo "║   ✅ Inventory only updated once                                    ║\n";
    echo "║   ✅ Stock movements correctly recorded                             ║\n";
} else {
    echo "║                    ❌ CRITICAL BUG DETECTED!                         ║\n";
    echo "║                                                                      ║\n";
    echo "║   Returns can be approved multiple times, causing:                  ║\n";
    echo "║   - Duplicate inventory updates                                     ║\n";
    echo "║   - Incorrect stock levels                                          ║\n";
    echo "║   - Data integrity issues                                           ║\n";
}
echo "╚══════════════════════════════════════════════════════════════════════╝\n";

exit($allTestsPassed ? 0 : 1);
