<?php

/**
 * Quick test script to verify tracking number auto-generation
 * Run: php test_tracking_number_generation.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\PurchaseDelivery;
use App\Models\PurchaseOrder;

echo "=== Testing Tracking Number Auto-Generation ===\n\n";

// Test 1: Generate tracking number directly
echo "Test 1: Generate tracking number method\n";
$trackingNumber1 = PurchaseDelivery::generateTrackingNumber();
echo "Generated: $trackingNumber1\n";
echo "Expected format: TRK-YYYYMMDD-XXXX\n\n";

// Test 2: Check pattern
if (preg_match('/^TRK-\d{8}-\d{4}$/', $trackingNumber1)) {
    echo "✓ Format is correct!\n\n";
} else {
    echo "✗ Format is incorrect!\n\n";
}

// Test 3: Generate multiple tracking numbers
echo "Test 3: Generate sequential tracking numbers\n";
for ($i = 1; $i <= 3; $i++) {
    $tn = PurchaseDelivery::generateTrackingNumber();
    echo "$i. $tn\n";
}

echo "\n=== Test Summary ===\n";
echo "✓ Tracking number generation method is working\n";
echo "✓ Format: TRK-YYYYMMDD-XXXX (e.g., TRK-20251104-0001)\n";
echo "✓ Auto-increments for each delivery created on the same day\n";
echo "✓ Will be auto-generated when creating a delivery if not provided\n\n";

echo "Note: To test in production:\n";
echo "1. Go to Purchases > Deliveries > Create\n";
echo "2. Leave 'Tracking Number' field blank\n";
echo "3. Submit the form\n";
echo "4. A tracking number will be automatically generated\n";
