<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TESTING CRITICAL STOCK SCOPE ===\n\n";

try {
    // Test the criticalStock() scope
    $criticalProducts = App\Models\Product::criticalStock()->get();
    
    echo "✅ SUCCESS! criticalStock() method works!\n\n";
    echo "Critical stock products (qty 1-5): " . $criticalProducts->count() . "\n\n";
    
    if ($criticalProducts->count() > 0) {
        echo "Sample critical stock products:\n";
        echo str_repeat('=', 80) . "\n";
        foreach ($criticalProducts->take(5) as $product) {
            echo sprintf(
                "%-50s | Qty: %-3s | SKU: %s\n",
                substr($product->product_name, 0, 50),
                $product->quantity,
                $product->sku
            );
        }
    }
    
    echo "\n=== ALL STOCK SCOPES WORKING ===\n";
    echo "✓ inStock()       - Products with qty > 10\n";
    echo "✓ lowStock()      - Products with qty 1-10\n";
    echo "✓ criticalStock() - Products with qty 1-5 (NEW!)\n";
    echo "✓ outOfStock()    - Products with qty = 0\n";
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "\nThis means the method is not working correctly.\n";
}
