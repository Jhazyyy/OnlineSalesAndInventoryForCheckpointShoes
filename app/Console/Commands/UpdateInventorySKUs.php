<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inventory;

class UpdateInventorySKUs extends Command
{
    protected $signature = 'inventory:update-skus';
    protected $description = 'Update SKU for all inventory records';

    public function handle(): int
    {
        $this->info('Updating SKUs for all inventory records...');
        
        $inventories = Inventory::with('product')->get();
        $updated = 0;

        foreach ($inventories as $inv) {
            if (!$inv->product) {
                continue;
            }

            $product = $inv->product;
            
            // Generate SKU from brand and product name
            $brandCode = strtoupper(substr($product->product_brand ?? 'XX', 0, 3));
            $productCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $product->product_name), 0, 3));
            $uniqueId = str_pad($product->product_id, 3, '0', STR_PAD_LEFT);
            $newSku = "{$brandCode}-{$productCode}{$uniqueId}";
            
            if ($inv->sku !== $newSku) {
                $inv->sku = $newSku;
                $inv->save();
                $updated++;
                $this->line("✓ Updated SKU for {$product->product_name}: {$newSku}");
            }
        }

        $this->newLine();
        $this->info("Updated {$updated} SKUs!");

        return Command::SUCCESS;
    }
}
