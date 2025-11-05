<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$discount = \App\Models\TaxDiscount::first();
if ($discount) {
    $discount->applicable_for = 'supplier';
    $discount->save();
    echo "Updated discount: {$discount->name}\n";
    echo "Applicable for: {$discount->applicable_for}\n";
} else {
    echo "No discount found\n";
}
