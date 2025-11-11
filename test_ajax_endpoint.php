<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

echo "=== Testing AJAX Endpoint for Different Periods ===\n\n";

$periods = ['today', 'yesterday', 'this_week', 'this_month'];

foreach ($periods as $period) {
    echo "Period: $period\n";
    echo str_repeat('-', 50) . "\n";
    
    $query = \App\Models\SalesOrderItem::query();
    
    switch ($period) {
        case 'today':
            $query->whereHas('order', function ($q) {
                $q->whereDate('order_date', today());
            });
            break;
        case 'yesterday':
            $query->whereHas('order', function ($q) {
                $q->whereDate('order_date', today()->subDay());
            });
            break;
        case 'this_week':
            $query->whereHas('order', function ($q) {
                $q->whereBetween('order_date', [now()->startOfWeek(), now()->endOfWeek()]);
            });
            break;
        case 'this_month':
            $query->whereHas('order', function ($q) {
                $q->whereMonth('order_date', now()->month)
                  ->whereYear('order_date', now()->year);
            });
            break;
    }
    
    $items = $query->select('product_id')
        ->selectRaw('SUM(quantity) as total_quantity')
        ->selectRaw('SUM(quantity * unit_price) as total_revenue')
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
                'revenue' => (float) $item->total_revenue,
                'image' => $item->product->image ?? null,
            ];
        })
        ->values();
    
    echo "Found " . $items->count() . " products\n";
    foreach ($items as $item) {
        echo "  - {$item['name']}: {$item['quantity']} units (₱" . number_format($item['revenue'], 2) . ")\n";
    }
    echo "\n";
}

echo "=== Test Complete ===\n";
