<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$order = App\Models\SalesOrder::where('order_number', 'SO202511050011')->first();

if ($order) {
    echo "Order: {$order->order_number}\n";
    echo "Status: {$order->status}\n";
    echo "Payment Status: {$order->payment_status}\n";
    echo "Total: {$order->total_amount}\n";
    echo "Amount Received: " . ($order->amount_received ?? 'NULL') . "\n";
    echo "Purchase Type: {$order->purchase_type}\n";
    echo "Created At: {$order->created_at}\n";
} else {
    echo "Order not found\n";
}
