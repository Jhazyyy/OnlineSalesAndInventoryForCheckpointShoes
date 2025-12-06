<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\AuditLog;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'adjustment_type' => 'required|in:increase,decrease',
            'reason' => 'required|string|max:255',
            'custom_reason' => 'nullable|required_if:reason,Other|string|max:500',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();

        try {
            $product = Product::findOrFail($validated['product_id']);
            $quantity = $validated['quantity'];
            $adjustmentType = $validated['adjustment_type'];
            
            // Determine the reason
            $reason = $validated['reason'];
            if ($reason === 'Other' && !empty($validated['custom_reason'])) {
                $reason = $validated['custom_reason'];
            }

            // Calculate quantity change (positive for increase, negative for decrease)
            $quantityChange = $adjustmentType === 'increase' ? $quantity : -$quantity;
            
            // Determine movement type based on reason
            $movementType = $this->getMovementType($reason, $adjustmentType);

            // Check if decrease would result in negative stock
            if ($adjustmentType === 'decrease' && ($product->quantity - $quantity) < 0) {
                return redirect()->back()->with('warning', "Adjustment would result in negative stock. Current stock: {$product->quantity}, Requested decrease: {$quantity}");
            }

            // Store old quantity for logging
            $oldQuantity = $product->quantity;

            // Use InventoryService to adjust inventory and sync product quantity properly
            InventoryService::adjust(
                productId: $product->product_id,
                quantityChange: $quantityChange,
                unitCost: null,
                movementType: $movementType,
                referenceType: 'manual_adjustment',
                referenceId: null,
                propertyId: null,
                location: null,
                syncProductQuantity: true
            );

            // Refresh product to get updated quantity
            $product->refresh();

            DB::commit();

            // Log the stock adjustment
            AuditLog::logAction(
                $adjustmentType === 'increase' ? 'stock increase' : 'stock decrease',
                AuditLog::MODULE_INVENTORY,
                "{$adjustmentType} stock for {$product->product_name}: {$quantityChange} units. Reason: {$reason}",
                Product::class,
                $product->product_id,
                $product->product_name,
                ['quantity' => $oldQuantity],
                ['quantity' => $product->quantity, 'reason' => $reason, 'adjustment' => $quantityChange],
                AuditLog::SEVERITY_INFO
            );

            $message = $adjustmentType === 'increase' 
                ? "Successfully increased stock by {$quantity} units. New stock: {$product->quantity}"
                : "Successfully decreased stock by {$quantity} units. New stock: {$product->quantity}";

            return redirect()->back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to adjust stock: ' . $e->getMessage());
        }
    }

    /**
     * Determine the appropriate movement type based on the reason and adjustment type
     */
    private function getMovementType(string $reason, string $adjustmentType): string
    {
        // Map specific reasons to movement types
        $reasonMap = [
            'Damaged' => StockMovement::TYPE_WASTE,
            'Expired' => StockMovement::TYPE_WASTE,
            'Waste' => StockMovement::TYPE_WASTE,
            'Stolen' => StockMovement::TYPE_WASTE,
            'Lost' => StockMovement::TYPE_WASTE,
            'Return from Customer' => StockMovement::TYPE_RETURN,
            'Correction' => StockMovement::TYPE_ADJUSTMENT,
            'Recount' => StockMovement::TYPE_AUDIT,
        ];

        // Check if reason matches a specific movement type
        foreach ($reasonMap as $keyword => $movementType) {
            if (stripos($reason, $keyword) !== false) {
                return $movementType;
            }
        }

        // Default to adjustment or audit based on type
        return $adjustmentType === 'increase' 
            ? StockMovement::TYPE_ADJUSTMENT 
            : StockMovement::TYPE_WASTE;
    }

    /**
     * Display adjustment history for a specific product
     */
    public function history(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);
        
        // Get all stock movements for this product (including sales, purchases, and adjustments)
        $adjustments = StockMovement::where('product_id', $productId)
            ->with(['user', 'product'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Check if AJAX request for modal
        if ($request->ajax()) {
            return view('inventory.stock-adjustments.history-modal', compact('product', 'adjustments'));
        }

        return view('inventory.stock-adjustments.history-modal', compact('product', 'adjustments'));
    }
}
