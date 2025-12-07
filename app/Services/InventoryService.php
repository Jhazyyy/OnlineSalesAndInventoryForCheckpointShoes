<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Get or create an inventory record for a product/location.
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

        // Use SKU from product if available
        $sku = $product?->sku;
        if (!$sku && $product) {
            // Generate SKU from brand and product name as fallback
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

            // Calculate weighted average cost when receiving inventory
            if ($quantityChange > 0 && $unitCost !== null && $unitCost > 0) {
                // Weighted Average Cost formula: (Old Value + New Value) / Total Quantity
                $oldValue = $before * (float) ($inventory->unit_cost ?? 0);
                $newValue = $quantityChange * $unitCost;
                $totalQuantity = $after;
                
                if ($totalQuantity > 0) {
                    $inventory->unit_cost = (string) round(($oldValue + $newValue) / $totalQuantity, 2);
                }
            }

            // Persist inventory change with protection against negative values
            $inventory->quantity_on_hand = max(0, $after);
            $inventory->last_movement_at = now();
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
                // Use model to trigger mutators and prevent negative values
                $product = Product::find($productId);
                if ($product) {
                    $product->quantity = max(0, $total); // Ensure non-negative
                    $product->save();
                }
            }

            return $inventory->refresh();
        });
    }
}
