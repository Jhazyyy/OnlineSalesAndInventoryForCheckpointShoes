<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$order = App\Models\SalesOrder::where('order_number', 'SO202511050011')->first();

if ($order) {
    echo "Before update:\n";
    echo "Status: {$order->status}\n";
    echo "Payment Status: {$order->payment_status}\n\n";
    
    // Update status to delivered since payment is complete
    $order->status = 'delivered';
    $order->save();
    
    echo "After update:\n";
    echo "Status: {$order->status}\n";
    echo "Payment Status: {$order->payment_status}\n\n";
    
    echo "Order status updated successfully!\n";
} else {
    echo "Order not found\n";
}
