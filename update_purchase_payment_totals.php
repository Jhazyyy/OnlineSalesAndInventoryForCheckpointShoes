<?php

/**
 * Update Existing Purchase Orders with Payment Totals
 * 
 * This script updates the paid_amount field for all existing purchase orders
 * based on their completed payments.
 * 
 * Run this once after applying the migration to populate historical data.
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PurchaseOrder;

echo "Starting update of purchase order payment amounts...\n\n";

$orders = PurchaseOrder::with('payments')->get();
$updated = 0;
$skipped = 0;

foreach ($orders as $order) {
    echo "Processing Order: {$order->order_number}\n";
    echo "  Current paid_amount: ₱" . number_format($order->paid_amount ?? 0, 2) . "\n";
    
    // Update using the model method
    $order->updatePaidAmount();
    
    echo "  Updated paid_amount: ₱" . number_format($order->paid_amount, 2) . "\n";
    echo "  Payment status: {$order->payment_status}\n";
    echo "  Completed payments count: " . $order->payments()->where('status', 'completed')->count() . "\n";
    echo "  ---\n";
    
    $updated++;
}

echo "\n✓ Update completed!\n";
echo "  Total orders processed: {$orders->count()}\n";
echo "  Orders updated: {$updated}\n";
echo "\nAll purchase orders now have accurate payment tracking.\n";
