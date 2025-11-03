<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Services\PurchaseOrderService;

echo "Creating Test Purchase Orders for Supplier Performance\n";
echo "======================================================\n\n";

$supplier = Supplier::first();

if (!$supplier) {
    echo "❌ No suppliers found. Please seed suppliers first.\n";
    exit;
}

echo "Supplier: {$supplier->supplier_name}\n";
echo "Current Orders: " . $supplier->purchaseOrders()->count() . "\n\n";

// Get some products
$products = Product::take(5)->get();

if ($products->count() === 0) {
    echo "❌ No products found. Please seed products first.\n";
    exit;
}

echo "Creating purchase order with " . $products->count() . " products...\n";

// Prepare order items
$items = [];
foreach ($products as $product) {
    $items[] = [
        'product_id' => $product->product_id,
        'quantity_ordered' => rand(10, 50),
        'unit_price' => $product->price ?? rand(100, 1000),
        'tax_rate' => 12,
        'notes' => 'Test item for performance metrics'
    ];
}

// Create order data
$orderData = [
    'supplier_id' => $supplier->supplier_id,
    'order_date' => now()->format('Y-m-d'),
    'expected_delivery_date' => now()->addDays(7)->format('Y-m-d'),
    'status' => 'pending',
    'priority' => 'normal',
    'payment_terms' => 'Net 30',
    'shipping_address' => 'Test Warehouse Address',
    'shipping_method' => 'Standard Delivery',
    'notes' => 'Test order for performance metrics demonstration',
    'items' => $items
];

try {
    $purchaseOrderService = app(PurchaseOrderService::class);
    $order = $purchaseOrderService->createOrder($orderData);
    
    echo "✅ Purchase Order Created: {$order->order_number}\n";
    echo "   Total Amount: ₱" . number_format($order->total_amount, 2) . "\n";
    echo "   Items: " . $order->items()->count() . "\n";
    
    // Update performance
    $performance = app(App\Services\SupplierService::class)->calculateSupplierPerformance($supplier->fresh());
    
    echo "\n📊 Updated Performance:\n";
    echo "   Total Orders: {$performance['total_orders']}\n";
    echo "   Total Purchased: ₱" . number_format($performance['total_purchased'], 2) . "\n";
    echo "   Performance Score: {$performance['performance_score']}/100\n";
    echo "   Performance Rating: {$performance['performance_rating']}\n";
    
    echo "\n✅ Test data created successfully!\n";
    echo "\n💡 Visit the supplier profile to see the updated metrics:\n";
    echo "   http://onlinesalesandinventoryforcheckpoint.test/master_data/suppliers/{$supplier->supplier_id}\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
