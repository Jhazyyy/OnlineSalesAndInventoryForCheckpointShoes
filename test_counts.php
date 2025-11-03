<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Inventory;

echo "Products: " . Product::count() . "\n";
echo "Inventory Records: " . Inventory::count() . "\n";
echo "Inventory with product: " . Inventory::whereHas('product')->count() . "\n";
echo "Orphaned inventory records: " . Inventory::whereDoesntHave('product')->count() . "\n";
