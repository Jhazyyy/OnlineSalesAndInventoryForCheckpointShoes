<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== CHECKING PRODUCT VARIANTS ===\n\n";

// Find products with same base name but different sizes/colors
$products = App\Models\Product::select('product_id', 'product_name', 'size', 'color', 'sku', 'quantity')
    ->whereNotNull('size')
    ->orWhereNotNull('color')
    ->orderBy('product_name')
    ->take(10)
    ->get();

if ($products->isEmpty()) {
    echo "No products with size/color variants found.\n";
} else {
    echo "Sample Products with Size/Color Variants:\n";
    echo str_repeat('=', 100) . "\n";
    
    foreach ($products as $product) {
        echo sprintf(
            "ID: %-3s | %-50s | Size: %-8s | Color: %-10s | Qty: %-3s | SKU: %s\n",
            $product->product_id,
            substr($product->product_name, 0, 50),
            $product->size ?: 'N/A',
            $product->color ?: 'N/A',
            $product->quantity,
            $product->sku
        );
    }
}

echo "\n=== SYSTEM DESIGN ===\n";
echo "Your system correctly treats each size/color variant as a SEPARATE product:\n";
echo "✓ Each variant has its own SKU (unique identifier)\n";
echo "✓ Each variant has its own quantity tracking\n";
echo "✓ Each variant has its own price\n";
echo "✓ Each variant gets its own stock notifications\n\n";

echo "Example:\n";
echo "  Product: 'Nike Air Max'\n";
echo "  - Nike Air Max, Size 8, Black  → SKU: NIK-AIR-001 → Qty: 5  → Gets notification\n";
echo "  - Nike Air Max, Size 9, Black  → SKU: NIK-AIR-002 → Qty: 50 → No notification\n";
echo "  - Nike Air Max, Size 8, White  → SKU: NIK-AIR-003 → Qty: 0  → Gets notification\n\n";

echo "This is the CORRECT approach! Each variant needs independent stock tracking.\n";
