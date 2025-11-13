<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking profit_margin values in database:\n\n";

$products = DB::table('products')
    ->select('product_id', 'product_name', 'total_cost', 'price', 'profit_amount', 'profit_margin')
    ->get();

foreach ($products as $product) {
    echo "ID: {$product->product_id}\n";
    echo "Name: {$product->product_name}\n";
    echo "Total Cost: {$product->total_cost}\n";
    echo "Price: {$product->price}\n";
    echo "Profit Amount: {$product->profit_amount}\n";
    echo "Profit Margin (DB): {$product->profit_margin}\n";
    echo "---\n";
}
