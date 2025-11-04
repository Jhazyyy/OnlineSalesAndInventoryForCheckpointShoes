<?php

/**
 * Test Sales Order Purchase Type Implementation
 * 
 * This script tests the new purchase_type functionality:
 * 1. In-store + paid = delivered status
 * 2. In-store + unpaid = pending status
 * 3. Online = normal flow
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\Product;
use App\Services\SalesOrderService;
use Carbon\Carbon;

try {
    echo "Testing Sales Order Purchase Type Implementation\n";
    echo "================================================\n\n";

    // Get required data
    $customer = Customer::first();
    if (!$customer) {
        echo "❌ No customer found. Please create a customer first.\n";
        exit(1);
    }

    $product = Product::where('quantity', '>', 0)->first();
    if (!$product) {
        echo "❌ No product with stock found.\n";
        exit(1);
    }

    $service = new SalesOrderService();

    // Test 1: In-Store + Paid = Delivered
    echo "Test 1: In-Store Purchase + Paid\n";
    echo "---------------------------------\n";
    
    $orderData1 = [
        'customer_id' => $customer->customer_id,
        'order_date' => Carbon::today(),
        'payment_status' => 'paid',
        'payment_method' => 'cash',
        'purchase_type' => 'in_store',
        'items' => [
            [
                'product_id' => $product->product_id,
                'quantity' => 1,
                'unit_price' => $product->price,
            ]
        ]
    ];

    $order1 = $service->createOrder($orderData1);
    echo "✓ Order Created: {$order1->order_number}\n";
    echo "  - Purchase Type: {$order1->purchase_type}\n";
    echo "  - Payment Status: {$order1->payment_status}\n";
    echo "  - Order Status: {$order1->status}\n";
    echo "  - Shipped Date: " . ($order1->shipped_date ? $order1->shipped_date->format('Y-m-d') : 'Not set') . "\n";
    
    if ($order1->status === 'delivered') {
        echo "  ✅ PASS: Status is 'delivered' as expected\n";
    } else {
        echo "  ❌ FAIL: Status should be 'delivered' but is '{$order1->status}'\n";
    }
    echo "\n";

    // Test 2: In-Store + Pending = Pending
    echo "Test 2: In-Store Purchase + Pending Payment\n";
    echo "--------------------------------------------\n";
    
    $orderData2 = [
        'customer_id' => $customer->customer_id,
        'order_date' => Carbon::today(),
        'payment_status' => 'pending',
        'payment_method' => 'cash',
        'purchase_type' => 'in_store',
        'items' => [
            [
                'product_id' => $product->product_id,
                'quantity' => 1,
                'unit_price' => $product->price,
            ]
        ]
    ];

    $order2 = $service->createOrder($orderData2);
    echo "✓ Order Created: {$order2->order_number}\n";
    echo "  - Purchase Type: {$order2->purchase_type}\n";
    echo "  - Payment Status: {$order2->payment_status}\n";
    echo "  - Order Status: {$order2->status}\n";
    
    if ($order2->status === 'pending') {
        echo "  ✅ PASS: Status is 'pending' as expected\n";
    } else {
        echo "  ❌ FAIL: Status should be 'pending' but is '{$order2->status}'\n";
    }
    echo "\n";

    // Test 3: Online + Paid = Pending (normal flow)
    echo "Test 3: Online Purchase + Paid\n";
    echo "-------------------------------\n";
    
    $orderData3 = [
        'customer_id' => $customer->customer_id,
        'order_date' => Carbon::today(),
        'payment_status' => 'paid',
        'payment_method' => 'card',
        'purchase_type' => 'online',
        'items' => [
            [
                'product_id' => $product->product_id,
                'quantity' => 1,
                'unit_price' => $product->price,
            ]
        ]
    ];

    $order3 = $service->createOrder($orderData3);
    echo "✓ Order Created: {$order3->order_number}\n";
    echo "  - Purchase Type: {$order3->purchase_type}\n";
    echo "  - Payment Status: {$order3->payment_status}\n";
    echo "  - Order Status: {$order3->status}\n";
    
    if ($order3->status === 'pending') {
        echo "  ✅ PASS: Status is 'pending' (follows normal flow)\n";
    } else {
        echo "  ⚠️  WARN: Status is '{$order3->status}' (may be manually set)\n";
    }
    echo "\n";

    // Summary
    echo "Test Summary\n";
    echo "============\n";
    echo "Test 1 (In-Store + Paid): " . ($order1->status === 'delivered' ? '✅ PASS' : '❌ FAIL') . "\n";
    echo "Test 2 (In-Store + Pending): " . ($order2->status === 'pending' ? '✅ PASS' : '❌ FAIL') . "\n";
    echo "Test 3 (Online + Paid): " . ($order3->status === 'pending' ? '✅ PASS' : '⚠️  WARN') . "\n";
    echo "\n";

    echo "✓ All tests completed!\n";
    echo "\nCreated Order IDs: {$order1->order_id}, {$order2->order_id}, {$order3->order_id}\n";
    echo "You can view these orders in the sales orders list.\n";

} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}
