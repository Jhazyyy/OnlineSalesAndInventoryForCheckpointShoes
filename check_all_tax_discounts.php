<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== All Tax & Discount Records ===\n\n";

$all = \App\Models\TaxDiscount::all();

foreach ($all as $item) {
    echo "ID: {$item->id}\n";
    echo "  Name: {$item->name}\n";
    echo "  Type: {$item->type}\n";
    echo "  Applicable For: {$item->applicable_for}\n";
    echo "  Active: " . ($item->is_active ? 'Yes' : 'No') . "\n";
    echo "  Method: {$item->calculation_method}\n";
    echo "  Rate: {$item->rate}\n";
    echo "  Fixed: {$item->fixed_amount}\n";
    echo "\n";
}

echo "=== What Shows Where ===\n\n";

echo "Purchase Orders (Supplier):\n";
$supplierTaxes = \App\Models\TaxDiscount::active()->taxes()->forSupplier()->get();
$supplierDiscounts = \App\Models\TaxDiscount::active()->discounts()->forSupplier()->get();
echo "  Taxes: " . $supplierTaxes->pluck('name')->join(', ') . "\n";
echo "  Discounts: " . $supplierDiscounts->pluck('name')->join(', ') . "\n\n";

echo "Sales Orders / POS (Customer):\n";
$customerTaxes = \App\Models\TaxDiscount::active()->taxes()->forCustomer()->get();
$customerDiscounts = \App\Models\TaxDiscount::active()->discounts()->forCustomer()->get();
echo "  Taxes: " . $customerTaxes->pluck('name')->join(', ') . "\n";
echo "  Discounts: " . $customerDiscounts->pluck('name')->join(', ') . "\n\n";

echo "=== Recommendation ===\n";
echo "To fix this, update the 'applicable_for' field:\n";
echo "- For supplier-only: SET applicable_for = 'supplier'\n";
echo "- For customer-only: SET applicable_for = 'customer'\n";
echo "- For both: Keep as 'both'\n";
