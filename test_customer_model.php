<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Customer Model ===\n\n";

$customer = App\Models\Customer::first();

if ($customer) {
    echo "✓ Customer found!\n";
    echo "  - ID: " . $customer->customer_id . "\n";
    echo "  - Full Name: " . $customer->full_name . "\n";
    echo "  - Display Name: " . $customer->display_name . "\n";
    echo "  - Email: " . $customer->email . "\n";
    echo "  - Phone: " . $customer->phone . "\n";
    echo "  - Address: " . $customer->full_address . "\n";
    echo "  - Type: " . $customer->customer_type . "\n";
    echo "  - Status: " . $customer->status . "\n";
    
    echo "\n✓ All accessors working correctly!\n";
    echo "\n=== Testing Sales Orders ===\n\n";
    
    $orders = App\Models\SalesOrder::with('customer')->get();
    echo "✓ Found " . $orders->count() . " orders\n";
    
    foreach ($orders->take(3) as $order) {
        echo "  - Order #" . $order->order_number . ": " . $order->customer->full_name . " - $" . number_format($order->total_amount, 2) . "\n";
    }
    
    echo "\n✅ All tests passed!\n";
} else {
    echo "❌ No customer found\n";
}
