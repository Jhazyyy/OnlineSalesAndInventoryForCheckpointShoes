<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\ProductVariant;

echo "=== Testing Product Variant System ===\n\n";

// Step 1: Create Parent Product
echo "1. Creating parent product...\n";
$product = Product::create([
    'product_name' => 'LEATHER CHELSEA BOOTS SLIP ON',
    'sku' => 'BOOTS-DEMO-' . rand(100, 999),
    'price' => 5000.00,
    'product_brand' => 'Premium Footwear',
    'product_category' => 'Boots',
    'description' => 'Premium leather chelsea boots with slip-on design',
    'is_parent' => true,
    'has_variants' => true,
]);

echo "   ✓ Parent Product Created\n";
echo "   - Name: {$product->product_name}\n";
echo "   - SKU: {$product->sku}\n";
echo "   - Base Price: ₱" . number_format($product->price, 2) . "\n\n";

// Step 2: Create Variants
echo "2. Creating product variants...\n";

$variants = [
    [
        'variant_sku' => 'BOOTS-DEMO-001-BLK-7-NAPPA',
        'variant_name' => 'Black Size 7',
        'color' => 'Black',
        'size' => '7',
        'material' => 'Black Nappa',
        'quantity' => 25,
        'price_adjustment' => 0,
    ],
    [
        'variant_sku' => 'BOOTS-DEMO-001-TAN-7-NAPPA',
        'variant_name' => 'Tan Size 7',
        'color' => 'Tan',
        'size' => '7',
        'material' => 'Tan Nappa',
        'quantity' => 20,
        'price_adjustment' => 200.00,
    ],
    [
        'variant_sku' => 'BOOTS-DEMO-001-BLK-8-NAPPA',
        'variant_name' => 'Black Size 8',
        'color' => 'Black',
        'size' => '8',
        'material' => 'Black Nappa',
        'quantity' => 30,
        'price_adjustment' => 0,
    ],
    [
        'variant_sku' => 'BOOTS-DEMO-001-TAN-8-NAPPA',
        'variant_name' => 'Tan Size 8',
        'color' => 'Tan',
        'size' => '8',
        'material' => 'Tan Nappa',
        'quantity' => 15,
        'price_adjustment' => 200.00,
    ],
];

foreach ($variants as $variantData) {
    $variant = $product->createVariant($variantData);
    echo "   ✓ Variant Created: {$variant->display_name}\n";
    echo "     - SKU: {$variant->variant_sku}\n";
    echo "     - Price: ₱" . number_format($variant->full_price, 2) . "\n";
    echo "     - Stock: {$variant->quantity} units\n";
}

echo "\n3. Testing relationships and methods...\n";

// Reload product to get variants
$product = Product::with('variants')->find($product->product_id);

echo "   ✓ Product has " . $product->variants->count() . " variants\n";
echo "   ✓ Total stock across all variants: {$product->total_variant_quantity} units\n";

$options = $product->variant_options;
echo "   ✓ Available colors: " . implode(', ', $options['colors']) . "\n";
echo "   ✓ Available sizes: " . implode(', ', $options['sizes']) . "\n";
echo "   ✓ Available materials: " . implode(', ', $options['materials']) . "\n";

echo "\n4. Testing queries...\n";

// Query active variants
$activeCount = $product->activeVariants->count();
echo "   ✓ Active variants: {$activeCount}\n";

// Query by color
$blackVariants = ProductVariant::where('parent_product_id', $product->product_id)
    ->byColor('Black')
    ->get();
echo "   ✓ Black color variants: {$blackVariants->count()}\n";

// Query by size
$size7Variants = ProductVariant::where('parent_product_id', $product->product_id)
    ->bySize('7')
    ->get();
echo "   ✓ Size 7 variants: {$size7Variants->count()}\n";

echo "\n5. Testing variant details...\n";

$firstVariant = $product->variants->first();
echo "   Variant: {$firstVariant->variant_name}\n";
echo "   - Parent Product: {$firstVariant->parentProduct->product_name}\n";
echo "   - Full Price: ₱" . number_format($firstVariant->full_price, 2) . "\n";
echo "   - Display Name: {$firstVariant->display_name}\n";
echo "   - Is Low Stock: " . ($firstVariant->isLowStock() ? 'Yes' : 'No') . "\n";
echo "   - Is Critical Stock: " . ($firstVariant->isCriticalStock() ? 'Yes' : 'No') . "\n";

echo "\n=== Test Completed Successfully! ✓ ===\n";
echo "\nYou can now use this variant system in your application!\n";
echo "Check PRODUCT_VARIANT_SYSTEM.md for full documentation.\n";
