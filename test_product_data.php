<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

// Get first product
$product = Product::first();

if ($product) {
    echo "=== FIRST PRODUCT DATA ===\n";
    echo "Product Name: " . $product->product_name . "\n";
    echo "SKU: " . $product->sku . "\n";
    echo "Brand: " . ($product->product_brand ?? 'NULL') . "\n";
    echo "Category: " . ($product->product_category ?? 'NULL') . "\n";
    echo "Quantity (DB): " . $product->quantity . "\n";
    echo "Price: " . $product->price . "\n";
    
    // Check stock movements
    $movementsCount = $product->stockMovements()->count();
    echo "Stock Movements Count: " . $movementsCount . "\n";
    
    if ($movementsCount > 0) {
        $latest = $product->stockMovements()->latest()->first();
        echo "Latest Movement Type: " . $latest->movement_type . "\n";
        echo "Latest Movement Quantity After: " . $latest->quantity_after . "\n";
    }
    
    echo "\n=== SAMPLE OF PRODUCTS ===\n";
    $products = Product::take(5)->get();
    foreach ($products as $p) {
        echo sprintf(
            "%-30s | Brand: %-15s | Category: %-15s | Qty: %d\n",
            $p->product_name,
            $p->product_brand ?? 'NULL',
            $p->product_category ?? 'NULL',
            $p->quantity
        );
    }
} else {
    echo "No products found in database.\n";
}
