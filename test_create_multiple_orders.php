<?php

/**
 * Create Multiple Test Orders Script
 * Simulates multiple orders from e-commerce with different statuses
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Customer;
use App\Models\Product;
use Carbon\Carbon;

try {
    echo "Creating multiple test orders from e-commerce...\n\n";
    
    // Get customer
    $customer = Customer::first();
    if (!$customer) {
        echo "❌ No customer found. Run test_create_sample_order.php first.\n";
        exit(1);
    }
    
    // Get products
    $products = Product::all();
    if ($products->isEmpty()) {
        echo "❌ No products found.\n";
        exit(1);
    }
    
    // Order statuses to test
    $statuses = [
        ['status' => 'pending', 'name' => 'Pending Order'],
        ['status' => 'confirmed', 'name' => 'Confirmed Order'],
        ['status' => 'processing', 'name' => 'Processing Order'],
        ['status' => 'shipped', 'name' => 'Shipped Order'],
        ['status' => 'delivered', 'name' => 'Delivered Order'],
    ];
    
    foreach ($statuses as $statusInfo) {
        $orderNumber = 'ECOM-' . now()->format('Ymd') . '-' . str_pad(rand(100, 999), 3, '0', STR_PAD_LEFT);
        
        // Create order
        $order = SalesOrder::create([
            'order_number' => $orderNumber,
            'customer_id' => $customer->customer_id,
            'order_date' => Carbon::today()->subDays(rand(0, 7)),
            'required_date' => Carbon::today()->addDays(7),
            'status' => $statusInfo['status'],
            'priority' => collect(['low', 'normal', 'high'])->random(),
            'payment_status' => in_array($statusInfo['status'], ['delivered']) ? 'paid' : 'pending',
            'payment_method' => 'card',
            'shipping_address' => '123 E-commerce St, City',
            'billing_address' => '123 E-commerce St, City',
            'notes' => 'Test order: ' . $statusInfo['name'],
            'tracking_number' => in_array($statusInfo['status'], ['shipped', 'delivered']) ? 'TRACK-' . strtoupper(substr(md5($orderNumber), 0, 12)) : null,
            'shipping_carrier' => in_array($statusInfo['status'], ['shipped', 'delivered']) ? collect(['fedex', 'ups', 'dhl'])->random() : null,
            'shipped_date' => in_array($statusInfo['status'], ['shipped', 'delivered']) ? Carbon::today()->subDays(rand(1, 3)) : null,
            'subtotal' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'shipping_amount' => 50.00,
            'total_amount' => 0,
        ]);
        
        // Add items
        $subtotal = 0;
        $itemCount = rand(1, 3);
        $selectedProducts = $products->random(min($itemCount, $products->count()));
        
        foreach ($selectedProducts as $product) {
            $quantity = rand(1, 3);
            $unitPrice = (float) $product->price;
            $lineTotal = $quantity * $unitPrice;
            $subtotal += $lineTotal;
            
            SalesOrderItem::create([
                'order_id' => $order->order_id,
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'discount_amount' => 0,
                'line_total' => $lineTotal,
            ]);
        }
        
        // Update totals
        $taxAmount = $subtotal * 0.12;
        $totalAmount = $subtotal + $taxAmount + $order->shipping_amount;
        
        $order->update([
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
        ]);
        
        echo "✓ Created {$statusInfo['name']}: {$orderNumber} - Total: $" . number_format($totalAmount, 2) . "\n";
    }
    
    $totalOrders = SalesOrder::count();
    echo "\n✅ Successfully created " . count($statuses) . " test orders!\n";
    echo "📊 Total orders in system: {$totalOrders}\n\n";
    echo "🌐 You can view them at: http://localhost/sales/orders\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
