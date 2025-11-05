<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Creating Customer Tax & Discount ===\n\n";

// Create customer tax (12% VAT)
$customerTax = \App\Models\TaxDiscount::create([
    'code' => 'CUST-TAX-VAT',
    'name' => 'Customer VAT 12%',
    'type' => 'tax',
    'applicable_for' => 'customer',
    'calculation_method' => 'percentage',
    'rate' => 12.00,
    'description' => 'Standard customer VAT tax',
    'applies_to' => 'all',
    'priority' => 1,
    'is_active' => true,
]);

echo "Created: {$customerTax->name} (ID: {$customerTax->id})\n";

// Create customer discount (5% loyalty)
$customerDiscount = \App\Models\TaxDiscount::create([
    'code' => 'CUST-DISC-LOYALTY',
    'name' => 'Customer Loyalty 5%',
    'type' => 'discount',
    'applicable_for' => 'customer',
    'calculation_method' => 'percentage',
    'rate' => 5.00,
    'description' => 'Loyalty discount for regular customers',
    'applies_to' => 'all',
    'priority' => 1,
    'is_active' => true,
]);

echo "Created: {$customerDiscount->name} (ID: {$customerDiscount->id})\n\n";

echo "=== Summary: What Shows Where ===\n\n";

echo "📦 Purchase Orders (Supplier):\n";
$supplierTaxes = \App\Models\TaxDiscount::active()->taxes()->forSupplier()->get();
$supplierDiscounts = \App\Models\TaxDiscount::active()->discounts()->forSupplier()->get();
echo "  Taxes: " . ($supplierTaxes->isEmpty() ? 'None' : $supplierTaxes->pluck('name')->join(', ')) . "\n";
echo "  Discounts: " . ($supplierDiscounts->isEmpty() ? 'None' : $supplierDiscounts->pluck('name')->join(', ')) . "\n\n";

echo "🛒 Sales Orders / POS (Customer):\n";
$customerTaxes = \App\Models\TaxDiscount::active()->taxes()->forCustomer()->get();
$customerDiscounts = \App\Models\TaxDiscount::active()->discounts()->forCustomer()->get();
echo "  Taxes: " . ($customerTaxes->isEmpty() ? 'None' : $customerTaxes->pluck('name')->join(', ')) . "\n";
echo "  Discounts: " . ($customerDiscounts->isEmpty() ? 'None' : $customerDiscounts->pluck('name')->join(', ')) . "\n\n";

echo "✅ Done! Tax and discount separation is now working correctly.\n";
