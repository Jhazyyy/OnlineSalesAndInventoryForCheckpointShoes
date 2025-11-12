<?php

/**
 * Test Script: Return Single Processing Prevention
 * 
 * This script tests that a return can only be processed once.
 * 
 * Test Cases:
 * 1. Create a return in pending status
 * 2. Approve the return
 * 3. Mark as processed successfully
 * 4. Attempt to mark as processed again (should fail)
 * 5. Verify inventory was only updated once
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
echo "║         RETURN SINGLE PROCESSING PREVENTION TEST                    ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

$allTestsPassed = true;

try {
    DB::beginTransaction();

    // SETUP: Create test product
    echo "=== SETUP: Creating Test Data ===\n";
    $product = Product::create([
        'product_name' => 'Test Shoe - Single Process ' . uniqid(),
        'sku' => 'TEST-SP-' . uniqid(),
        'description' => 'Test product for single processing test',
        'category_id' => 1,
        'price' => 150.00,
        'quantity' => 50,
        'size' => '9',
        'color' => 'Black',
        'product_image' => 'test.jpg'
    ]);
    echo "✓ Created test product: {$product->product_name}\n";
    echo "  Initial quantity: {$product->quantity}\n";

    $customer = Customer::first();
    if (!$customer) {
        $customer = Customer::create([
            'first_name' => 'Test',
            'last_name' => 'Customer',
            'email' => 'test' . uniqid() . '@example.com',
            'phone' => '1234567890',
            'address' => '123 Test St'
        ]);
        echo "✓ Created test customer\n";
    } else {
        echo "✓ Using existing customer\n";
    }
    echo "\n";

    $initialProductQty = $product->quantity;

    // TEST 1: Create Return
    echo "=== TEST 1: Create Return ===\n";
    $return = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'quantity' => 5,
        'price' => 150.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Testing single processing prevention',
    ]);

    if ($return && $return->return_id) {
        echo "✅ Return created successfully\n";
        echo "   Return ID: {$return->return_id}\n";
        echo "   Status: {$return->return_status}\n";
        echo "   Quantity: {$return->quantity}\n";
    } else {
        echo "❌ Failed to create return\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // TEST 2: Approve Return
    echo "=== TEST 2: Approve Return ===\n";
    $product->refresh();
    $qtyBeforeApproval = $product->quantity;
    
    $approved = $return->approve();
    
    if ($approved && $return->isApproved()) {
        echo "✅ Return approved successfully\n";
        echo "   Status: {$return->return_status}\n";
        
        $product->refresh();
        $qtyAfterApproval = $product->quantity;
        $expectedQty = $initialProductQty + 5;
        
        if ($qtyAfterApproval == $expectedQty) {
            echo "✅ Inventory updated correctly after approval\n";
            echo "   Before: {$qtyBeforeApproval}, After: {$qtyAfterApproval}, Expected: {$expectedQty}\n";
        } else {
            echo "❌ Inventory not updated correctly\n";
            echo "   Before: {$qtyBeforeApproval}, After: {$qtyAfterApproval}, Expected: {$expectedQty}\n";
            $allTestsPassed = false;
        }
    } else {
        echo "❌ Failed to approve return\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // TEST 3: Mark as Processed (First Time - Should Succeed)
    echo "=== TEST 3: Mark as Processed (First Time) ===\n";
    $product->refresh();
    $qtyBeforeProcessing = $product->quantity;
    
    $processed = $return->markAsProcessed();
    $return->refresh();
    
    if ($processed && $return->isProcessed()) {
        echo "✅ Return marked as processed successfully\n";
        echo "   Status: {$return->return_status}\n";
        
        $product->refresh();
        $qtyAfterProcessing = $product->quantity;
        
        // Quantity should NOT change when marking as processed
        // (inventory was already updated during approval)
        if ($qtyAfterProcessing == $qtyBeforeProcessing) {
            echo "✅ Inventory correctly unchanged during processing\n";
            echo "   Quantity remains: {$qtyAfterProcessing}\n";
        } else {
            echo "❌ Inventory incorrectly changed during processing\n";
            echo "   Before: {$qtyBeforeProcessing}, After: {$qtyAfterProcessing}\n";
            $allTestsPassed = false;
        }
    } else {
        echo "❌ Failed to mark return as processed\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // TEST 4: Attempt to Mark as Processed Again (Should Fail)
    echo "=== TEST 4: Attempt to Mark as Processed Again (Should Fail) ===\n";
    $product->refresh();
    $qtyBeforeSecondProcessing = $product->quantity;
    
    $secondProcessing = $return->markAsProcessed();
    $return->refresh();
    
    if (!$secondProcessing) {
        echo "✅ Correctly prevented double processing\n";
        echo "   Return status remains: {$return->return_status}\n";
        
        $product->refresh();
        $qtyAfterSecondAttempt = $product->quantity;
        
        if ($qtyAfterSecondAttempt == $qtyBeforeSecondProcessing) {
            echo "✅ Inventory correctly unchanged after second processing attempt\n";
            echo "   Quantity remains: {$qtyAfterSecondAttempt}\n";
        } else {
            echo "❌ Inventory incorrectly changed after second processing attempt\n";
            echo "   Before: {$qtyBeforeSecondProcessing}, After: {$qtyAfterSecondAttempt}\n";
            $allTestsPassed = false;
        }
    } else {
        echo "❌ CRITICAL ERROR: Double processing was allowed!\n";
        echo "   This is a serious bug that could lead to inventory discrepancies\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // TEST 5: Verify Stock Movements
    echo "=== TEST 5: Verify Stock Movements ===\n";
    $stockMovements = StockMovement::where('reference_type', 'sales_return')
        ->where('reference_id', $return->return_id)
        ->get();
    
    $movementCount = $stockMovements->count();
    
    if ($movementCount == 1) {
        echo "✅ Exactly one stock movement recorded\n";
        $movement = $stockMovements->first();
        echo "   Movement Type: {$movement->movement_type}\n";
        echo "   Quantity: {$movement->quantity}\n";
        echo "   Reference: {$movement->reference_type}#{$movement->reference_id}\n";
    } else {
        echo "❌ Incorrect number of stock movements\n";
        echo "   Expected: 1, Found: {$movementCount}\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // TEST 6: Verify Final State
    echo "=== TEST 6: Verify Final State ===\n";
    $product->refresh();
    $finalProductQty = $product->quantity;
    $expectedFinalQty = $initialProductQty + 5; // Added 5 from return approval

    echo "State Summary:\n";
    echo "  Initial Product Qty: {$initialProductQty}\n";
    echo "  Return Quantity: 5\n";
    echo "  Expected Final Qty: {$expectedFinalQty}\n";
    echo "  Actual Final Qty: {$finalProductQty}\n";
    echo "  Return Status: {$return->return_status}\n";
    echo "  Stock Movements: {$movementCount}\n\n";

    $stateChecks = [
        'Final quantity correct' => ($finalProductQty == $expectedFinalQty),
        'Return is processed' => $return->isProcessed(),
        'Return is not approved' => !$return->isApproved(), // Once processed, no longer "approved"
        'Only one stock movement' => ($movementCount == 1),
    ];

    foreach ($stateChecks as $check => $passed) {
        echo ($passed ? "✅" : "❌") . " {$check}\n";
        if (!$passed) {
            $allTestsPassed = false;
        }
    }
    echo "\n";

    DB::rollBack();
    echo "✓ Test data cleaned up (transaction rolled back)\n\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ EXCEPTION OCCURRED: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}\n";
    echo "   Line: {$e->getLine()}\n\n";
    $allTestsPassed = false;
}

// Final Result
echo "╔══════════════════════════════════════════════════════════════════════╗\n";
if ($allTestsPassed) {
    echo "║                        🎉 ALL TESTS PASSED! 🎉                       ║\n";
    echo "║                                                                      ║\n";
    echo "║   ✅ Returns can only be processed once                             ║\n";
    echo "║   ✅ Double processing is prevented                                 ║\n";
    echo "║   ✅ Inventory is only updated during approval                      ║\n";
    echo "║   ✅ Stock movements are correctly recorded                         ║\n";
} else {
    echo "║                        ❌ SOME TESTS FAILED                          ║\n";
    echo "║                                                                      ║\n";
    echo "║   Please review the output above for details                        ║\n";
}
echo "╚══════════════════════════════════════════════════════════════════════╝\n";

exit($allTestsPassed ? 0 : 1);
