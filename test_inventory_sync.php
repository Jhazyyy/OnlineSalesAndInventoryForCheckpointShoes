<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Product;
use App\Models\Inventory;

echo "=== CHECKING PRODUCT AND INVENTORY SYNC ===\n\n";

$products = Product::take(5)->get();

foreach ($products as $product) {
    $inventory = Inventory::where('product_id', $product->product_id)
        ->whereNull('property_id')
        ->whereNull('location')
        ->first();
    
    echo sprintf(
        "Product: %-35s\n",
        substr($product->product_name, 0, 35)
    );
    echo sprintf("  Product.quantity:           %d\n", $product->quantity ?? 0);
    echo sprintf("  Inventory.quantity_on_hand: %d\n", $inventory ? $inventory->quantity_on_hand : 0);
    echo sprintf("  Stock Movements Count:      %d\n", $product->stockMovements()->count());
    
    $latestMovement = $product->stockMovements()->latest()->first();
    if ($latestMovement) {
        echo sprintf("  Latest Movement:            %s (qty: %d → %d)\n", 
            $latestMovement->movement_type,
            $latestMovement->quantity_before,
            $latestMovement->quantity_after
        );
    }
    echo "\n";
}

echo "\n=== INVENTORY TABLE SUMMARY ===\n";
echo "Total Inventory Records: " . Inventory::count() . "\n";
echo "Records with stock > 0:  " . Inventory::where('quantity_on_hand', '>', 0)->count() . "\n";

$sampleInventory = Inventory::where('quantity_on_hand', '>', 0)->take(5)->get();
echo "\nSample Inventory Records:\n";
foreach ($sampleInventory as $inv) {
    echo sprintf(
        "  %-35s | Qty: %3d | Product Qty: %3d\n",
        substr($inv->product->product_name ?? 'Unknown', 0, 35),
        $inv->quantity_on_hand,
        $inv->product->quantity ?? 0
    );
}
