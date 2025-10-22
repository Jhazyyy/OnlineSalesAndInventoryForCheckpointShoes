<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Carbon\Carbon;

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

            $quantityBefore = $product->quantity;
            $newQuantity = $data['new_quantity'];
            $quantityChange = $newQuantity - $quantityBefore;

            // Record the movement (no need to update product quantity directly anymore)
            $movement = StockMovement::recordMovement(
                productId: $data['product_id'],
                quantityBefore: $quantityBefore,
                quantityChange: $quantityChange,
                quantityAfter: $newQuantity,
                movementType: StockMovement::TYPE_ADJUSTMENT,
                userId: auth()->id(),
                unitCost: $data['unit_cost'] ?? null,
                notes: $data['notes'] ?? null,
                reason: $data['reason'] ?? null,
                movementDate: isset($data['movement_date']) ? Carbon::parse($data['movement_date']) : now()
            );

            return [
                'success' => true,
                'message' => 'Stock adjustment recorded successfully',
                'movement' => $movement,
                'product' => $product->fresh(),
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

            $transferQuantity = $data['quantity'];
            
            if ($productFrom->quantity < $transferQuantity) {
                return [
                    'success' => false,
                    'message' => 'Insufficient stock in source product',
                ];
            }

            // Get quantities before transfer
            $quantityBeforeFrom = $productFrom->quantity;
            $quantityBeforeTo = $productTo->quantity;
            
            // No need to update product quantities directly - movements handle this

            // Record outbound movement
            $movementOut = StockMovement::recordMovement(
                productId: $data['product_id_from'],
                quantityBefore: $quantityBeforeFrom,
                quantityChange: -$transferQuantity,
                quantityAfter: $quantityBeforeFrom - $transferQuantity,
                movementType: StockMovement::TYPE_TRANSFER_OUT,
                userId: auth()->id(),
                unitCost: $data['unit_cost'] ?? null,
                locationFrom: $data['location_from'] ?? null,
                locationTo: $data['location_to'] ?? null,
                notes: $data['notes'] ?? null,
                reason: $data['reason'] ?? null,
                movementDate: isset($data['movement_date']) ? Carbon::parse($data['movement_date']) : now()
            );

            // Record inbound movement
            $movementIn = StockMovement::recordMovement(
                productId: $data['product_id_to'],
                quantityBefore: $quantityBeforeTo,
                quantityChange: $transferQuantity,
                quantityAfter: $quantityBeforeTo + $transferQuantity,
                movementType: StockMovement::TYPE_TRANSFER_IN,
                userId: auth()->id(),
                unitCost: $data['unit_cost'] ?? null,
                locationFrom: $data['location_from'] ?? null,
                locationTo: $data['location_to'] ?? null,
                notes: $data['notes'] ?? null,
                reason: $data['reason'] ?? null,
                movementDate: isset($data['movement_date']) ? Carbon::parse($data['movement_date']) : now()
            );

            return [
                'success' => true,
                'message' => 'Stock transfer completed successfully',
                'movements' => [$movementOut, $movementIn],
                'product_from' => $productFrom->fresh(),
                'product_to' => $productTo->fresh(),
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

            $wasteQuantity = $data['quantity'];
            
            if ($product->quantity < $wasteQuantity) {
                return [
                    'success' => false,
                    'message' => 'Insufficient stock available',
                ];
            }

            $quantityBefore = $product->quantity;
            $quantityAfter = $quantityBefore - $wasteQuantity;
            
            // No need to update product quantity directly - movement handles this

            // Record the movement
            $movement = StockMovement::recordMovement(
                productId: $data['product_id'],
                quantityBefore: $quantityBefore,
                quantityChange: -$wasteQuantity,
                quantityAfter: $quantityAfter,
                movementType: StockMovement::TYPE_WASTE,
                userId: auth()->id(),
                unitCost: $data['unit_cost'] ?? null,
                notes: $data['notes'] ?? null,
                reason: $data['reason'] ?? 'Waste/Damaged goods',
                movementDate: isset($data['movement_date']) ? Carbon::parse($data['movement_date']) : now()
            );

            return [
                'success' => true,
                'message' => 'Waste/damage recorded successfully',
                'movement' => $movement,
                'product' => $product->fresh(),
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
        
        $lowStockProducts = Product::needsReordering(10);
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
