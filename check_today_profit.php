<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SalesOrder;

echo "TODAY'S PROFIT BREAKDOWN (" . today()->format('Y-m-d') . ")\n";
echo str_repeat('=', 70) . "\n\n";

$orders = SalesOrder::with('items.product')
    ->whereDate('order_date', today())
    ->get();

echo "Total Orders: " . $orders->count() . "\n\n";

// Calculate revenue
$grossRevenue = $orders->sum('subtotal');
$totalDiscount = $orders->sum('discount_amount');
$netRevenue = $grossRevenue - $totalDiscount;

echo "REVENUE CALCULATION:\n";
echo "  Gross Revenue (Subtotal): ₱" . number_format($grossRevenue, 2) . "\n";
echo "  Less: Discounts:          ₱" . number_format($totalDiscount, 2) . "\n";
echo "  Net Revenue:              ₱" . number_format($netRevenue, 2) . "\n\n";

// Calculate COGS
$totalCost = 0;
foreach ($orders as $order) {
    foreach ($order->items as $item) {
        $totalCost += ($item->product->total_cost ?? 0) * $item->quantity;
    }
}

echo "COST CALCULATION (COGS):\n";
echo "  Total Cost of Goods Sold: ₱" . number_format($totalCost, 2) . "\n\n";

// Calculate profit
$totalProfit = $netRevenue - $totalCost;

echo "PROFIT CALCULATION:\n";
echo "  Net Revenue:              ₱" . number_format($netRevenue, 2) . "\n";
echo "  Less: COGS:               ₱" . number_format($totalCost, 2) . "\n";
echo "  ──────────────────────────────────────\n";
echo "  Gross Profit:             ₱" . number_format($totalProfit, 2) . "\n\n";

echo str_repeat('=', 70) . "\n";
echo "DETAILED ORDER BREAKDOWN:\n";
echo str_repeat('=', 70) . "\n";

foreach ($orders as $order) {
    echo "\nOrder #" . $order->order_number . ":\n";
    echo "  Subtotal:  ₱" . number_format($order->subtotal, 2) . "\n";
    echo "  Discount:  ₱" . number_format($order->discount_amount, 2) . "\n";
    echo "  Net:       ₱" . number_format($order->subtotal - $order->discount_amount, 2) . "\n";
    
    $orderCost = 0;
    foreach ($order->items as $item) {
        $orderCost += ($item->product->total_cost ?? 0) * $item->quantity;
    }
    
    echo "  COGS:      ₱" . number_format($orderCost, 2) . "\n";
    echo "  Profit:    ₱" . number_format(($order->subtotal - $order->discount_amount) - $orderCost, 2) . "\n";
    
    echo "  Items:\n";
    foreach ($order->items as $item) {
        $itemCost = ($item->product->total_cost ?? 0) * $item->quantity;
        $itemRevenue = $item->unit_price * $item->quantity;
        $itemProfit = $itemRevenue - $itemCost;
        
        echo "    • " . $item->product->product_name . "\n";
        echo "      Qty: " . $item->quantity . " × ₱" . number_format($item->unit_price, 2) 
             . " = ₱" . number_format($itemRevenue, 2) . " (revenue)\n";
        echo "      Cost: " . $item->quantity . " × ₱" . number_format($item->product->total_cost ?? 0, 2) 
             . " = ₱" . number_format($itemCost, 2) . " (COGS)\n";
        echo "      Item Profit: ₱" . number_format($itemProfit, 2) . "\n";
    }
}

echo "\n" . str_repeat('=', 70) . "\n";
echo "FORMULA: Gross Profit = Net Revenue - COGS\n";
echo "         ₱" . number_format($totalProfit, 2) . " = ₱" . number_format($netRevenue, 2) 
     . " - ₱" . number_format($totalCost, 2) . "\n";
echo str_repeat('=', 70) . "\n";
