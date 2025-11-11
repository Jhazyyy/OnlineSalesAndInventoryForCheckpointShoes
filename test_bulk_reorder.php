<?php

/**
 * Test script to verify bulk reorder data structure
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate request data as it would come from the form
$testData = [
    'supplier_id' => 1,
    'products' => [10, 20, 30],
    'quantities' => [
        10 => 5,
        20 => 10,
        30 => 15,
    ]
];

echo "Test Data Structure:\n";
echo "===================\n\n";
echo "Supplier ID: " . $testData['supplier_id'] . "\n";
echo "Products: " . implode(', ', $testData['products']) . "\n";
echo "Quantities:\n";
foreach ($testData['quantities'] as $productId => $qty) {
    echo "  - Product $productId: $qty\n";
}

echo "\n\nValidation Test:\n";
echo "===================\n";

// Test if we can iterate properly
foreach ($testData['products'] as $productId) {
    if (!isset($testData['quantities'][$productId])) {
        echo "ERROR: No quantity for product $productId\n";
    } else {
        echo "OK: Product $productId has quantity " . $testData['quantities'][$productId] . "\n";
    }
}

echo "\n\nThis is the expected data format that JavaScript should send.\n";
echo "If the bulk order doesn't work, check:\n";
echo "1. Browser console for JavaScript errors\n";
echo "2. Laravel logs for validation errors\n";
echo "3. Network tab to see the actual POST data\n";
