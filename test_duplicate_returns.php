<?php

/**
 * Test Script: Duplicate Return Prevention
 * 
 * This tests if users can create multiple returns for the same sale,
 * which would allow "performing the return" multiple times.
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
echo "║         DUPLICATE RETURN CREATION TEST                              ║\n";
echo "╚══════════════════════════════════════════════════════════════════════╝\n\n";

$allTestsPassed = true;

try {
    DB::beginTransaction();

    // SETUP
    echo "=== SETUP ===\n";
    $product = Product::create([
        'product_name' => 'Test Shoe - Duplicate Return ' . uniqid(),
        'sku' => 'TEST-DR-' . uniqid(),
        'description' => 'Test product',
        'category_id' => 1,
        'price' => 100.00,
        'quantity' => 50,
        'size' => '9',
        'color' => 'Black',
        'product_image' => 'test.jpg'
    ]);
    
    $customer = Customer::first();
    
    $salesOrder = SalesOrder::first();
    if (!$salesOrder) {
        echo "! No sales order found, creating one...\n";
        $salesOrder = SalesOrder::create([
            'customer_id' => $customer->customer_id,
            'order_date' => now(),
            'total_amount' => 1000.00,
            'order_status' => 'completed',
        ]);
    }
    
    echo "✓ Test data created\n";
    echo "  Product: {$product->product_name}\n";
    echo "  Initial Qty: {$product->quantity}\n";
    echo "  Sales Order: {$salesOrder->order_id}\n\n";

    $initialQty = $product->quantity;

    // ==================================================================
    // SCENARIO: Create Multiple Returns for Same Sales Order & Product
    // ==================================================================
    echo "=== SCENARIO: Create 2 Returns for Same Sale ===\n";
    echo "This simulates a user creating and processing the same return twice\n\n";
    
    // First Return
    echo "--- First Return ---\n";
    $return1 = Returns::create([
        'product_id' => $product->product_id,
        'customer_id' => $customer->customer_id,
        'sales_order_id' => $salesOrder->order_id,
        'quantity' => 5,
        'price' => 100.00,
        'return_status' => Returns::STATUS_PENDING,
        'return_date' => now(),
        'reason' => 'Defective product',
    ]);
    
    echo "✓ Return 1 created (ID: {$return1->return_id})\n";
    echo "  Sales Order: {$return1->sales_order_id}\n";
    echo "  Product: {$return1->product_id}\n";
    echo "  Quantity: {$return1->quantity}\n";
    
    // Approve first return
    $return1->approve();
    $product->refresh();
    $qtyAfterFirstReturn = $product->quantity;
    
    echo "✓ Return 1 approved\n";
    echo "  Inventory after: {$qtyAfterFirstReturn} (was {$initialQty})\n\n";
    
    // Second Return (DUPLICATE - Same order, same product)
    echo "--- Second Return (DUPLICATE!) ---\n";
    try {
        $return2 = Returns::create([
            'product_id' => $product->product_id,
            'customer_id' => $customer->customer_id,
            'sales_order_id' => $salesOrder->order_id, // SAME SALES ORDER
            'quantity' => 5, // SAME QUANTITY
            'price' => 100.00,
            'return_status' => Returns::STATUS_PENDING,
            'return_date' => now(),
            'reason' => 'Defective product', // SAME REASON
        ]);
        
        echo "❌ Return 2 was created (ID: {$return2->return_id})\n";
        echo "   This should NOT have been allowed!\n\n";
        $duplicateCreated = true;
    } catch (\Exception $e) {
        echo "✅ Duplicate return prevented!\n";
        echo "   Error message: {$e->getMessage()}\n\n";
        $duplicateCreated = false;
    }
    
    $product->refresh();
    $qtyAfterSecondAttempt = $product->quantity;
    
    // Analysis
    echo "=== ANALYSIS ===\n";
    $totalInventoryIncrease = $qtyAfterSecondAttempt - $initialQty;
    $expectedIncrease = 5; // Only one return should be allowed
    
    echo "Initial Inventory: {$initialQty}\n";
    echo "Final Inventory: {$qtyAfterSecondAttempt}\n";
    echo "Total Increase: {$totalInventoryIncrease}\n";
    echo "Expected Increase: {$expectedIncrease} (one return of 5 units)\n\n";
    
    if (!$duplicateCreated && $totalInventoryIncrease == $expectedIncrease) {
        echo "✅ SUCCESS: Duplicate return was prevented\n";
        echo "   Inventory only increased by {$totalInventoryIncrease} (correct)\n\n";
    } else {
        echo "❌ FAIL: Duplicate return was allowed\n";
        echo "   Inventory increased by {$totalInventoryIncrease} (WRONG!)\n\n";
        $allTestsPassed = false;
    }
    
    // Check database
    echo "=== DATABASE CHECK ===\n";
    $returnsForOrder = Returns::where('sales_order_id', $salesOrder->order_id)
                              ->where('product_id', $product->product_id)
                              ->whereIn('return_status', [Returns::STATUS_APPROVED, Returns::STATUS_PROCESSED])
                              ->count();
    
    echo "Approved/Processed returns for Sales Order #{$salesOrder->order_id} and Product #{$product->product_id}: {$returnsForOrder}\n";
    
    if ($returnsForOrder == 1) {
        echo "✅ Only one approved/processed return exists\n";
    } else {
        echo "❌ Multiple approved/processed returns exist!\n";
        echo "   This should NOT be allowed\n";
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
    echo "║   ✅ Duplicate returns are prevented                                ║\n";
    echo "║   ✅ Users cannot \"perform the return\" multiple times              ║\n";
} else {
    echo "║                    ❌ DUPLICATE RETURN BUG FOUND!                    ║\n";
    echo "║                                                                      ║\n";
    echo "║   Issue: Users can create multiple returns for the same sale        ║\n";
    echo "║   Impact: Inventory is inflated by duplicate returns                ║\n";
    echo "║   Fix needed: Prevent creating returns for already-returned items   ║\n";
}
echo "╚══════════════════════════════════════════════════════════════════════╝\n";

exit($allTestsPassed ? 0 : 1);
