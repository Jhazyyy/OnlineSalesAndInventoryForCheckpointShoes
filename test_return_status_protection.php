<?php

/**
 * Test Script: Complete Return Status Transition Protection
 * 
 * This script tests ALL possible ways to inappropriately change return status
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Returns;
use App\Models\Product;
use App\Models\Customer;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║         COMPLETE RETURN STATUS PROTECTION TEST                      ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

$allTestsPassed = true;

try {
    DB::beginTransaction();

    // SETUP
    echo "=== SETUP ===\n";
    $product = Product::create([
        'product_name' => 'Test Shoe - Status Protection ' . uniqid(),
        'sku' => 'TEST-SP-' . uniqid(),
        'description' => 'Test product',
        'category_id' => 1,
        'price' => 100.00,
        'quantity' => 100,
        'size' => '9',
        'color' => 'Black',
        'product_image' => 'test.jpg'
    ]);
    $customer = Customer::first();
    echo "✓ Test data created\n\n";

    $initialQty = $product->quantity;

    // ========================================
    // SCENARIO 1: Try to approve APPROVED return
    // ========================================
    echo "=== SCENARIO 1: Try to Approve Already-Approved Return ===\n";
    $return1 = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'quantity' => 5,
        'price' => 100.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Test 1',
    ]);
    
    // First approval (should work)
    $return1->approve();
    $product->refresh();
    $qtyAfterFirstApproval = $product->quantity;
    echo "✓ Return approved (Status: {$return1->return_status})\n";
    echo "  Inventory: {$qtyAfterFirstApproval} (was {$initialQty})\n";
    
    // Try second approval (should fail)
    $secondApproval = $return1->approve();
    $product->refresh();
    $qtyAfterSecondApproval = $product->quantity;
    
    if (!$secondApproval && $qtyAfterSecondApproval == $qtyAfterFirstApproval) {
        echo "✅ PASS: Cannot approve already-approved return\n";
        echo "   Inventory unchanged: {$qtyAfterSecondApproval}\n";
    } else {
        echo "❌ FAIL: Approved return was approved again!\n";
        echo "   Inventory: {$qtyAfterSecondApproval} (WRONG!)\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // ========================================
    // SCENARIO 2: Try to approve PROCESSED return
    // ========================================
    echo "=== SCENARIO 2: Try to Approve Processed Return ===\n";
    $return2 = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'quantity' => 5,
        'price' => 100.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Test 2',
    ]);
    
    $return2->approve();
    $product->refresh();
    $qtyAfterApproval = $product->quantity;
    
    $return2->markAsProcessed();
    echo "✓ Return approved and processed (Status: {$return2->return_status})\n";
    
    // Try to approve processed return
    $approveProcessed = $return2->approve();
    $product->refresh();
    $qtyAfterAttempt = $product->quantity;
    
    if (!$approveProcessed && $qtyAfterAttempt == $qtyAfterApproval) {
        echo "✅ PASS: Cannot approve processed return\n";
        echo "   Inventory unchanged: {$qtyAfterAttempt}\n";
    } else {
        echo "❌ FAIL: Processed return was approved again!\n";
        echo "   Inventory: {$qtyAfterAttempt} (WRONG!)\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // ========================================
    // SCENARIO 3: Try to reject APPROVED return
    // ========================================
    echo "=== SCENARIO 3: Try to Reject Approved Return ===\n";
    $return3 = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'quantity' => 5,
        'price' => 100.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Test 3',
    ]);
    
    $return3->approve();
    echo "✓ Return approved (Status: {$return3->return_status})\n";
    
    $rejectApproved = $return3->reject();
    
    if (!$rejectApproved && $return3->return_status == Returns::STATUS_APPROVED) {
        echo "✅ PASS: Cannot reject approved return\n";
        echo "   Status remains: {$return3->return_status}\n";
    } else {
        echo "❌ FAIL: Approved return was rejected!\n";
        echo "   Status: {$return3->return_status} (WRONG!)\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // ========================================
    // SCENARIO 4: Try to mark PENDING as processed
    // ========================================
    echo "=== SCENARIO 4: Try to Mark Pending Return as Processed ===\n";
    $return4 = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'quantity' => 5,
        'price' => 100.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Test 4',
    ]);
    
    echo "✓ Return created (Status: {$return4->return_status})\n";
    
    $processPending = $return4->markAsProcessed();
    
    if (!$processPending && $return4->return_status == Returns::STATUS_PENDING) {
        echo "✅ PASS: Cannot mark pending return as processed\n";
        echo "   Status remains: {$return4->return_status}\n";
    } else {
        echo "❌ FAIL: Pending return was marked as processed!\n";
        echo "   Status: {$return4->return_status} (WRONG!)\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // ========================================
    // SCENARIO 5: Try to mark PROCESSED as processed again
    // ========================================
    echo "=== SCENARIO 5: Try to Mark Processed Return as Processed Again ===\n";
    $return5 = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'quantity' => 5,
        'price' => 100.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Test 5',
    ]);
    
    $return5->approve();
    $return5->markAsProcessed();
    echo "✓ Return processed (Status: {$return5->return_status})\n";
    
    $processAgain = $return5->markAsProcessed();
    
    if (!$processAgain && $return5->return_status == Returns::STATUS_PROCESSED) {
        echo "✅ PASS: Cannot mark processed return as processed again\n";
        echo "   Status remains: {$return5->return_status}\n";
    } else {
        echo "❌ FAIL: Processed return was processed again!\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // ========================================
    // SCENARIO 6: Try to approve REJECTED return
    // ========================================
    echo "=== SCENARIO 6: Try to Approve Rejected Return ===\n";
    $return6 = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'quantity' => 5,
        'price' => 100.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Test 6',
    ]);
    
    $return6->reject();
    $product->refresh();
    $qtyBeforeApprovalAttempt = $product->quantity;
    echo "✓ Return rejected (Status: {$return6->return_status})\n";
    
    $approveRejected = $return6->approve();
    $product->refresh();
    $qtyAfterApprovalAttempt = $product->quantity;
    
    if (!$approveRejected && $return6->return_status == Returns::STATUS_REJECTED && $qtyAfterApprovalAttempt == $qtyBeforeApprovalAttempt) {
        echo "✅ PASS: Cannot approve rejected return\n";
        echo "   Status remains: {$return6->return_status}\n";
        echo "   Inventory unchanged: {$qtyAfterApprovalAttempt}\n";
    } else {
        echo "❌ FAIL: Rejected return was approved!\n";
        echo "   Status: {$return6->return_status} (WRONG!)\n";
        echo "   Inventory: {$qtyAfterApprovalAttempt} (may be incorrect)\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // Final inventory check
    echo "=== FINAL INVENTORY CHECK ===\n";
    $product->refresh();
    $finalQty = $product->quantity;
    // Only return1, return2, and return5 were approved (3 returns x 5 units = +15)
    // return3 was approved (+5) = +20 total
    $expectedFinalQty = $initialQty + 20; // 4 successful approvals
    
    echo "Initial Quantity: {$initialQty}\n";
    echo "Successful Approvals: 4 x 5 units = +20\n";
    echo "Expected Final: {$expectedFinalQty}\n";
    echo "Actual Final: {$finalQty}\n";
    
    if ($finalQty == $expectedFinalQty) {
        echo "✅ PASS: Final inventory is correct\n";
    } else {
        echo "❌ FAIL: Final inventory is WRONG\n";
        $difference = $finalQty - $expectedFinalQty;
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
    echo "║   ✅ All status transitions are properly protected                  ║\n";
    echo "║   ✅ No duplicate processing possible                               ║\n";
    echo "║   ✅ Inventory integrity maintained                                 ║\n";
} else {
    echo "║                    ❌ STATUS PROTECTION FAILED!                      ║\n";
    echo "║                                                                      ║\n";
    echo "║   Critical issues found with return status transitions              ║\n";
}
echo "╚══════════════════════════════════════════════════════════════════════╝\n";

exit($allTestsPassed ? 0 : 1);
