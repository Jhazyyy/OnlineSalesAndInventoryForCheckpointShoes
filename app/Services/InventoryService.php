<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductProperty;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Get or create an inventory record for a product/property/location.
     */
    public static function getOrCreate(int $productId, ?int $propertyId = null, ?string $location = null): Inventory
    {
        $inv = Inventory::where('product_id', $productId)
            ->when($propertyId, fn($q) => $q->where('property_id', $propertyId))
            ->when(!$propertyId, fn($q) => $q->whereNull('property_id'))
            ->when($location, fn($q) => $q->where('location', $location))
            ->when(!$location, fn($q) => $q->whereNull('location'))
            ->first();

        if ($inv) {
            return $inv;
        }

        $product = Product::find($productId);
        $property = $propertyId ? ProductProperty::find($propertyId) : null;

        // Generate SKU if not available from property
        $sku = $property?->sku;
        if (!$sku && $product) {
            // Generate SKU from brand and product name
            $brandCode = strtoupper(substr($product->product_brand ?? 'XX', 0, 3));
            $productCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $product->product_name), 0, 3));
            $uniqueId = str_pad($product->product_id, 3, '0', STR_PAD_LEFT);
            $sku = "{$brandCode}-{$productCode}{$uniqueId}";
        }

        return Inventory::create([
            'product_id' => $productId,
            'property_id' => $propertyId,
            'sku' => $sku,
            'location' => $location,
            'quantity_on_hand' => 0,
            'quantity_reserved' => 0,
            'unit_cost' => null,
            'last_movement_at' => null,
        ]);
    }

    /**
     * Adjust inventory and record stock movement. Optionally sync Product.quantity for backward compatibility.
     */
    public static function adjust(
        int $productId,
        int $quantityChange,
        ?float $unitCost = null,
        string $movementType = StockMovement::TYPE_ADJUSTMENT,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $propertyId = null,
        ?string $location = null,
        bool $syncProductQuantity = true
    ): Inventory {
        return DB::transaction(function () use (
            $productId,
            $quantityChange,
            $unitCost,
            $movementType,
            $referenceType,
            $referenceId,
            $propertyId,
            $location,
            $syncProductQuantity
        ) {
            $inventory = self::getOrCreate($productId, $propertyId, $location);

            $before = (int) $inventory->quantity_on_hand;
            $after = $before + (int) $quantityChange;

            // Persist inventory change
            $inventory->quantity_on_hand = $after;
            $inventory->last_movement_at = now();
            // Optionally unit_cost could be tracked via a costing service; skipping assignment here for compatibility
            $inventory->save();

            // Record stock movement using inventory-based before/after
            StockMovement::recordMovement(
                productId: $productId,
                quantityBefore: $before,
                quantityChange: $quantityChange,
                quantityAfter: $after,
                movementType: $movementType,
                unitCost: $unitCost,
                referenceType: $referenceType,
                referenceId: $referenceId,
                locationFrom: null,
                locationTo: $location,
            );

            // Back-compat: keep products.quantity in sync as total of all inventory rows
            if ($syncProductQuantity) {
                $total = Inventory::where('product_id', $productId)->sum('quantity_on_hand');
                Product::where('product_id', $productId)->update(['quantity' => $total]);
            }

            return $inventory->refresh();
        });
    }
}
