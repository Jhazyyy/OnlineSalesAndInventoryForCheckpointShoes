<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\InventoryService;

class InitInventory extends Command
{
    protected $signature = 'inventory:init {--force : Overwrite existing inventory quantities}';
    protected $description = 'Initialize inventories from current product quantities (master data)';

    public function handle(): int
    {
        $force = (bool) $this->option('force');
        $count = 0;

        Product::chunk(200, function ($products) use (&$count, $force) {
            foreach ($products as $product) {
                $inv = \App\Models\Inventory::where('product_id', $product->product_id)
                    ->whereNull('property_id')
                    ->whereNull('location')
                    ->first();

                if ($inv && !$force) {
                    continue;
                }

                $targetQty = (int) ($product->quantity ?? 0);
                if (!$inv) {
                    $inv = \App\Models\Inventory::create([
                        'product_id' => $product->product_id,
                        'sku' => $product->product_brand,
                        'location' => null,
                        'quantity_on_hand' => 0,
                        'quantity_reserved' => 0,
                    ]);
                }

                $delta = $targetQty - (int) $inv->quantity_on_hand;
                if ($delta !== 0) {
                    InventoryService::adjust(
                        productId: $product->product_id,
                        quantityChange: $delta,
                        unitCost: null,
                        movementType: \App\Models\StockMovement::TYPE_INITIAL_STOCK,
                        referenceType: 'inventory_init',
                        referenceId: null,
                        propertyId: null,
                        location: null,
                        syncProductQuantity: true
                    );
                }
                $count++;
            }
        });

        $this->info("Initialized inventory for {$count} products.");
        return self::SUCCESS;
    }
}
