<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Services\InventoryService;
use App\Models\Inventory;

class StockService
{
    /**
     * Get paginated stock movements with filters.
     */
    public function getPaginatedMovements(Request $request)
    {
        $query = StockMovement::with(['product', 'user']);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('product_name', 'LIKE', "%{$search}%")
                  ->orWhere('product_brand', 'LIKE', "%{$search}%");
            })->orWhere('notes', 'LIKE', "%{$search}%")
              ->orWhere('reason', 'LIKE', "%{$search}%");
        }

        // Filter by movement type
        if ($request->has('movement_type') && $request->movement_type) {
            $query->ofType($request->movement_type);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            if ($request->status === 'confirmed') {
                $query->confirmed();
            } elseif ($request->status === 'pending') {
                $query->pending();
            }
        }

        // Filter by direction (inbound/outbound)
        if ($request->has('direction') && $request->direction) {
            if ($request->direction === 'inbound') {
                $query->inbound();
            } elseif ($request->direction === 'outbound') {
                $query->outbound();
            }
        }

        // Date range filters
        if ($request->has('start_date') && $request->start_date) {
            $query->where('movement_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('movement_date', '<=', $request->end_date);
        }

        // Product filter
        if ($request->has('product_id') && $request->product_id) {
            $query->where('product_id', $request->product_id);
        }

        // User filter
        if ($request->has('user_id') && $request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        // Sorting
        $sortBy = $request->get('sort', 'movement_date');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        return $query->paginate(15)->withQueryString();
    }

    /**
     * Get filter options for dropdowns.
     */
    public function getFilterOptions(): array
    {
        return [
            'movement_types' => [
                StockMovement::TYPE_SALE => 'Sale',
                StockMovement::TYPE_PURCHASE => 'Purchase',
                StockMovement::TYPE_RETURN => 'Return',
                StockMovement::TYPE_ADJUSTMENT => 'Stock Adjustment',
                StockMovement::TYPE_TRANSFER_IN => 'Transfer In',
                StockMovement::TYPE_TRANSFER_OUT => 'Transfer Out',
                StockMovement::TYPE_AUDIT => 'Audit Adjustment',
                StockMovement::TYPE_WASTE => 'Waste/Damaged',
                StockMovement::TYPE_PRODUCTION => 'Production',
                StockMovement::TYPE_INITIAL_STOCK => 'Initial Stock',
            ],
            'products' => Product::orderBy('product_name')->get(['product_id', 'product_name', 'product_brand']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ];
    }

    /**
     * Create a new stock adjustment.
     */
    public function createStockAdjustment(array $data): array
    {
        try {
            $product = Product::find($data['product_id']);
            
            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'Product not found',
                ];
            }

            // Compute change from current inventory total to the requested new quantity
            $currentInventoryTotal = (int) Inventory::where('product_id', $product->product_id)->sum('quantity_on_hand');
            $newQuantity = (int) $data['new_quantity'];
            $quantityChange = $newQuantity - $currentInventoryTotal;

            // Use InventoryService to adjust stock so Inventory and Product.quantity stay in sync.
            // This will also record a StockMovement with the correct before/after.
            $inventory = InventoryService::adjust(
                productId: $data['product_id'],
                quantityChange: $quantityChange,
                unitCost: $data['unit_cost'] ?? null,
                movementType: StockMovement::TYPE_ADJUSTMENT,
                referenceType: null,
                referenceId: null,
                propertyId: null,
                location: null,
                syncProductQuantity: true
            );

            // Optionally, movement_date from input should be respected. If provided, update the just-created movement's date.
            if (!empty($data['movement_date'])) {
                $latestMovement = StockMovement::where('product_id', $product->product_id)
                    ->orderByDesc('movement_id')
                    ->first();
                if ($latestMovement) {
                    $latestMovement->update(['movement_date' => Carbon::parse($data['movement_date'])]);
                }
            }

            // Create notification for stock adjustment
            $adjustmentType = $quantityChange > 0 ? 'increased' : 'decreased';
            Notification::create([
                'title' => 'Stock Adjustment Recorded',
                'message' => "Stock for {$product->product_name} has been {$adjustmentType} by " . abs($quantityChange) . " units (New quantity: {$newQuantity})",
                'level' => 'info',
                'type' => 'inventory.stock_adjustment',
                'link' => route('inventory.product_stock_adjustment.index'),
            ]);

            return [
                'success' => true,
                'message' => 'Stock adjustment recorded successfully',
                'movement' => isset($latestMovement) && $latestMovement ? $latestMovement : null,
                'product' => $product->fresh(),
                'inventory' => $inventory,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to record stock adjustment: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Create a stock transfer.
     */
    public function createStockTransfer(array $data): array
    {
        try {
            $productFrom = Product::find($data['product_id_from']);
            $productTo = Product::find($data['product_id_to']);
            
            if (!$productFrom || !$productTo) {
                return [
                    'success' => false,
                    'message' => 'One or both products not found',
                ];
            }

            $transferQuantity = (int) $data['quantity'];
            
            if ($productFrom->quantity < $transferQuantity) {
                return [
                    'success' => false,
                    'message' => 'Insufficient stock in source product',
                ];
            }

            // Apply adjustments via InventoryService to keep inventory in sync
            $movementDate = isset($data['movement_date']) ? Carbon::parse($data['movement_date']) : null;

            $invFrom = InventoryService::adjust(
                productId: $data['product_id_from'],
                quantityChange: -$transferQuantity,
                unitCost: $data['unit_cost'] ?? null,
                movementType: StockMovement::TYPE_TRANSFER_OUT,
                referenceType: 'transfer',
                referenceId: null,
                propertyId: null,
                location: $data['location_from'] ?? null,
                syncProductQuantity: true
            );
            $invTo = InventoryService::adjust(
                productId: $data['product_id_to'],
                quantityChange: $transferQuantity,
                unitCost: $data['unit_cost'] ?? null,
                movementType: StockMovement::TYPE_TRANSFER_IN,
                referenceType: 'transfer',
                referenceId: null,
                propertyId: null,
                location: $data['location_to'] ?? null,
                syncProductQuantity: true
            );

            // If a specific movement date was provided, update the two most recent movements accordingly
            if ($movementDate) {
                $movements = StockMovement::whereIn('product_id', [$productFrom->product_id, $productTo->product_id])
                    ->orderByDesc('movement_id')
                    ->limit(2)
                    ->get();
                foreach ($movements as $m) {
                    $m->update(['movement_date' => $movementDate]);
                }
            }

            return [
                'success' => true,
                'message' => 'Stock transfer completed successfully',
                'movements' => StockMovement::whereIn('product_id', [$productFrom->product_id, $productTo->product_id])
                                ->orderByDesc('movement_id')
                                ->limit(2)
                                ->get(),
                'product_from' => $productFrom->fresh(),
                'product_to' => $productTo->fresh(),
                'inventory_from' => $invFrom,
                'inventory_to' => $invTo,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to complete stock transfer: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Record waste/damage stock movement.
     */
    public function recordWaste(array $data): array
    {
        try {
            $product = Product::find($data['product_id']);
            
            if (!$product) {
                return [
                    'success' => false,
                    'message' => 'Product not found',
                ];
            }

            $wasteQuantity = (int) $data['quantity'];
            
            if ($product->quantity < $wasteQuantity) {
                return [
                    'success' => false,
                    'message' => 'Insufficient stock available',
                ];
            }

            // Apply adjustment via InventoryService (negative change)
            $inventory = InventoryService::adjust(
                productId: $data['product_id'],
                quantityChange: -$wasteQuantity,
                unitCost: $data['unit_cost'] ?? null,
                movementType: StockMovement::TYPE_WASTE,
                referenceType: 'waste',
                referenceId: null,
                propertyId: null,
                location: null,
                syncProductQuantity: true
            );

            // Respect custom movement date if provided
            if (!empty($data['movement_date'])) {
                $latestMovement = StockMovement::where('product_id', $product->product_id)
                    ->orderByDesc('movement_id')
                    ->first();
                if ($latestMovement) {
                    $latestMovement->update(['movement_date' => Carbon::parse($data['movement_date'])]);
                }
            }

            return [
                'success' => true,
                'message' => 'Waste/damage recorded successfully',
                'movement' => isset($latestMovement) && $latestMovement ? $latestMovement : null,
                'product' => $product->fresh(),
                'inventory' => $inventory,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to record waste: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get stock analytics and dashboard data.
     */
    public function getStockAnalytics(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $movements = StockMovement::confirmed()
                       ->dateRange($startDate, $endDate)
                       ->with(['product']);

        $totalMovements = $movements->count();
        $inboundMovements = $movements->inbound()->count();
        $outboundMovements = $movements->outbound()->count();
        
        $inboundQuantity = $movements->inbound()->sum('quantity_change');
        $outboundQuantity = abs($movements->outbound()->sum('quantity_change'));
        
        $inboundValue = $movements->inbound()->sum('total_value') ?? 0;
        $outboundValue = $movements->outbound()->sum('total_value') ?? 0;

        $movementsByType = StockMovement::getMovementSummary($startDate, $endDate);
        
        $recentMovements = StockMovement::getRecentMovements(10);
        
        $lowStockProducts = Product::whereNotNull('reorder_level')
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->get();
        $outOfStockProducts = Product::outOfStock();

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'totals' => [
                'total_movements' => $totalMovements,
                'inbound_movements' => $inboundMovements,
                'outbound_movements' => $outboundMovements,
                'inbound_quantity' => $inboundQuantity,
                'outbound_quantity' => $outboundQuantity,
                'net_quantity_change' => $inboundQuantity - $outboundQuantity,
                'inbound_value' => $inboundValue,
                'outbound_value' => $outboundValue,
                'net_value_change' => $inboundValue - $outboundValue,
            ],
            'movements_by_type' => $movementsByType,
            'recent_movements' => $recentMovements,
            'alerts' => [
                'low_stock_count' => $lowStockProducts->count(),
                'low_stock_products' => $lowStockProducts,
                'out_of_stock_count' => $outOfStockProducts->count(),
                'out_of_stock_products' => $outOfStockProducts,
            ],
        ];
    }

    /**
     * Get stock movement history for a specific product.
     */
    public function getProductStockHistory(int $productId, ?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $product = Product::find($productId);
        
        if (!$product) {
            return [
                'success' => false,
                'message' => 'Product not found',
            ];
        }

        $query = StockMovement::where('product_id', $productId)
                    ->with(['user'])
                    ->confirmed();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        $movements = $query->orderBy('movement_date', 'desc')->get();

        return [
            'success' => true,
            'product' => $product,
            'movements' => $movements,
            'summary' => [
                'total_movements' => $movements->count(),
                'total_inbound' => $movements->where('quantity_change', '>', 0)->sum('quantity_change'),
                'total_outbound' => abs($movements->where('quantity_change', '<', 0)->sum('quantity_change')),
                'net_change' => $movements->sum('quantity_change'),
                'current_stock' => $product->quantity,
            ],
        ];
    }

    /**
     * Bulk stock update from import.
     */
    public function bulkStockUpdate(array $updates): array
    {
        $results = [
            'success_count' => 0,
            'error_count' => 0,
            'errors' => [],
        ];

        foreach ($updates as $update) {
            try {
                $result = $this->createStockAdjustment($update);
                
                if ($result['success']) {
                    $results['success_count']++;
                } else {
                    $results['error_count']++;
                    $results['errors'][] = $result['message'];
                }
            } catch (\Exception $e) {
                $results['error_count']++;
                $results['errors'][] = "Product ID {$update['product_id']}: " . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Delete a stock movement (only if pending).
     */
    public function deleteMovement(StockMovement $movement): array
    {
        if ($movement->status !== StockMovement::STATUS_PENDING) {
            return [
                'success' => false,
                'message' => 'Only pending movements can be deleted',
            ];
        }

        try {
            $movement->delete();
            
            return [
                'success' => true,
                'message' => 'Stock movement deleted successfully',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete movement: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Confirm a pending stock movement.
     */
    public function confirmMovement(StockMovement $movement): array
    {
        if ($movement->status !== StockMovement::STATUS_PENDING) {
            return [
                'success' => false,
                'message' => 'Movement is not pending',
            ];
        }

        try {
            // No need to update product stock directly - movement already tracks quantity_after
            
            // Update movement status
            $movement->update(['status' => StockMovement::STATUS_CONFIRMED]);
            
            return [
                'success' => true,
                'message' => 'Stock movement confirmed successfully',
                'movement' => $movement->fresh(),
                'product' => $movement->product->fresh(),
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to confirm movement: ' . $e->getMessage(),
            ];
        }
    }
}
