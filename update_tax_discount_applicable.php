<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Updating Tax & Discount Records ===\n\n";

// Update SUPPLIER records to be supplier-only
$updated = \App\Models\TaxDiscount::whereIn('id', [2, 3])
    ->update(['applicable_for' => 'supplier']);

echo "Updated {$updated} records to 'supplier'\n\n";

// Show updated records
echo "=== Updated Records ===\n\n";
$records = \App\Models\TaxDiscount::whereIn('id', [2, 3])->get();
foreach ($records as $item) {
    echo "ID: {$item->id} - {$item->name}\n";
    echo "  Applicable For: {$item->applicable_for}\n\n";
}

echo "=== What Shows Where Now ===\n\n";

echo "Purchase Orders (Supplier):\n";
$supplierTaxes = \App\Models\TaxDiscount::active()->taxes()->forSupplier()->get();
$supplierDiscounts = \App\Models\TaxDiscount::active()->discounts()->forSupplier()->get();
echo "  Taxes: " . ($supplierTaxes->isEmpty() ? 'None' : $supplierTaxes->pluck('name')->join(', ')) . "\n";
echo "  Discounts: " . ($supplierDiscounts->isEmpty() ? 'None' : $supplierDiscounts->pluck('name')->join(', ')) . "\n\n";

echo "Sales Orders / POS (Customer):\n";
$customerTaxes = \App\Models\TaxDiscount::active()->taxes()->forCustomer()->get();
$customerDiscounts = \App\Models\TaxDiscount::active()->discounts()->forCustomer()->get();
echo "  Taxes: " . ($customerTaxes->isEmpty() ? 'None' : $customerTaxes->pluck('name')->join(', ')) . "\n";
echo "  Discounts: " . ($customerDiscounts->isEmpty() ? 'None' : $customerDiscounts->pluck('name')->join(', ')) . "\n\n";

echo "✅ Done! Now supplier tax/discounts will only show in Purchase Orders.\n";
echo "To create customer-specific tax/discounts, create new ones with applicable_for = 'customer'.\n";
