<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Checking Tax & Discount Data ===\n\n";

// Check customer taxes
$taxes = \App\Models\TaxDiscount::active()->taxes()->forCustomer()->get();
echo "Customer Taxes Count: " . $taxes->count() . "\n";
foreach ($taxes as $tax) {
    echo "  - ID: {$tax->id}, Name: {$tax->name}, Type: {$tax->type}, Applicable: {$tax->applicable_for}\n";
    echo "    Method: {$tax->calculation_method}, Rate: {$tax->rate}, Fixed: {$tax->fixed_amount}\n";
}

echo "\n";

// Check customer discounts
$discounts = \App\Models\TaxDiscount::active()->discounts()->forCustomer()->get();
echo "Customer Discounts Count: " . $discounts->count() . "\n";
foreach ($discounts as $discount) {
    echo "  - ID: {$discount->id}, Name: {$discount->name}, Type: {$discount->type}, Applicable: {$discount->applicable_for}\n";
    echo "    Method: {$discount->calculation_method}, Rate: {$discount->rate}, Fixed: {$discount->fixed_amount}\n";
}

echo "\n=== Testing View Variables ===\n";
echo "Variable \$activeTaxes would have: " . $taxes->count() . " items\n";
echo "Variable \$activeDiscounts would have: " . $discounts->count() . " items\n";

echo "\n=== isNotEmpty() Check ===\n";
echo "\$activeTaxes->isNotEmpty(): " . ($taxes->isNotEmpty() ? 'true' : 'false') . "\n";
echo "\$activeDiscounts->isNotEmpty(): " . ($discounts->isNotEmpty() ? 'true' : 'false') . "\n";
