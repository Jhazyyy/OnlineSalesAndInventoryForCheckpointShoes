<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;
use App\Models\Customer;
use App\Services\SalesOrderService;
use Illuminate\Support\Facades\Log;

// Pick a product with stock
$product = Product::where('quantity', '>', 0)->first();
if (!$product) {
    echo "No product with stock found\n";
    exit(1);
}

$initialQty = $product->quantity;

// Create quick customer
$customer = Customer::create([
    'first_name' => 'POSTest',
    'last_name' => 'User',
    'phone' => '000',
    'email' => 'postest+' . time() . '@example.test',
    'customer_type' => 'individual',
    'status' => 'active',
]);

$service = new SalesOrderService();

$orderData = [
    'customer_id' => $customer->customer_id,
    'order_date' => date('Y-m-d'),
    'payment_method' => 'cash',
    'payment_status' => 'paid',
    'purchase_type' => 'in_store',
    'items' => [
        [
            'product_id' => $product->product_id,
            'quantity' => 2,
            'unit_price' => $product->price,
            'discount_amount' => 0,
        ],
    ],
    'notes' => 'POS test order'
];

try {
    $order = $service->createOrder($orderData);
    echo "Order created: {$order->order_number}\n";
} catch (Exception $e) {
    echo "Order creation failed: " . $e->getMessage() . "\n";
    exit(1);
}

$product->refresh();
echo "Product quantity before: {$initialQty}, after: {$product->quantity}\n";

// Cleanup: do not delete records to preserve history

