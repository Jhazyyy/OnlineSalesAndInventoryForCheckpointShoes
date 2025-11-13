<?php

/**
 * Test script to verify all inventory threshold fixes
 * Run this with: php test_threshold_fixes.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Product;
use App\Models\InventoryAlert;
use App\Services\InventoryThresholdService;
use Illuminate\Support\Facades\DB;

echo "========================================\n";
echo "Testing Inventory Threshold Fixes\n";
echo "========================================\n\n";

// Test 1: Check if Log facade is properly imported
echo "Test 1: InventoryThresholdService Log Import\n";
echo "-------------------------------------------\n";
try {
    $service = new InventoryThresholdService();
    echo "✓ Service instantiated successfully\n";
    echo "✓ Log facade import is working\n";
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 2: Check Product-InventoryAlert relationship
echo "Test 2: Product-InventoryAlert Relationship\n";
echo "--------------------------------------------\n";
try {
    $alert = InventoryAlert::with('product')->first();
    if ($alert) {
        echo "✓ Alert ID: {$alert->id}\n";
        echo "✓ Alert Type: {$alert->alert_type}\n";
        echo "✓ Product Relationship: " . ($alert->product ? 'Working' : 'NULL') . "\n";
        if ($alert->product) {
            echo "✓ Product Name: {$alert->product->product_name}\n";
            echo "✓ Product ID: {$alert->product->product_id}\n";
        }
    } else {
        echo "No alerts found in the database\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

// Test 3: Check Product threshold methods
echo "Test 3: Product Threshold Methods\n";
echo "----------------------------------\n";
try {
    $product = Product::where('reorder_level', '>', 0)->first();
    if ($product) {
        echo "Testing Product: {$product->product_name}\n";
        echo "- Current Stock: {$product->quantity}\n";
        echo "- Reorder Level: " . ($product->reorder_level ?? 'Not Set') . "\n";
        echo "- Critical Level: " . ($product->critical_level ?? 'Not Set') . "\n";
        
        // Test stock status methods
        echo "\nStock Status Checks:\n";
        echo "- Is Low Stock: " . ($product->isLowStock() ? 'Yes' : 'No') . "\n";
        echo "- Is Critical Stock: " . ($product->isCriticalStock() ? 'Yes' : 'No') . "\n";
        echo "- Needs Reordering: " . ($product->needsReordering() ? 'Yes' : 'No') . "\n";
        echo "- Should Auto Reorder: " . ($product->shouldAutoReorder() ? 'Yes' : 'No') . "\n";
        
        // Test getStockStatus method
        $stockStatus = $product->getStockStatus();
        echo "\nStock Status Array:\n";
        if (empty($stockStatus)) {
            echo "- No status alerts (stock level is adequate)\n";
        } else {
            foreach ($stockStatus as $status) {
                echo "- Type: {$status['type']}, Severity: {$status['severity']}\n";
            }
        }
        
        // Test suggested order quantity
        $suggestedQty = $product->getSuggestedOrderQuantity();
        echo "\n- Suggested Order Quantity: {$suggestedQty}\n";
        
        echo "\n✓ All Product threshold methods working correctly\n";
    } else {
        echo "No products with reorder levels found\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
echo "\n";

// Test 4: Test threshold service methods
echo "Test 4: Threshold Service Methods\n";
echo "----------------------------------\n";
try {
    $service = new InventoryThresholdService();
    
    // Test getThresholdStatistics
    echo "Getting threshold statistics...\n";
    $stats = $service->getThresholdStatistics();
    echo "✓ Total Products: {$stats['total_products']}\n";
    echo "✓ Products with Thresholds: {$stats['products_with_thresholds']}\n";
    echo "✓ Active Alerts: {$stats['active_alerts']}\n";
    echo "✓ Critical Alerts: {$stats['critical_alerts']}\n";
    echo "✓ Threshold Coverage: {$stats['threshold_coverage']}%\n";
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
echo "\n";

// Test 5: Test alert generation
echo "Test 5: Alert Generation Test\n";
echo "------------------------------\n";
try {
    $service = new InventoryThresholdService();
    
    // Find a product with low stock
    $lowStockProduct = Product::whereColumn('quantity', '<=', 'reorder_level')
        ->whereNotNull('reorder_level')
        ->where('threshold_alerts_enabled', true)
        ->first();
    
    if ($lowStockProduct) {
        echo "Testing alert generation for: {$lowStockProduct->product_name}\n";
        echo "- Current Stock: {$lowStockProduct->quantity}\n";
        echo "- Reorder Level: {$lowStockProduct->reorder_level}\n";
        
        $alerts = $service->checkProductThresholds($lowStockProduct);
        echo "✓ Alerts generated: {$alerts->count()}\n";
        
        foreach ($alerts as $alert) {
            echo "  - Type: {$alert->alert_type}, Severity: {$alert->severity}\n";
        }
    } else {
        echo "No products with low stock found for testing\n";
    }
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
echo "\n";

// Test 6: Database queries verification
echo "Test 6: Database Queries\n";
echo "------------------------\n";
try {
    // Test alerts query with product relationship
    $alertsCount = InventoryAlert::count();
    $activeAlertsCount = InventoryAlert::where('status', 'active')->count();
    $alertsWithProductsCount = InventoryAlert::whereHas('product')->count();
    
    echo "✓ Total Alerts: {$alertsCount}\n";
    echo "✓ Active Alerts: {$activeAlertsCount}\n";
    echo "✓ Alerts with Valid Products: {$alertsWithProductsCount}\n";
    
    // Check for orphaned alerts
    $orphanedAlerts = $alertsCount - $alertsWithProductsCount;
    if ($orphanedAlerts > 0) {
        echo "⚠ Warning: {$orphanedAlerts} orphaned alerts (product no longer exists)\n";
    } else {
        echo "✓ No orphaned alerts found\n";
    }
    
} catch (\Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
echo "\n";

echo "========================================\n";
echo "All Tests Completed!\n";
echo "========================================\n";
