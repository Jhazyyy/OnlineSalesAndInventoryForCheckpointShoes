<?php
/**
 * Demo Script: Add Sample Costing Data to Products
 * Run: php demo_product_costing.php
 * 
 * This script adds sample costing data to your existing products
 * so you can immediately see the Product Costing Module in action.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Services\ProductCostingService;

echo "========================================\n";
echo "Product Costing Module - Demo Setup\n";
echo "========================================\n\n";

$costingService = new ProductCostingService();

// Get all products
$products = Product::all();

if ($products->isEmpty()) {
    echo "⚠ No products found. Please add products first.\n";
    exit(1);
}

echo "Found {$products->count()} product(s). Adding sample costing data...\n\n";

// Sample costing scenarios
$costingScenarios = [
    // Good profit margin
    [
        'raw_material_cost' => 500.00,
        'labor_cost' => 150.00,
        'overhead_cost' => 100.00,
        'shipping_cost_per_unit' => 50.00,
        'tax_amount_per_unit' => 30.00,
        'handling_cost' => 20.00,
        'price' => 1200.00,
        'cost_notes' => 'Good profit margin - 41% (Demo data)',
    ],
    // Low profit margin
    [
        'raw_material_cost' => 700.00,
        'labor_cost' => 200.00,
        'overhead_cost' => 150.00,
        'shipping_cost_per_unit' => 80.00,
        'tax_amount_per_unit' => 50.00,
        'handling_cost' => 30.00,
        'price' => 1500.00,
        'cost_notes' => 'Low profit margin - 22% (Demo data)',
    ],
    // Negative profit margin (losing money)
    [
        'raw_material_cost' => 800.00,
        'labor_cost' => 250.00,
        'overhead_cost' => 200.00,
        'shipping_cost_per_unit' => 100.00,
        'tax_amount_per_unit' => 60.00,
        'handling_cost' => 40.00,
        'price' => 1300.00,
        'cost_notes' => 'Negative profit margin - losing money! (Demo data)',
    ],
];

foreach ($products as $index => $product) {
    $scenario = $costingScenarios[$index % count($costingScenarios)];
    
    echo "Processing Product {$product->product_id}: {$product->product_name}\n";
    
    try {
        // Update the product with costing data
        $updatedProduct = $costingService->updateProductCosting($product, $scenario);
        
        echo "✓ Costing data added successfully:\n";
        echo "  - Total Cost: ₱" . number_format($updatedProduct->total_cost ?? 0, 2) . "\n";
        echo "  - Selling Price: ₱" . number_format($updatedProduct->price ?? 0, 2) . "\n";
        echo "  - Profit: ₱" . number_format($updatedProduct->profit_amount ?? 0, 2) . "\n";
        echo "  - Margin: " . number_format($updatedProduct->profit_margin ?? 0, 2) . "%\n";
        
        if ($updatedProduct->profit_margin < 0) {
            echo "  ⚠ WARNING: Negative margin - losing money!\n";
        } elseif ($updatedProduct->profit_margin < 20) {
            echo "  ⚠ WARNING: Low margin - below 20%\n";
        } else {
            echo "  ✓ Healthy profit margin\n";
        }
        
        echo "\n";
    } catch (Exception $e) {
        echo "✗ Error: " . $e->getMessage() . "\n\n";
    }
}

echo "========================================\n";
echo "Demo Setup Complete!\n";
echo "========================================\n\n";

// Show summary
echo "Summary:\n";
$stats = $costingService->getCostingStatistics();
echo "- Total Products: {$stats['total_products']}\n";
echo "- Products with Costing: {$stats['products_with_costing']}\n";
echo "- Completion: {$stats['costing_completion_percentage']}%\n";
echo "- Average Profit Margin: " . number_format($stats['average_profit_margin'], 2) . "%\n";
echo "- Low Margin Products: {$stats['low_margin_products']}\n";
echo "- Negative Margin Products: {$stats['negative_margin_products']}\n\n";

echo "Now you can:\n";
echo "1. Visit: /inventory/product-costing\n";
echo "2. View the dashboard with statistics\n";
echo "3. See products categorized by margin\n";
echo "4. Edit product costing\n";
echo "5. View low margin products at: /inventory/product-costing/low-margin\n";
echo "6. View negative margin products at: /inventory/product-costing/negative-margin\n\n";

echo "Enjoy exploring the Product Costing Module! 🎉\n\n";
