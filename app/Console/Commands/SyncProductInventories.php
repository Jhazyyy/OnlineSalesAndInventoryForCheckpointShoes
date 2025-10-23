<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\InventoryService;

class SyncProductInventories extends Command
{
    protected $signature = 'inventory:sync-products';
    protected $description = 'Ensure all products have corresponding inventory records';

    public function handle(): int
    {
        $this->info('Syncing inventory records for all products...');
        
        $products = Product::all();
        $created = 0;
        $existing = 0;

        foreach ($products as $product) {
            $inventory = \App\Models\Inventory::where('product_id', $product->product_id)
                ->whereNull('property_id')
                ->whereNull('location')
                ->first();

            if (!$inventory) {
                InventoryService::getOrCreate(
                    productId: $product->product_id,
                    propertyId: null,
                    location: null
                );
                $created++;
                $this->line("✓ Created inventory record for: {$product->product_name}");
            } else {
                $existing++;
            }
        }

        $this->newLine();
        $this->info("Sync complete!");
        $this->table(
            ['Status', 'Count'],
            [
                ['Created', $created],
                ['Existing', $existing],
                ['Total', $products->count()],
            ]
        );

        return Command::SUCCESS;
    }
}
