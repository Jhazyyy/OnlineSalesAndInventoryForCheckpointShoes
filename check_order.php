<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;

$order = SalesOrder::where('order_number', 'SO202512090002')->first();
if ($order) {
    $items = SalesOrderItem::where('order_id', $order->order_id)->with('product')->get();
    
    foreach ($items as $item) {
        echo "Item ID: {$item->item_id}\n";
        echo "Product: {$item->product->product_name}\n";
        echo "unit_cost_at_sale: {$item->unit_cost_at_sale}\n";
        echo "Product total_cost: " . ($item->product->total_cost ?? 'NULL') . "\n\n";
    }
}
