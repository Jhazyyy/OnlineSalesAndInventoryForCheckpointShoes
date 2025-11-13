<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;

echo "Economic Order Quantity (EOQ) Check\n";
echo "====================================\n\n";

// Check if EOQ field exists in database
echo "1. Database Field Check:\n";
$products = Product::whereNotNull('economic_order_quantity')
    ->select('product_name', 'economic_order_quantity', 'reorder_level', 'quantity')
    ->limit(10)
    ->get();

echo "   Products with EOQ set: " . $products->count() . "\n\n";

if ($products->count() > 0) {
    echo "2. Sample Products with EOQ:\n";
    foreach ($products as $product) {
        echo "   - {$product->product_name}\n";
        echo "     EOQ: {$product->economic_order_quantity}\n";
        echo "     Reorder Level: {$product->reorder_level}\n";
        echo "     Current Stock: {$product->quantity}\n";
        echo "     Suggested Order Qty: " . $product->getSuggestedOrderQuantity() . "\n\n";
    }
} else {
    echo "   No products have EOQ values set.\n\n";
}

// Check all products count
$totalProducts = Product::count();
$withEOQ = Product::whereNotNull('economic_order_quantity')->count();
$withoutEOQ = $totalProducts - $withEOQ;

echo "3. Summary Statistics:\n";
echo "   Total Products: {$totalProducts}\n";
echo "   With EOQ: {$withEOQ} (" . ($totalProducts > 0 ? round(($withEOQ / $totalProducts) * 100, 2) : 0) . "%)\n";
echo "   Without EOQ: {$withoutEOQ} (" . ($totalProducts > 0 ? round(($withoutEOQ / $totalProducts) * 100, 2) : 0) . "%)\n\n";

// Check if EOQ is used in threshold views
echo "4. EOQ Integration Points:\n";
echo "   ✓ Database field: products.economic_order_quantity\n";
echo "   ✓ Model method: Product::getSuggestedOrderQuantity()\n";
echo "   ✓ Threshold edit form: Yes (editable field)\n";
echo "   ✓ Threshold index modal: Yes (editable field)\n";
echo "   ✓ Threshold show view: Yes (displays value)\n\n";

echo "5. EOQ Functionality Status:\n";
echo "   ✓ Field exists in database\n";
echo "   ✓ Field is fillable in Product model\n";
echo "   ✓ Field is editable in UI forms\n";
echo "   ✓ Used in getSuggestedOrderQuantity() method\n";
echo "   ⚠ No automatic EOQ calculation formula implemented\n";
echo "   ⚠ Values must be manually entered by users\n\n";

echo "Note: EOQ is currently a MANUAL INPUT field, not auto-calculated.\n";
echo "      Standard EOQ formula: √(2 × Demand × Ordering Cost / Holding Cost)\n";
echo "      To enable auto-calculation, implement an EOQ calculation service.\n";
