<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Supplier;
use App\Services\SupplierService;

echo "Testing Supplier Performance Metrics\n";
echo "=====================================\n\n";

$supplier = Supplier::first();

if (!$supplier) {
    echo "No suppliers found in database.\n";
    exit;
}

echo "Supplier: {$supplier->supplier_name}\n";
echo "Status: {$supplier->status}\n\n";

$supplierService = app(SupplierService::class);
$performance = $supplierService->calculateSupplierPerformance($supplier);

echo "Performance Metrics:\n";
echo "-------------------\n";
echo "Total Purchased: ₱" . number_format($performance['total_purchased'], 2) . "\n";
echo "Total Orders: {$performance['total_orders']}\n";
echo "Average Order Value: ₱" . number_format($performance['average_order_value'], 2) . "\n";
echo "Order Frequency: {$performance['order_frequency_per_month']}/month\n";
echo "Relationship Days: {$performance['relationship_days']}\n";
echo "First Order: " . ($performance['first_order_date'] ? $performance['first_order_date']->format('M d, Y') : 'N/A') . "\n";
echo "Last Order: " . ($performance['last_order_date'] ? $performance['last_order_date']->format('M d, Y') : 'N/A') . "\n";
echo "Recent Orders (90 days): {$performance['recent_orders_90_days']}\n";
echo "Recent Value (90 days): ₱" . number_format($performance['recent_value_90_days'], 2) . "\n";
echo "Performance Score: {$performance['performance_score']}/100\n";
echo "Performance Rating: {$performance['performance_rating']}\n";
echo "Is Reliable: " . ($performance['is_reliable'] ? 'Yes' : 'No') . "\n";

echo "\n✅ Performance metrics calculated successfully!\n";
