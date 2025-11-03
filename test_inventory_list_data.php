<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Inventory;

echo "=== INVENTORY LIST DATA (What LiveWire will show) ===\n\n";

$inventory = Inventory::query()
    ->with(['product'])
    ->orderBy('product_id', 'asc')
    ->paginate(10);

echo "Total Inventory Records: " . $inventory->total() . "\n";
echo "Showing Page 1 (10 records):\n\n";

foreach ($inventory as $row) {
    $qty = (int)($row->quantity ?? 0);
    $status = $qty > 10 ? 'Good' : ($qty > 0 ? 'Low' : 'Out');
    
    echo sprintf(
        "%-40s | SKU: %-18s | Qty: %3d | Status: %s\n",
        substr($row->product->product_name ?? 'Unknown', 0, 40),
        substr($row->product->sku ?? '—', 0, 18),
        $qty,
        $status
    );
}

echo "\n=== STOCK STATUS SUMMARY ===\n";
echo "Good Stock (>10):  " . Inventory::where('quantity_on_hand', '>', 10)->count() . "\n";
echo "Low Stock (1-10):  " . Inventory::where('quantity_on_hand', '>', 0)->where('quantity_on_hand', '<=', 10)->count() . "\n";
echo "Out of Stock (0):  " . Inventory::where('quantity_on_hand', '<=', 0)->count() . "\n";
