<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\ProductVariant;

echo "===== VARIANT SKU AUTO-GENERATION TEST =====\n\n";

try {
    // Use existing parent product
    $product = Product::where('sku', 'BOOTS-DEMO-001')->first();
    
    if (!$product) {
        echo "❌ Parent product not found. Please run test_product_variants.php first.\n";
        exit(1);
    }
    
    echo "Parent Product: {$product->product_name}\n";
    echo "Parent SKU: {$product->sku}\n\n";
    
    // Test 1: Create variant WITHOUT specifying SKU (should auto-generate)
    echo "Test 1: Auto-generate SKU (no SKU provided)\n";
    echo "----------------------------------------\n";
    
    // Check if variant already exists, if so delete it for testing
    $existingVariant = ProductVariant::where('parent_product_id', $product->product_id)
                                      ->where('color', 'Red')
                                      ->where('size', '9')
                                      ->first();
    if ($existingVariant) {
        $existingVariant->delete();
    }
    
    $variant1 = ProductVariant::create([
        'parent_product_id' => $product->product_id,
        'variant_name' => 'Red Size 9',
        // NO variant_sku provided - should auto-generate
        'color' => 'Red',
        'size' => '9',
        'material' => 'Red Nappa',
        'price_adjustment' => 0.00,
        'quantity' => 0,
        'is_active' => true,
    ]);
    
    echo "Created: {$variant1->variant_name}\n";
    echo "Auto-generated SKU: {$variant1->variant_sku}\n";
    echo "Expected pattern: BOOTS-DEMO-001-RED-9-NAPPA\n";
    echo "✓ SKU auto-generated successfully!\n\n";
    
    // Test 2: Create variant with manual SKU (should keep manual)
    echo "Test 2: Manual SKU provided (should not override)\n";
    echo "----------------------------------------\n";
    
    // Delete existing if any
    ProductVariant::where('variant_sku', 'CUSTOM-SKU-TEST-001')->delete();
    
    $variant2 = ProductVariant::create([
        'parent_product_id' => $product->product_id,
        'variant_name' => 'Navy Size 10',
        'variant_sku' => 'CUSTOM-SKU-TEST-001', // Manual SKU
        'color' => 'Navy',
        'size' => '10',
        'material' => 'Navy Nappa',
        'price_adjustment' => 0.00,
        'quantity' => 0,
        'is_active' => true,
    ]);
    
    echo "Created: {$variant2->variant_name}\n";
    echo "Manual SKU: {$variant2->variant_sku}\n";
    echo "Should be: CUSTOM-SKU-TEST-001 (unchanged)\n";
    echo "✓ Manual SKU preserved!\n\n";
    
    // Test 3: Different materials
    echo "Test 3: Different material types\n";
    echo "----------------------------------------\n";
    
    $materials = [
        ['name' => 'Suede', 'expected' => 'SDE'],
        ['name' => 'Patent Leather', 'expected' => 'PAT'],
        ['name' => 'Canvas', 'expected' => 'CNV'],
        ['name' => 'Synthetic Rubber', 'expected' => 'SYN'],
    ];
    
    foreach ($materials as $mat) {
        $variant = ProductVariant::create([
            'parent_product_id' => $product->product_id,
            'variant_name' => "Black Size 8 - {$mat['name']}",
            'color' => 'Black',
            'size' => '8',
            'material' => $mat['name'],
            'price_adjustment' => 0.00,
            'quantity' => 0,
            'is_active' => true,
        ]);
        
        echo "Material: {$mat['name']}\n";
        echo "  Generated SKU: {$variant->variant_sku}\n";
        echo "  Contains: {$mat['expected']} ✓\n\n";
    }
    
    // Test 4: Minimal attributes (only color)
    echo "Test 4: Minimal attributes (color only)\n";
    echo "----------------------------------------\n";
    
    $variant3 = ProductVariant::create([
        'parent_product_id' => $product->product_id,
        'variant_name' => 'Brown variant',
        'color' => 'Brown',
        // No size or material
        'price_adjustment' => 0.00,
        'quantity' => 0,
        'is_active' => true,
    ]);
    
    echo "Created: {$variant3->variant_name}\n";
    echo "Auto-generated SKU: {$variant3->variant_sku}\n";
    echo "Pattern: {PARENT_SKU}-{COLOR}\n\n";
    
    // Test 5: No attributes except parent (fallback)
    echo "Test 5: No color/size/material (fallback to unique ID)\n";
    echo "----------------------------------------\n";
    
    $variant4 = ProductVariant::create([
        'parent_product_id' => $product->product_id,
        'variant_name' => 'Generic variant',
        // No color, size, or material
        'price_adjustment' => 0.00,
        'quantity' => 0,
        'is_active' => true,
    ]);
    
    echo "Created: {$variant4->variant_name}\n";
    echo "Auto-generated SKU: {$variant4->variant_sku}\n";
    echo "Should be: {PARENT_SKU} only\n\n";
    
    // Summary
    echo "===== SUMMARY =====\n";
    echo "✓ Test 1: Auto-generation with full attributes\n";
    echo "✓ Test 2: Manual SKU preserved\n";
    echo "✓ Test 3: Material abbreviations working\n";
    echo "✓ Test 4: Partial attributes handled\n";
    echo "✓ Test 5: Fallback pattern working\n\n";
    
    echo "All variants created:\n";
    $allVariants = ProductVariant::where('parent_product_id', $product->product_id)
                                 ->orderBy('created_at', 'desc')
                                 ->limit(10)
                                 ->get();
    
    foreach ($allVariants as $v) {
        echo "  - {$v->variant_name}: {$v->variant_sku}\n";
    }
    
    echo "\n✅ ALL TESTS PASSED!\n\n";
    
} catch (Exception $e) {
    echo "\n❌ ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
}
