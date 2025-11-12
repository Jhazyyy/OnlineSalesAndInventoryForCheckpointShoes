<?php

/**
 * FINAL COMPREHENSIVE TEST
 * Tests ALL return protections together
 */

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use App\Models\Returns;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SalesOrder;

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "╔══════════════════════════════════════════════════════════════════════╗\n";
echo "║         COMPREHENSIVE RETURN PROTECTION TEST                        ║\n";
echo "║         (All fixes working together)                                ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

$allTestsPassed = true;

try {
    DB::beginTransaction();

    // SETUP
    echo "=== SETUP ===\n";
    $product = Product::create([
        'product_name' => 'Final Test Shoe ' . uniqid(),
        'sku' => 'TEST-FINAL-' . uniqid(),
        'description' => 'Comprehensive test',
        'category_id' => 1,
        'price' => 200.00,
        'quantity' => 100,
        'size' => '10',
        'color' => 'Blue',
        'product_image' => 'test.jpg'
    ]);
    
    $customer = Customer::first();
    $salesOrder = SalesOrder::first();
    
    echo "✓ Test data ready\n";
    echo "  Initial inventory: {$product->quantity}\n\n";

    $initialQty = $product->quantity;
    $passedTests = 0;
    $totalTests = 0;

    // ========================================
    // TEST 1: Normal workflow works
    // ========================================
    $totalTests++;
    echo "=== TEST 1: Normal Return Workflow ===\n";
    $return1 = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'sales_order_id' => $salesOrder->order_id,
        'quantity' => 10,
        'price' => 200.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Normal return',
    ]);
    
    $return1->approve();
    $return1->markAsProcessed();
    $product->refresh();
    
    if ($return1->isProcessed() && $product->quantity == ($initialQty + 10)) {
        echo "✅ PASS: Normal workflow works (pending → approved → processed)\n";
        echo "   Inventory correctly updated: {$product->quantity}\n";
        $passedTests++;
    } else {
        echo "❌ FAIL: Normal workflow broken\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // ========================================
    // TEST 2: Cannot create duplicate return
    // ========================================
    $totalTests++;
    echo "=== TEST 2: Duplicate Return Prevention ===\n";
    try {
        $duplicateReturn = Returns::create([
            'product_id' => $product->product_id,
            'customer_id' => $customer->customer_id,
            'sales_order_id' => $salesOrder->order_id, // SAME ORDER
            'quantity' => 10,
            'price' => 200.00,
            'return_status' => Returns::STATUS_PENDING,
            'return_date' => now(),
            'reason' => 'Duplicate attempt',
        ]);
        
        echo "❌ FAIL: Duplicate return was created!\n";
        $allTestsPassed = false;
    } catch (\Exception $e) {
        $product->refresh();
        if ($product->quantity == ($initialQty + 10)) {
            echo "✅ PASS: Duplicate return prevented\n";
            echo "   Inventory unchanged: {$product->quantity}\n";
            $passedTests++;
        } else {
            echo "❌ FAIL: Inventory was affected by duplicate attempt\n";
            $allTestsPassed = false;
        }
    }
    echo "\n";

    // ========================================
    // TEST 3: Cannot approve already-approved return
    // ========================================
    $totalTests++;
    echo "=== TEST 3: Cannot Double-Approve ===\n";
    $return2 = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'quantity' => 5,
        'price' => 200.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Test double approve',
    ]);
    
    $return2->approve();
    $product->refresh();
    $qtyAfterFirstApprove = $product->quantity;
    
    $secondApprove = $return2->approve();
    $product->refresh();
    
    if (!$secondApprove && $product->quantity == $qtyAfterFirstApprove) {
        echo "✅ PASS: Double approval prevented\n";
        echo "   Inventory unchanged: {$product->quantity}\n";
        $passedTests++;
    } else {
        echo "❌ FAIL: Double approval allowed\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // ========================================
    // TEST 4: Cannot mark pending as processed
    // ========================================
    $totalTests++;
    echo "=== TEST 4: Cannot Skip Approval Step ===\n";
    $return3 = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'quantity' => 3,
        'price' => 200.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Test skip approval',
    ]);
    
    $processWithoutApproval = $return3->markAsProcessed();
    
    if (!$processWithoutApproval && $return3->return_status == Returns::STATUS_PENDING) {
        echo "✅ PASS: Cannot skip approval step\n";
        echo "   Status remains: {$return3->return_status}\n";
        $passedTests++;
    } else {
        echo "❌ FAIL: Approval step was skipped\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // ========================================
    // TEST 5: Cannot process already-processed return
    // ========================================
    $totalTests++;
    echo "=== TEST 5: Cannot Double-Process ===\n";
    $return4 = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'quantity' => 2,
        'price' => 200.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Test double process',
    ]);
    
    $return4->approve();
    $return4->markAsProcessed();
    
    $secondProcess = $return4->markAsProcessed();
    
    if (!$secondProcess && $return4->return_status == Returns::STATUS_PROCESSED) {
        echo "✅ PASS: Double processing prevented\n";
        echo "   Status remains: {$return4->return_status}\n";
        $passedTests++;
    } else {
        echo "❌ FAIL: Double processing allowed\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // ========================================
    // TEST 6: Can create return for DIFFERENT product in same order
    // ========================================
    $totalTests++;
    echo "=== TEST 6: Can Return Different Product from Same Order ===\n";
    $product2 = Product::create([
        'product_name' => 'Different Shoe ' . uniqid(),
        'sku' => 'TEST-DIFF-' . uniqid(),
        'description' => 'Different product',
        'category_id' => 1,
        'price' => 150.00,
        'quantity' => 50,
        'size' => '9',
        'color' => 'Red',
        'product_image' => 'test.jpg'
    ]);
    
    try {
        $differentProductReturn = Returns::create([
            'product_id' => $product2->product_id, // DIFFERENT PRODUCT
            'customer_id' => $customer->customer_id,
            'sales_order_id' => $salesOrder->order_id, // SAME ORDER
            'quantity' => 2,
            'price' => 150.00,
            'return_status' => Returns::STATUS_PENDING,
            'return_date' => now(),
            'reason' => 'Different product return',
        ]);
        
        echo "✅ PASS: Can return different product from same order\n";
        echo "   Return created for Product #{$product2->product_id}\n";
        $passedTests++;
    } catch (\Exception $e) {
        echo "❌ FAIL: Cannot return different product from same order\n";
        echo "   Error: {$e->getMessage()}\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // ========================================
    // TEST 7: Can create return after rejection
    // ========================================
    $totalTests++;
    echo "=== TEST 7: Can Create New Return After Rejection ===\n";
    $product3 = Product::create([
        'product_name' => 'Rejected Test Shoe ' . uniqid(),
        'sku' => 'TEST-REJ-' . uniqid(),
        'description' => 'Test rejection',
        'category_id' => 1,
        'price' => 180.00,
        'quantity' => 75,
        'size' => '8',
        'color' => 'Green',
        'product_image' => 'test.jpg'
    ]);
    
    $salesOrder2 = SalesOrder::create([
        'customer_id' => $customer->customer_id,
        'order_date' => now(),
        'total_amount' => 500.00,
        'order_status' => 'completed',
    ]);
    
    $rejectedReturn = Returns::create([
        'product_id' => $product3->product_id,
        'customer_id' => $customer->customer_id,
        'sales_order_id' => $salesOrder2->order_id,
        'quantity' => 3,
        'price' => 180.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'First attempt',
    ]);
    
    $rejectedReturn->reject();
    
    try {
        $newReturnAfterRejection = Returns::create([
            'product_id' => $product3->product_id,
            'customer_id' => $customer->customer_id,
            'sales_order_id' => $salesOrder2->order_id, // SAME ORDER
            'quantity' => 3,
            'price' => 180.00,
            'return_status' => Returns::STATUS_PENDING,
            'return_date' => now(),
            'reason' => 'Second attempt after rejection',
        ]);
        
        echo "✅ PASS: Can create new return after previous was rejected\n";
        echo "   Return ID: {$newReturnAfterRejection->return_id}\n";
        $passedTests++;
    } catch (\Exception $e) {
        echo "❌ FAIL: Cannot create return after rejection\n";
        echo "   Error: {$e->getMessage()}\n";
        $allTestsPassed = false;
    }
    echo "\n";

    // ========================================
    // FINAL INVENTORY VERIFICATION
    // ========================================
    echo "=== FINAL INVENTORY VERIFICATION ===\n";
    $product->refresh();
    $finalQty = $product->quantity;
    
    // Approved returns: return1 (10), return2 (5), return4 (2) = +17
    $expectedQty = $initialQty + 17;
    
    echo "Initial Quantity: {$initialQty}\n";
    echo "Approved Returns: 10 + 5 + 2 = 17 units\n";
    echo "Expected Final: {$expectedQty}\n";
    echo "Actual Final: {$finalQty}\n";
    
    if ($finalQty == $expectedQty) {
        echo "✅ Inventory is correct!\n";
    } else {
        echo "❌ Inventory mismatch!\n";
        $allTestsPassed = false;
    }
    echo "\n";

    DB::rollBack();
    echo "✓ Test data cleaned up\n\n";

    // ========================================
    // SUMMARY
    // ========================================
    echo "╔══════════════════════════════════════════════════════════════════════╗\n";
    echo "║                        TEST SUMMARY                                  ║\n";
    echo "╠══════════════════════════════════════════════════════════════════════╣\n";
    echo "║  Tests Passed: {$passedTests}/{$totalTests}                                               ║\n";
    echo "╠══════════════════════════════════════════════════════════════════════╣\n";
    
    if ($allTestsPassed) {
        echo "║                   🎉 ALL PROTECTIONS WORKING! 🎉                    ║\n";
        echo "║                                                                      ║\n";
        echo "║  ✅ Normal workflow works correctly                                 ║\n";
        echo "║  ✅ Duplicate returns prevented                                     ║\n";
        echo "║  ✅ Double approval prevented                                       ║\n";
        echo "║  ✅ Cannot skip approval step                                       ║\n";
        echo "║  ✅ Double processing prevented                                     ║\n";
        echo "║  ✅ Different products can be returned separately                   ║\n";
        echo "║  ✅ New returns allowed after rejection                             ║\n";
        echo "║  ✅ Inventory accuracy maintained                                   ║\n";
    } else {
        echo "║                    ⚠️  SOME TESTS FAILED                             ║\n";
        echo "║                                                                      ║\n";
        echo "║  Please review the output above for details                         ║\n";
    }
    
    echo "╚══════════════════════════════════════════════════════════════════════╝\n";

} catch (\Exception $e) {
    DB::rollBack();
    echo "❌ EXCEPTION: {$e->getMessage()}\n";
    echo "   File: {$e->getFile()}\n";
    echo "   Line: {$e->getLine()}\n";
    $allTestsPassed = false;
}

exit($allTestsPassed ? 0 : 1);
