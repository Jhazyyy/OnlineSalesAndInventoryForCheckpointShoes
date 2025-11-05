<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Recent POS Orders ===\n\n";

$recentOrders = \App\Models\SalesOrder::where('purchase_type', 'in_store')
    ->orderBy('created_at', 'desc')
    ->limit(5)
    ->get();

foreach ($recentOrders as $order) {
    echo "Order ID: {$order->order_id}\n";
    echo "  Order Number: {$order->order_number}\n";
    echo "  Created: {$order->created_at}\n";
    echo "  Subtotal: {$order->subtotal}\n";
    echo "  Tax Rule ID: " . ($order->tax_rule_id ?? 'NULL') . "\n";
    echo "  Tax Amount: {$order->tax_amount}\n";
    echo "  Discount Rule ID: " . ($order->discount_rule_id ?? 'NULL') . "\n";
    echo "  Discount Amount: {$order->discount_amount}\n";
    echo "  Total: {$order->total_amount}\n";
    echo "\n";
}
