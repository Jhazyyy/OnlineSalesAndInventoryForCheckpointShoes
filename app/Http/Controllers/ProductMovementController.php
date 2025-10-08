<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ProductMovementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductMovementController extends Controller
{
    protected ProductMovementService $movementService;

    public function __construct(ProductMovementService $movementService)
    {
        $this->movementService = $movementService;
    }

    /**
     * Display product movement dashboard
     */
    public function index(Request $request): View
    {
        $stats = $this->movementService->getMovementStatistics();

        // Get filtered products
        $category = $request->get('category', 'all');
        $search = $request->get('search');

        $query = Product::query();

        if ($category !== 'all') {
            $query->where('movement_category', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                    ->orWhere('product_brand', 'like', "%{$search}%")
                    ->orWhere('product_category', 'like', "%{$search}%");
            });
        }

        $products = $query->orderBy('movement_velocity', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('inventory.product-movement.index', compact('stats', 'products', 'category'));
    }

    /**
     * Calculate movement for all products
     */
    public function calculateMovements(Request $request): RedirectResponse
    {
        $days = $request->get('days', 90);

        try {
            $stats = $this->movementService->calculateAllProductMovements($days);

            return redirect()->back()->with('success',
                "Movement analysis completed! {$stats['updated']} products analyzed: ".
                "{$stats['fast_moving']} fast-moving, {$stats['slow_moving']} slow-moving, ".
                "{$stats['non_moving']} non-moving."
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error calculating movements: '.$e->getMessage());
        }
    }

    /**
     * Calculate movement for single product
     */
    public function calculateSingleMovement(Product $product): RedirectResponse
    {
        try {
            $this->movementService->calculateProductMovement($product);

            return redirect()->back()->with('success',
                "Movement calculated for {$product->product_name}. Category: {$product->movement_category}"
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error calculating movement: '.$e->getMessage());
        }
    }

    /**
     * Display fast moving products
     */
    public function fastMoving(Request $request): View
    {
        $products = $this->movementService->getProductsByCategory('fast', 15);
        $stats = $this->movementService->getMovementStatistics();

        return view('inventory.product-movement.fast-moving', compact('products', 'stats'));
    }

    /**
     * Display slow moving products
     */
    public function slowMoving(Request $request): View
    {
        $products = $this->movementService->getProductsByCategory('slow', 15);
        $stats = $this->movementService->getMovementStatistics();

        return view('inventory.product-movement.slow-moving', compact('products', 'stats'));
    }

    /**
     * Display non-moving products
     */
    public function nonMoving(Request $request): View
    {
        $products = $this->movementService->getProductsByCategory('non-moving', 15);
        $stats = $this->movementService->getMovementStatistics();

        return view('inventory.product-movement.non-moving', compact('products', 'stats'));
    }

    /**
     * Display promotional products
     */
    public function promotional(Request $request): View
    {
        $products = $this->movementService->getPromotionalProducts(15);
        $stats = $this->movementService->getMovementStatistics();

        return view('inventory.product-movement.promotional', compact('products', 'stats'));
    }

    /**
     * Mark products for promotion
     */
    public function markForPromotion(Request $request): RedirectResponse
    {
        $request->validate([
            'product_ids' => 'nullable|array',
            'product_ids.*' => 'exists:products,product_id',
            'criteria' => 'nullable|array',
        ]);

        try {
            if ($request->has('product_ids')) {
                // Mark specific products
                $count = 0;
                foreach ($request->product_ids as $productId) {
                    $product = Product::find($productId);
                    if ($product && ! $product->is_promotional) {
                        $reason = 'Manually selected for promotion';
                        $product->update([
                            'is_promotional' => true,
                            'promotional_reason' => $reason,
                        ]);
                        $count++;
                    }
                }
            } else {
                // Use criteria to auto-select
                $count = $this->movementService->markProductsForPromotion($request->criteria ?? []);
            }

            return redirect()->back()->with('success', "{$count} product(s) marked for promotion.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error marking products: '.$e->getMessage());
        }
    }

    /**
     * Unmark products from promotion
     */
    public function unmarkFromPromotion(Request $request): RedirectResponse
    {
        $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,product_id',
        ]);

        try {
            $count = $this->movementService->unmarkPromotionalProducts($request->product_ids);

            return redirect()->back()->with('success', "{$count} product(s) removed from promotions.");
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error unmarking products: '.$e->getMessage());
        }
    }

    /**
     * Get movement analytics data (JSON API)
     */
    public function analytics(): JsonResponse
    {
        $stats = $this->movementService->getMovementStatistics();
        $topFastMoving = $this->movementService->getTopFastMovingProducts(10);
        $needsAttention = $this->movementService->getProductsNeedingAttention(10);

        return response()->json([
            'statistics' => $stats,
            'top_fast_moving' => $topFastMoving,
            'needs_attention' => $needsAttention,
        ]);
    }

    /**
     * Export movement data
     */
    public function export(Request $request)
    {
        // This will be implemented when we add the reporting module
        return redirect()->back()->with('info', 'Export feature will be available in the Reports module.');
    }
}
