<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test today's top selling items
echo "=== Testing Top Selling Items (Today) ===\n";
$todayItems = \App\Models\SalesOrderItem::whereHas('order', function ($q) {
    $q->whereDate('order_date', today());
})
->select('product_id')
->selectRaw('SUM(quantity) as total_quantity')
->whereNotNull('product_id')
->with('product')
->groupBy('product_id')
->orderByDesc('total_quantity')
->limit(10)
->get()
->filter(function ($item) {
    return $item->product !== null;
})
->map(function ($item) {
    return [
        'name' => $item->product->name ?? 'Unknown Product',
        'quantity' => (int) $item->total_quantity,
        'revenue' => (float) ($item->quantity * $item->unit_price ?? 0),
        'image' => $item->product->image ?? null,
    ];
});

echo "Found " . $todayItems->count() . " products sold today:\n";
foreach ($todayItems as $item) {
    echo "  - {$item['name']}: {$item['quantity']} units\n";
}

// Test this week
echo "\n=== Testing Top Selling Items (This Week) ===\n";
$weekItems = \App\Models\SalesOrderItem::whereHas('order', function ($q) {
    $q->whereBetween('order_date', [now()->startOfWeek(), now()->endOfWeek()]);
})
->select('product_id')
->selectRaw('SUM(quantity) as total_quantity')
->whereNotNull('product_id')
->with('product')
->groupBy('product_id')
->orderByDesc('total_quantity')
->limit(10)
->get()
->filter(function ($item) {
    return $item->product !== null;
})
->map(function ($item) {
    return [
        'name' => $item->product->name ?? 'Unknown Product',
        'quantity' => (int) $item->total_quantity,
    ];
});

echo "Found " . $weekItems->count() . " products sold this week:\n";
foreach ($weekItems as $item) {
    echo "  - {$item['name']}: {$item['quantity']} units\n";
}

echo "\n=== Test Complete ===\n";
