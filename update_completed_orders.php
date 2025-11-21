<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\PurchaseOrder;
use App\Models\PurchaseReceive;
use Illuminate\Support\Facades\DB;

// Find all orders that are still 'cancelled' but have been fully received
$orders = PurchaseOrder::where('status', 'cancelled')
    ->whereHas('receives', function($query) {
        $query->where('status', 'received');
    })
    ->get();

$updated = 0;

foreach ($orders as $order) {
    // Get the latest receive with 'received' status
    $latestReceive = $order->receives()
        ->where('status', 'received')
        ->orderBy('receive_date', 'desc')
        ->first();
    
    if ($latestReceive) {
        // Use DB::update to avoid the enum issue with Eloquent
        DB::table('purchase_orders')
            ->where('order_id', $order->order_id)
            ->update([
                'status' => 'completed',
                'received_date' => $latestReceive->receive_date->format('Y-m-d'),
                'updated_at' => now(),
            ]);
        
        $updated++;
        
        echo "Updated Order #{$order->order_number} to completed - Received on {$latestReceive->receive_date}\n";
    }
}

echo "\nTotal orders updated: {$updated}\n";
