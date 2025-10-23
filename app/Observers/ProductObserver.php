<?php

namespace App\Observers;

use App\Models\Product;
use App\Services\InventoryService;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     * Automatically create an inventory record when a product is created.
     */
    public function created(Product $product): void
    {
        // Create an initial inventory record for the product
        InventoryService::getOrCreate(
            productId: $product->product_id,
            propertyId: null,
            location: null
        );
    }
}
