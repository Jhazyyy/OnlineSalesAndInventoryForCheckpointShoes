<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\SalesOrder;

echo "CHECKING PRODUCTS SOLD TODAY\n";
echo str_repeat('=', 70) . "\n\n";

// Get today's orders
$orders = SalesOrder::with('items.product')
    ->whereDate('order_date', today())
    ->get();

$productsNeedingCost = [];

foreach ($orders as $order) {
    foreach ($order->items as $item) {
        $product = $item->product;
        if (($product->total_cost ?? 0) == 0) {
            $productsNeedingCost[$product->product_id] = [
                'id' => $product->product_id,
                'name' => $product->product_name,
                'sku' => $product->sku,
                'current_cost' => $product->total_cost ?? 0,
                'price' => $product->price,
            ];
        }
    }
}

if (empty($productsNeedingCost)) {
    echo "All products have cost data!\n";
    exit;
}

echo "Products without cost data:\n\n";
foreach ($productsNeedingCost as $prod) {
    echo "Product ID: {$prod['id']}\n";
    echo "Name: {$prod['name']}\n";
    echo "SKU: {$prod['sku']}\n";
    echo "Selling Price: ₱" . number_format($prod['price'], 2) . "\n";
    echo "Current Cost: ₱" . number_format($prod['current_cost'], 2) . "\n";
    echo str_repeat('-', 70) . "\n";
}

echo "\n\nDo you want to set default costs for these products?\n";
echo "This will set cost = 60% of selling price (40% profit margin)\n\n";
echo "Type 'yes' to proceed: ";

$handle = fopen("php://stdin", "r");
$line = trim(fgets($handle));

if (strtolower($line) !== 'yes') {
    echo "Operation cancelled.\n";
    exit;
}

echo "\nUpdating product costs...\n\n";

foreach ($productsNeedingCost as $prod) {
    $product = Product::find($prod['id']);
    if ($product) {
        // Set cost as 60% of selling price (40% profit margin)
        $estimatedCost = $product->price * 0.6;
        
        $product->total_cost = $estimatedCost;
        $product->save();
        
        echo "✓ Updated {$product->product_name}\n";
        echo "  Cost set to: ₱" . number_format($estimatedCost, 2) . "\n\n";
    }
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "Products updated successfully!\n";
echo "Run 'php check_today_profit.php' to see the corrected profit.\n";
echo str_repeat('=', 70) . "\n";
