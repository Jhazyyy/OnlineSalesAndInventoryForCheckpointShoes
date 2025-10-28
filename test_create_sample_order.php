<?php

/**
 * Sample Script to Create Test Sales Order
 * This simulates an order received from e-commerce application
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
    echo "Creating sample sales order from e-commerce...\n\n";
    
    // Get or create a test customer
    $customer = Customer::first();
    if (!$customer) {
        echo "No customers found. Creating test customer...\n";
        $customer = Customer::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'john.doe@ecommerce.com',
            'phone' => '1234567890',
            'address' => '123 Main Street, City, State 12345',
            'customer_type' => 'individual',
            'status' => 'active',
        ]);
        echo "✓ Created test customer: {$customer->display_name}\n\n";
    }
    
    // Get some products
    $products = Product::take(3)->get();
    if ($products->isEmpty()) {
        echo "No products found. Please create products first.\n";
        exit(1);
    }
    
    // Create sales order (simulating data from e-commerce)
    $order = SalesOrder::create([
        'order_number' => 'ECOM-' . now()->format('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT),
        'customer_id' => $customer->customer_id,
        'order_date' => Carbon::today(),
        'required_date' => Carbon::today()->addDays(7),
        'status' => 'shipped',
        'priority' => 'normal',
        'payment_status' => 'pending',
        'payment_method' => 'card',
        'shipping_address' => $customer->address ?? '123 Main St, City',
        'billing_address' => $customer->address ?? '123 Main St, City',
        'notes' => 'Order received from e-commerce application',
        'subtotal' => 0,
        'tax_amount' => 0,
        'discount_amount' => 0,
        'shipping_amount' => 50.00,
        'total_amount' => 0,
    ]);
    
    echo "✓ Created order: {$order->order_number}\n";
    
    // Add order items
    $subtotal = 0;
    foreach ($products as $index => $product) {
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
            'notes' => null,
        ]);
        
        echo "  ✓ Added item: {$product->product_name} (Qty: {$quantity})\n";
    }
    
    // Update order totals
    $taxAmount = $subtotal * 0.12; // 12% tax
    $totalAmount = $subtotal + $taxAmount + $order->shipping_amount - $order->discount_amount;
    
    $order->update([
        'subtotal' => $subtotal,
        'tax_amount' => $taxAmount,
        'total_amount' => $totalAmount,
    ]);
    
    echo "\n✓ Order totals updated:\n";
    echo "  - Subtotal: ₱" . number_format($subtotal, 2) . "\n";
    echo "  - Tax: ₱" . number_format($taxAmount, 2) . "\n";
    echo "  - Shipping: ₱" . number_format($order->shipping_amount, 2) . "\n";
    echo "  - Total: ₱" . number_format($totalAmount, 2) . "\n";
    
    echo "\n✅ Sample sales order created successfully!\n";
    echo "\nYou can now view it at: /sales/orders/{$order->order_id}\n";
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
