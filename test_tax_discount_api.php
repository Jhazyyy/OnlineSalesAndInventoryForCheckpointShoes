<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Create a test product first
$product = \App\Models\Product::first();

if (!$product) {
    echo "No products found. Please create a product first.\n";
    exit;
}

echo "Testing Tax Discount API with Product ID: {$product->id}\n";
echo "Product Name: {$product->name}\n\n";

// Test the service directly
$items = collect([
    (object) [
        'product_id' => $product->id,
        'quantity' => 2,
        'unit_price' => 100.00,
    ]
]);

$subtotal = 200.00;

$service = new \App\Services\TaxDiscountService();
$result = $service->calculateForOrder($subtotal, $items, 'sales');

echo "Service Test Results:\n";
echo "Total Tax: " . $result['total_tax'] . "\n";
echo "Total Discount: " . $result['total_discount'] . "\n";

echo "\nBreakdown:\n";
print_r($result);

echo "\n\nActive Discounts in Database:\n";
$activeDiscounts = \App\Models\TaxDiscount::active()->discounts()->get();
foreach ($activeDiscounts as $discount) {
    echo "- {$discount->name}: {$discount->rate} ({$discount->calculation_method})\n";
    echo "  Applies to: {$discount->applies_to}\n";
    if ($discount->applies_to === 'category') {
        echo "  Categories: " . $discount->categories->pluck('name')->join(', ') . "\n";
    }
    if ($discount->applies_to === 'product') {
        echo "  Products: " . $discount->products->pluck('name')->join(', ') . "\n";
    }
}
