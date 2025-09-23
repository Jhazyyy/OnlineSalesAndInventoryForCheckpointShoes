<?php

namespace App\Services;

use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PurchaseReturnService
{
    /**
     * Get paginated purchase returns with filters and search.
     */
    public function getPaginatedReturns(Request $request, int $perPage = 20): LengthAwarePaginator
    {
        $query = PurchaseReturn::with(['supplier', 'purchaseOrder', 'items.product', 'createdBy']);

        // Apply search
        if ($search = $request->get('search')) {
            $query->where(function (Builder $q) use ($search) {
                $q->where('return_number', 'like', "%{$search}%")
                  ->orWhere('reason', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function (Builder $sq) use ($search) {
                      $sq->where('supplier_name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('purchaseOrder', function (Builder $poq) use ($search) {
                      $poq->where('order_number', 'like', "%{$search}%");
                  });
            });
        }

        // Apply filters
        if ($status = $request->get('return_status')) {
            $query->where('return_status', $status);
        }

        if ($supplierId = $request->get('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        if ($purchaseOrderId = $request->get('purchase_order_id')) {
            $query->where('purchase_order_id', $purchaseOrderId);
        }

        if ($reason = $request->get('reason')) {
            $query->where('reason', $reason);
        }

        // Date range filters
        if ($startDate = $request->get('start_date')) {
            $query->where('return_date', '>=', $startDate);
        }

        if ($endDate = $request->get('end_date')) {
            $query->where('return_date', '<=', $endDate);
        }

        // Apply sorting
        $sortField = $request->get('sort', 'return_date');
        $sortOrder = $request->get('order', 'desc');
        
        if (in_array($sortField, ['return_number', 'return_date', 'total_amount', 'return_status', 'created_at'])) {
            $query->orderBy($sortField, $sortOrder);
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Get filter options for purchase return listing.
     */
    public function getFilterOptions(): array
    {
        return [
            'suppliers' => Supplier::where('status', 'active')
                                 ->orderBy('supplier_name')
                                 ->get()
                                 ->map(function ($supplier) {
                                     return [
                                         'id' => $supplier->supplier_id,
                                         'name' => $supplier->supplier_name,
                                     ];
                                 }),
            'purchase_orders' => PurchaseOrder::with('supplier')
                                            ->whereIn('status', ['received', 'partial_received'])
                                            ->orderBy('order_number')
                                            ->get()
                                            ->map(function ($order) {
                                                return [
                                                    'id' => $order->order_id,
                                                    'order_number' => $order->order_number,
                                                    'supplier_name' => $order->supplier->supplier_name,
                                                    'supplier_id' => $order->supplier_id,
                                                ];
                                            }),
            'return_reasons' => [
                'defective' => 'Defective',
                'damaged' => 'Damaged',
                'wrong_item' => 'Wrong Item',
                'overdelivery' => 'Overdelivery',
                'quality_issues' => 'Quality Issues',
                'expired' => 'Expired',
                'other' => 'Other',
            ],
        ];
    }

    /**
     * Create a new purchase return.
     */
    public function createReturn(array $data): PurchaseReturn
    {
        // Set defaults
        $data['return_date'] = $data['return_date'] ?? Carbon::today();
        $data['return_status'] = $data['return_status'] ?? 'pending';
        $data['created_by'] = $data['created_by'] ?? auth()->id();

        // Generate return number if not provided
        if (!isset($data['return_number'])) {
            $data['return_number'] = $this->generateReturnNumber();
        }

        // Get purchase order to auto-fill supplier if not provided
        if (isset($data['purchase_order_id']) && !isset($data['supplier_id'])) {
            $purchaseOrder = PurchaseOrder::find($data['purchase_order_id']);
            if ($purchaseOrder) {
                $data['supplier_id'] = $purchaseOrder->supplier_id;
            }
        }

        // Create the return
        $return = PurchaseReturn::create($data);

        // Add items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            $this->addItemsToReturn($return, $data['items']);
        }

        return $return->fresh(['supplier', 'purchaseOrder', 'items.product', 'createdBy']);
    }

    /**
     * Update a purchase return.
     */
    public function updateReturn(PurchaseReturn $return, array $data): PurchaseReturn
    {
        // Check if return can be updated
        if (!$return->canBeUpdated()) {
            throw new \Exception('Cannot update a purchase return that has been processed or refunded.');
        }

        // Update return details
        $return->update($data);

        // Update items if provided
        if (isset($data['items']) && is_array($data['items'])) {
            $this->updateReturnItems($return, $data['items']);
        }

        return $return->fresh(['supplier', 'purchaseOrder', 'items.product', 'createdBy']);
    }

    /**
     * Add items to a return.
     */
    public function addItemsToReturn(PurchaseReturn $return, array $items): void
    {
        $totalQuantity = 0;
        $totalAmount = 0;

        foreach ($items as $itemData) {
            $product = Product::find($itemData['product_id']);
            
            if (!$product) {
                continue;
            }

            $quantity = $itemData['quantity'] ?? 0;
            $price = $itemData['price'] ?? $product->price;

            $item = PurchaseReturnItem::create([
                'purchase_return_id' => $return->purchase_return_id,
                'product_id' => $product->product_id,
                'quantity' => $quantity,
                'price' => $price,
                'line_total' => $quantity * $price,
                'condition' => $itemData['condition'] ?? 'defective',
                'notes' => $itemData['notes'] ?? null,
            ]);

            // Accumulate totals
            $totalQuantity += $quantity;
            $totalAmount += ($quantity * $price);
        }

        // Update return totals
        $return->update([
            'total_quantity' => $totalQuantity,
            'total_amount' => $totalAmount,
        ]);
    }

    /**
     * Update return items.
     */
    public function updateReturnItems(PurchaseReturn $return, array $items): void
    {
        // Delete existing items (this also reverses any inventory changes)
        $this->deleteReturnItems($return);

        // Add new items
        $this->addItemsToReturn($return, $items);
    }

    /**
     * Delete return items.
     */
    protected function deleteReturnItems(PurchaseReturn $return): void
    {
        // First reverse any inventory changes if already processed
        if ($return->return_status === 'processed') {
            foreach ($return->items as $item) {
                $this->reverseInventoryChanges($item);
            }
        }

        // Then delete the items
        $return->items()->delete();
    }

    /**
     * Delete a purchase return.
     */
    public function deleteReturn(PurchaseReturn $return): bool
    {
        // Check if return can be deleted
        if (!$return->canBeDeleted()) {
            throw new \Exception('Cannot delete a purchase return that has been processed or refunded.');
        }

        // Delete return items
        $this->deleteReturnItems($return);

        return $return->delete();
    }

    /**
     * Approve a purchase return.
     */
    public function approveReturn(PurchaseReturn $return): PurchaseReturn
    {
        if ($return->return_status !== 'pending') {
            throw new \Exception('Only pending returns can be approved.');
        }

        $return->update([
            'return_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => Carbon::now(),
        ]);

        return $return->fresh();
    }

    /**
     * Reject a purchase return.
     */
    public function rejectReturn(PurchaseReturn $return, string $reason = null): PurchaseReturn
    {
        if ($return->return_status !== 'pending') {
            throw new \Exception('Only pending returns can be rejected.');
        }

        $return->update([
            'return_status' => 'rejected',
            'rejection_reason' => $reason,
            'rejected_by' => auth()->id(),
            'rejected_at' => Carbon::now(),
        ]);

        return $return->fresh();
    }

    /**
     * Process a purchase return (handle inventory and supplier account updates).
     */
    public function processReturn(PurchaseReturn $return): PurchaseReturn
    {
        if ($return->return_status !== 'approved') {
            throw new \Exception('Only approved returns can be processed.');
        }

        // Process each item
        foreach ($return->items as $item) {
            $this->processReturnItem($item, $return);
        }

        // Update return status
        $return->update([
            'return_status' => 'processed',
            'processed_by' => auth()->id(),
            'processed_at' => Carbon::now(),
        ]);

        return $return->fresh();
    }

    /**
     * Process a single return item (update inventory).
     */
    protected function processReturnItem(PurchaseReturnItem $item, PurchaseReturn $return): void
    {
        $product = $item->product;
        $quantityToReduce = $item->quantity;

        // Reduce inventory (outbound movement)
        $previousQuantity = $product->quantity;
        $product->decrement('quantity', $quantityToReduce);

        // Create stock movement record
        if (class_exists('App\Models\StockMovement')) {
            $movementNotes = "Purchase return to {$return->supplier->supplier_name} - Return #{$return->return_number}";
            if ($return->reason) {
                $movementNotes .= " (Reason: {$return->reason})";
            }

            \App\Models\StockMovement::recordMovement(
                productId: $product->product_id,
                quantityBefore: $previousQuantity,
                quantityChange: -$quantityToReduce, // Negative because it's outbound
                quantityAfter: $product->quantity,
                movementType: \App\Models\StockMovement::TYPE_RETURN_OUT,
                userId: auth()->id(),
                referenceType: 'purchase_return',
                referenceId: $return->purchase_return_id,
                notes: $movementNotes,
                movementDate: Carbon::now()
            );
        }
    }

    /**
     * Reverse inventory changes for a return item.
     */
    protected function reverseInventoryChanges(PurchaseReturnItem $item): void
    {
        $product = $item->product;
        $quantityToRestore = $item->quantity;

        // Restore inventory
        $previousQuantity = $product->quantity;
        $product->increment('quantity', $quantityToRestore);

        // Create reverse stock movement record
        if (class_exists('App\Models\StockMovement')) {
            \App\Models\StockMovement::recordMovement(
                productId: $product->product_id,
                quantityBefore: $previousQuantity,
                quantityChange: $quantityToRestore, // Positive because we're restoring
                quantityAfter: $product->quantity,
                movementType: \App\Models\StockMovement::TYPE_ADJUSTMENT_IN,
                userId: auth()->id(),
                referenceType: 'purchase_return_reversal',
                referenceId: null,
                notes: "Inventory restoration due to purchase return reversal",
                movementDate: Carbon::now()
            );
        }
    }

    /**
     * Mark return as refunded.
     */
    public function markAsRefunded(PurchaseReturn $return, float $refundAmount = null): PurchaseReturn
    {
        if ($return->return_status !== 'processed') {
            throw new \Exception('Only processed returns can be marked as refunded.');
        }

        $return->update([
            'return_status' => 'refunded',
            'refund_amount' => $refundAmount ?? $return->total_amount,
            'refunded_by' => auth()->id(),
            'refunded_at' => Carbon::now(),
        ]);

        return $return->fresh();
    }

    /**
     * Bulk approve returns.
     */
    public function bulkApprove(array $returnIds): int
    {
        $approved = 0;
        
        foreach ($returnIds as $id) {
            $return = PurchaseReturn::find($id);
            if ($return && $return->return_status === 'pending') {
                $this->approveReturn($return);
                $approved++;
            }
        }

        return $approved;
    }

    /**
     * Bulk reject returns.
     */
    public function bulkReject(array $returnIds, string $reason = null): int
    {
        $rejected = 0;
        
        foreach ($returnIds as $id) {
            $return = PurchaseReturn::find($id);
            if ($return && $return->return_status === 'pending') {
                $this->rejectReturn($return, $reason);
                $rejected++;
            }
        }

        return $rejected;
    }

    /**
     * Generate a unique return number.
     */
    protected function generateReturnNumber(): string
    {
        $prefix = 'PR-';
        $date = Carbon::now()->format('Ymd');
        
        // Get the last return number for today
        $lastReturn = PurchaseReturn::where('return_number', 'like', $prefix . $date . '%')
                                   ->orderBy('return_number', 'desc')
                                   ->first();

        if ($lastReturn) {
            // Extract the sequence number and increment
            $lastSequence = (int) substr($lastReturn->return_number, -4);
            $sequence = str_pad($lastSequence + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $sequence = '0001';
        }

        return $prefix . $date . '-' . $sequence;
    }

    /**
     * Get analytics data for purchase returns.
     */
    public function getAnalytics(array $filters = []): array
    {
        $query = PurchaseReturn::query();

        // Apply date filters
        if (isset($filters['start_date'])) {
            $query->where('return_date', '>=', $filters['start_date']);
        }
        
        if (isset($filters['end_date'])) {
            $query->where('return_date', '<=', $filters['end_date']);
        }

        // Basic statistics
        $totalReturns = $query->count();
        $pendingReturns = $query->clone()->pending()->count();
        $approvedReturns = $query->clone()->approved()->count();
        $processedReturns = $query->clone()->where('return_status', 'processed')->count();
        $rejectedReturns = $query->clone()->where('return_status', 'rejected')->count();
        $refundedReturns = $query->clone()->where('return_status', 'refunded')->count();

        // Recent activity (last 30 days)
        $recentReturns = PurchaseReturn::where('created_at', '>=', Carbon::now()->subDays(30))->count();

        // Financial metrics
        $totalReturnValue = $query->clone()->whereIn('return_status', ['processed', 'refunded'])->sum('total_amount');
        $totalRefunded = $query->clone()->where('return_status', 'refunded')->sum('refund_amount');
        $averageReturnValue = $query->clone()->avg('total_amount') ?? 0;

        // Return trends (last 12 months)
        $returnTrend = collect();
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $count = PurchaseReturn::whereYear('created_at', $date->year)
                                  ->whereMonth('created_at', $date->month)
                                  ->count();
            $value = PurchaseReturn::whereYear('created_at', $date->year)
                                  ->whereMonth('created_at', $date->month)
                                  ->whereIn('return_status', ['processed', 'refunded'])
                                  ->sum('total_amount');
            
            $returnTrend->push([
                'month' => $date->format('M Y'),
                'returns' => $count,
                'value' => $value
            ]);
        }

        // Top reasons for returns
        $topReasons = PurchaseReturn::selectRaw('reason, COUNT(*) as count, SUM(total_amount) as total_value')
                                   ->groupBy('reason')
                                   ->orderByDesc('count')
                                   ->limit(10)
                                   ->get();

        // Suppliers with most returns
        $topSuppliersWithReturns = PurchaseReturn::with(['supplier'])
                                                ->selectRaw('supplier_id, COUNT(*) as return_count, SUM(total_amount) as total_returned')
                                                ->groupBy('supplier_id')
                                                ->orderByDesc('return_count')
                                                ->limit(10)
                                                ->get();

        return [
            'summary' => [
                'total_returns' => $totalReturns,
                'pending_returns' => $pendingReturns,
                'approved_returns' => $approvedReturns,
                'processed_returns' => $processedReturns,
                'rejected_returns' => $rejectedReturns,
                'refunded_returns' => $refundedReturns,
                'recent_returns' => $recentReturns,
                'total_return_value' => $totalReturnValue,
                'total_refunded' => $totalRefunded,
                'average_return_value' => round($averageReturnValue, 2),
            ],
            'return_trend' => $returnTrend,
            'top_reasons' => $topReasons,
            'top_suppliers_with_returns' => $topSuppliersWithReturns,
        ];
    }

    /**
     * Get returns requiring attention.
     */
    public function getReturnsRequiringAttention(): Collection
    {
        return PurchaseReturn::pending()
                           ->where('created_at', '<=', Carbon::now()->subDays(2)) // Older than 2 days
                           ->with(['supplier', 'createdBy'])
                           ->get();
    }

    /**
     * Get supplier return history.
     */
    public function getSupplierReturnHistory(Supplier $supplier, int $perPage = 15): LengthAwarePaginator
    {
        return $supplier->purchaseReturns()
                       ->with(['items.product', 'createdBy'])
                       ->latest('created_at')
                       ->paginate($perPage);
    }

    /**
     * Get valid status transitions.
     */
    public function getValidStatusTransitions(string $currentStatus): array
    {
        return match($currentStatus) {
            'pending' => ['approved', 'rejected'],
            'approved' => ['processed'],
            'processed' => ['refunded'],
            'rejected' => [], // No transitions from rejected
            'refunded' => [], // No transitions from refunded
            default => [],
        };
    }
}