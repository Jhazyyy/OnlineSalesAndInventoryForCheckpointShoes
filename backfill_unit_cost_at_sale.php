<?php

/**
 * Backfill unit_cost_at_sale for existing sales order items
 * 
 * This script updates existing sales_order_items records to populate the unit_cost_at_sale field
 * with the product's current total_cost (from purchases). This ensures historical sales data
 * can calculate accurate COGS.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SalesOrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "Starting backfill of unit_cost_at_sale for existing sales order items...\n\n";

// Get all sales order items that don't have unit_cost_at_sale set
$items = SalesOrderItem::where('unit_cost_at_sale', 0)
    ->orWhereNull('unit_cost_at_sale')
    ->with('product')
    ->get();

$totalItems = $items->count();
$updated = 0;
$skipped = 0;

echo "Found {$totalItems} items to process.\n\n";

foreach ($items as $item) {
    if (!$item->product) {
        echo "⚠ Item {$item->item_id}: Product not found (product_id: {$item->product_id})\n";
        $skipped++;
        continue;
    }

    $totalCost = $item->product->total_cost ?? 0;
    
    // Update the item
    $item->update(['unit_cost_at_sale' => $totalCost]);
    
    $updated++;
    
    if ($updated % 50 == 0) {
        echo "Processed {$updated} items...\n";
    }
}

echo "\n";
echo "Backfill completed!\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "Total items processed: {$totalItems}\n";
echo "✓ Updated: {$updated}\n";
echo "⚠ Skipped: {$skipped}\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Verify by checking a sample
echo "\nVerifying backfill...\n";
$sampleItems = SalesOrderItem::with('product')
    ->whereNotNull('unit_cost_at_sale')
    ->where('unit_cost_at_sale', '>', 0)
    ->take(5)
    ->get();

echo "\nSample of backfilled items:\n";
foreach ($sampleItems as $item) {
    echo sprintf(
        "Item ID: %d | Product: %s | Unit Cost: ₱%.2f | Quantity: %d | Total COGS: ₱%.2f\n",
        $item->item_id,
        $item->product->product_name ?? 'Unknown',
        $item->unit_cost_at_sale,
        $item->quantity,
        $item->unit_cost_at_sale * $item->quantity
    );
}

echo "\n✓ Backfill completed successfully!\n";
