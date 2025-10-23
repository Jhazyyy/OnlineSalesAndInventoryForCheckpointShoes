<?php

/**
 * Quick test script for inventory settings integration
 * 
 * Run this in tinker: php artisan tinker
 * Then paste the contents of this file
 */

use App\Models\Product;
use App\Models\Setting;

// Display current inventory settings
echo "\n=== Current Inventory Settings ===\n";
echo "Low Stock Threshold: " . lowStockThreshold() . "\n";
echo "Critical Stock Level: " . criticalStockLevel() . "\n";
echo "Auto-Reorder Enabled: " . (isAutoReorderEnabled() ? 'Yes' : 'No') . "\n";
echo "Waste Tracking Enabled: " . (isWasteTrackingEnabled() ? 'Yes' : 'No') . "\n";
echo "Negative Stock Allowed: " . (isNegativeStockAllowed() ? 'Yes' : 'No') . "\n";

// Test with a sample product
echo "\n=== Testing with Sample Product ===\n";
$product = Product::first();

if ($product) {
    echo "Product: {$product->product_name}\n";
    echo "Current Quantity: {$product->quantity}\n";
    echo "Reorder Level: " . ($product->reorder_level ?? 'Not Set (using global: ' . lowStockThreshold() . ')') . "\n";
    echo "Critical Level: " . ($product->critical_level ?? 'Not Set (using global: ' . criticalStockLevel() . ')') . "\n";
    echo "\nStock Status:\n";
    echo "- Is Low Stock? " . ($product->isLowStock() ? 'Yes' : 'No') . "\n";
    echo "- Is Critical Stock? " . ($product->isCriticalStock() ? 'Yes' : 'No') . "\n";
    echo "- Needs Reordering? " . ($product->needsReordering() ? 'Yes' : 'No') . "\n";
    echo "- Should Auto-Reorder? " . ($product->shouldAutoReorder() ? 'Yes' : 'No') . "\n";
    echo "- Allows Negative Stock? " . ($product->allowsNegativeStock() ? 'Yes' : 'No') . "\n";
} else {
    echo "No products found in database.\n";
}

// Test setting a new value
echo "\n=== Testing Setting Updates ===\n";
echo "Current low stock threshold: " . lowStockThreshold() . "\n";

// To update, use the settings controller or service:
// Setting::updateOrCreate(
//     ['category' => 'inventory', 'key' => 'low_stock_threshold'],
//     ['value' => 15, 'data_type' => 'integer']
// );

echo "\nTest completed successfully!\n";
echo "To modify settings, visit: /settings/inventory\n";
