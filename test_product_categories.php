<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

echo "=== ALL PRODUCTS BY CATEGORY ===\n\n";

// Get products by category
$categories = ['Shoes', 'Garments', 'Accessories'];

foreach ($categories as $category) {
    echo "--- $category ---\n";
    $products = Product::where('product_category', $category)->get();
    
    foreach ($products as $p) {
        echo sprintf(
            "%-35s | Brand: %-18s | Qty: %3d\n",
            substr($p->product_name, 0, 35),
            $p->product_brand ?? 'NULL',
            $p->quantity
        );
    }
    echo "\n";
}

echo "\n=== LOW STOCK PRODUCTS (Qty < 10) ===\n";
$lowStock = Product::where('quantity', '<', 10)->get();
foreach ($lowStock as $p) {
    echo sprintf(
        "%-35s | Brand: %-18s | Category: %-15s | Qty: %d\n",
        substr($p->product_name, 0, 35),
        $p->product_brand ?? 'NULL',
        $p->product_category ?? 'NULL',
        $p->quantity
    );
}

echo "\n=== SUMMARY ===\n";
echo "Total Products: " . Product::count() . "\n";
echo "Total Shoes: " . Product::where('product_category', 'Shoes')->count() . "\n";
echo "Total Garments: " . Product::where('product_category', 'Garments')->count() . "\n";
echo "Total Accessories: " . Product::where('product_category', 'Accessories')->count() . "\n";
echo "Out of Stock: " . Product::where('quantity', 0)->count() . "\n";
echo "Low Stock (<10): " . Product::where('quantity', '<', 10)->where('quantity', '>', 0)->count() . "\n";
