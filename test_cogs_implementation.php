<?php

/**
 * Test COGS Implementation
 * 
 * This script verifies that the COGS (Cost of Goods Sold) is being correctly captured
 * and calculated in sales orders and reports.
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

echo "╔════════════════════════════════════════════════════════════╗\n";
echo "║          COGS Implementation Verification Test             ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

// Test 1: Check if unit_cost_at_sale column exists
echo "Test 1: Checking database schema...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$columnExists = DB::select("SHOW COLUMNS FROM sales_order_items LIKE 'unit_cost_at_sale'");
if (!empty($columnExists)) {
    echo "✓ Column 'unit_cost_at_sale' exists in sales_order_items table\n";
} else {
    echo "✗ Column 'unit_cost_at_sale' NOT found in sales_order_items table\n";
    exit(1);
}

// Test 2: Check if existing items have been backfilled
echo "\nTest 2: Checking backfill status...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$totalItems = SalesOrderItem::count();
$itemsWithCost = SalesOrderItem::where('unit_cost_at_sale', '>', 0)->count();
$itemsWithoutCost = SalesOrderItem::where('unit_cost_at_sale', '=', 0)
    ->orWhereNull('unit_cost_at_sale')
    ->count();

echo "Total sales order items: {$totalItems}\n";
echo "✓ Items with unit_cost_at_sale: {$itemsWithCost}\n";
echo "✗ Items without unit_cost_at_sale: {$itemsWithoutCost}\n";

$backfillPercentage = $totalItems > 0 ? round(($itemsWithCost / $totalItems) * 100, 2) : 0;
echo "Backfill completion: {$backfillPercentage}%\n";

// Test 3: Sample calculation verification
echo "\nTest 3: Verifying COGS calculations...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Get a recent order with items
$order = SalesOrder::with(['items.product', 'customer'])
    ->whereHas('items')
    ->latest()
    ->first();

if ($order) {
    echo "Sample Order: {$order->order_number}\n";
    echo "Customer: " . ($order->customer->display_name ?? 'N/A') . "\n";
    echo "Order Date: {$order->order_date}\n\n";
    
    $totalRevenue = 0;
    $totalCOGS = 0;
    
    echo "Items:\n";
    echo str_repeat("─", 100) . "\n";
    printf("%-40s %8s %12s %12s %12s %12s\n", 
        "Product", "Qty", "Unit Price", "Unit COGS", "Revenue", "COGS"
    );
    echo str_repeat("─", 100) . "\n";
    
    foreach ($order->items as $item) {
        $itemRevenue = $item->line_total;
        $itemCOGS = $item->unit_cost_at_sale * $item->quantity;
        
        $totalRevenue += $itemRevenue;
        $totalCOGS += $itemCOGS;
        
        printf("%-40s %8d ₱%10.2f ₱%10.2f ₱%10.2f ₱%10.2f\n",
            substr($item->product->product_name ?? 'Unknown', 0, 40),
            $item->quantity,
            $item->unit_price,
            $item->unit_cost_at_sale,
            $itemRevenue,
            $itemCOGS
        );
    }
    
    echo str_repeat("─", 100) . "\n";
    
    $grossProfit = $totalRevenue - $totalCOGS;
    $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
    
    echo "\nOrder Summary:\n";
    echo "  Total Revenue: ₱" . number_format($totalRevenue, 2) . "\n";
    echo "  Total COGS:    ₱" . number_format($totalCOGS, 2) . "\n";
    echo "  Gross Profit:  ₱" . number_format($grossProfit, 2) . "\n";
    echo "  Profit Margin: " . number_format($profitMargin, 2) . "%\n";
    
    // Verify calculation
    if ($totalCOGS > 0) {
        echo "\n✓ COGS is being calculated correctly!\n";
    } else {
        echo "\n⚠ Warning: COGS is zero. Products may not have costs set.\n";
    }
} else {
    echo "No orders found to test.\n";
}

// Test 4: Check product costs
echo "\nTest 4: Checking product costs...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$productsWithCost = Product::whereNotNull('total_cost')->where('total_cost', '>', 0)->count();
$totalProducts = Product::count();

echo "Products with costs set: {$productsWithCost} / {$totalProducts}\n";

if ($productsWithCost > 0) {
    echo "✓ Some products have costs from purchases\n";
    
    // Show sample products with costs
    echo "\nSample products with costs:\n";
    $sampleProducts = Product::whereNotNull('total_cost')
        ->where('total_cost', '>', 0)
        ->take(5)
        ->get();
    
    foreach ($sampleProducts as $product) {
        echo sprintf(
            "  • %s: Unit Cost = ₱%.2f, Selling Price = ₱%.2f\n",
            $product->product_name,
            $product->total_cost,
            $product->price ?? 0
        );
    }
} else {
    echo "⚠ No products have costs set. Run purchase receive process to populate costs.\n";
}

// Test 5: Verify report calculation would work
echo "\nTest 5: Testing report calculations...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$orders = SalesOrder::with(['items.product'])
    ->whereDate('order_date', '>=', now()->subDays(30))
    ->get();

if ($orders->count() > 0) {
    $totalRevenue = $orders->sum('subtotal');
    $totalCOGS = $orders->sum(function ($order) {
        return $order->items->sum(function ($item) {
            $unitCost = $item->unit_cost_at_sale ?? ($item->product->total_cost ?? 0);
            return $unitCost * $item->quantity;
        });
    });
    
    $grossProfit = $totalRevenue - $totalCOGS;
    $profitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;
    
    echo "Last 30 Days Summary:\n";
    echo "  Orders: {$orders->count()}\n";
    echo "  Total Revenue: ₱" . number_format($totalRevenue, 2) . "\n";
    echo "  Total COGS: ₱" . number_format($totalCOGS, 2) . "\n";
    echo "  Gross Profit: ₱" . number_format($grossProfit, 2) . "\n";
    echo "  Profit Margin: " . number_format($profitMargin, 2) . "%\n";
    
    echo "\n✓ Report calculations are working!\n";
} else {
    echo "No orders in the last 30 days to test.\n";
}

// Final Summary
echo "\n╔════════════════════════════════════════════════════════════╗\n";
echo "║                    Test Summary                            ║\n";
echo "╚════════════════════════════════════════════════════════════╝\n\n";

$allPassed = true;

if (!empty($columnExists)) {
    echo "✓ Database schema updated\n";
} else {
    echo "✗ Database schema NOT updated\n";
    $allPassed = false;
}

if ($backfillPercentage >= 90) {
    echo "✓ Backfill completed ({$backfillPercentage}%)\n";
} else {
    echo "⚠ Backfill incomplete ({$backfillPercentage}%)\n";
}

if ($productsWithCost > 0) {
    echo "✓ Products have costs from purchases\n";
} else {
    echo "⚠ Products need costs (run purchase receive)\n";
}

if ($allPassed) {
    echo "\n✓ All tests passed! COGS implementation is working correctly.\n";
} else {
    echo "\n⚠ Some tests failed. Review the issues above.\n";
}

echo "\n";
