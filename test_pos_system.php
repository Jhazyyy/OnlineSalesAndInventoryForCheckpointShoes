<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;

echo "=== POS System Test ===\n\n";

// Test 1: Check if products exist
echo "1. Checking products...\n";
$productCount = Product::where('quantity', '>', 0)->count();
echo "   Found {$productCount} products with stock\n";

if ($productCount > 0) {
    $sampleProduct = Product::where('quantity', '>', 0)->first();
    echo "   Sample Product: {$sampleProduct->product_name} (ID: {$sampleProduct->product_id}, Price: ₱{$sampleProduct->price}, Stock: {$sampleProduct->quantity})\n";
}
echo "\n";

// Test 2: Check if customers exist
echo "2. Checking customers...\n";
$customerCount = Customer::where('status', 'active')->count();
echo "   Found {$customerCount} active customers\n";

if ($customerCount > 0) {
    $sampleCustomer = Customer::where('status', 'active')->first();
    echo "   Sample Customer: {$sampleCustomer->first_name} {$sampleCustomer->last_name} (ID: {$sampleCustomer->customer_id})\n";
}
echo "\n";

// Test 3: Check sales_orders table structure
echo "3. Checking sales_orders table...\n";
$columns = DB::select("DESCRIBE sales_orders");
$columnNames = array_column($columns, 'Field');
echo "   Columns: " . implode(', ', $columnNames) . "\n";

$requiredColumns = ['purchase_type', 'payment_method', 'payment_status', 'customer_id', 'order_date'];
foreach ($requiredColumns as $col) {
    if (in_array($col, $columnNames)) {
        echo "   ✓ {$col} exists\n";
    } else {
        echo "   ✗ {$col} MISSING\n";
    }
}
echo "\n";

// Test 4: Check customers table structure
echo "4. Checking customers table...\n";
$customerColumns = DB::select("DESCRIBE customers");
$customerColumnNames = array_column($customerColumns, 'Field');
$requiredCustomerColumns = ['customer_id', 'first_name', 'last_name', 'phone', 'email', 'status', 'customer_type'];
foreach ($requiredCustomerColumns as $col) {
    if (in_array($col, $customerColumnNames)) {
        echo "   ✓ {$col} exists\n";
    } else {
        echo "   ✗ {$col} MISSING\n";
    }
}
echo "\n";

// Test 5: Check products table structure
echo "5. Checking products table...\n";
$productColumns = DB::select("DESCRIBE products");
$productColumnNames = array_column($productColumns, 'Field');
$requiredProductColumns = ['product_id', 'product_name', 'sku', 'price', 'quantity', 'image'];
foreach ($requiredProductColumns as $col) {
    if (in_array($col, $productColumnNames)) {
        echo "   ✓ {$col} exists\n";
    } else {
        echo "   ✗ {$col} MISSING\n";
    }
}
echo "\n";

echo "=== Test Complete ===\n";
echo "\nPOS System should be ready to use at: /pos/create\n";
