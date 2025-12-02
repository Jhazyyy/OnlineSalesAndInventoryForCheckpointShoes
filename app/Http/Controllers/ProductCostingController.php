<?php

namespace App\Http\Controllers;

use App\Services\ProductCostingService;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ProductCostingController extends Controller
{
    protected ProductCostingService $costingService;

    public function __construct(ProductCostingService $costingService)
    {
        $this->costingService = $costingService;
    }

    /**
     * Display product costing dashboard
     */
    public function index(Request $request): View
    {
        $stats = $this->costingService->getCostingStatistics();
        
        $search = $request->get('search');
        $filter = $request->get('filter', 'all'); // all, no-costing, low-margin, negative-margin
        
        $query = Product::query();
        
        // Apply filters
        switch ($filter) {
            case 'no-costing':
                $query->whereNull('total_cost');
                break;
            case 'low-margin':
                $query->whereNotNull('profit_margin')
                      ->where('profit_margin', '<', 20)
                      ->where('profit_margin', '>=', 0);
                break;
            case 'negative-margin':
                $query->whereNotNull('profit_margin')
                      ->where('profit_margin', '<', 0);
                break;
            case 'with-costing':
                $query->whereNotNull('total_cost');
                break;
            case 'with-price':
                $query->whereNotNull('price')
                      ->where('price', '>', 0);
                break;
            case 'no-price':
                $query->where(function($q) {
                    $q->whereNull('price')
                      ->orWhere('price', '=', 0);
                });
                break;
        }
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('product_name', 'like', "%{$search}%")
                  ->orWhere('product_brand', 'like', "%{$search}%")
                  ->orWhere('product_category', 'like', "%{$search}%");
            });
        }
        
        $products = $query->orderBy('product_name')->paginate(10)->appends($request->query());
        
        return view('inventory.product-costing.index', compact('stats', 'products', 'filter'));
    }

    /**
     * Show edit form for product costing
     */
    public function edit(Product $product): View
    {
        $breakdown = $this->costingService->getCostBreakdown($product);
        $pricesuggestion = $this->costingService->suggestOptimalPrice($product, 30);
        
        return view('inventory.product-costing.edit', compact('product', 'breakdown', 'pricesuggestion'));
    }

    /**
     * Update product costing
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'raw_material_cost' => 'nullable|numeric|min:0',
            'labor_cost' => 'nullable|numeric|min:0',
            'overhead_cost' => 'nullable|numeric|min:0',
            'shipping_cost_per_unit' => 'nullable|numeric|min:0',
            'tax_amount_per_unit' => 'nullable|numeric|min:0',
            'handling_cost' => 'nullable|numeric|min:0',
            'cost_calculation_method' => 'nullable|string',
            'cost_notes' => 'nullable|string',
        ]);

        try {
            $this->costingService->updateProductCosting($product, $validated);
            
            // Check if request is from inventory products page (AJAX/modal)
            if ($request->wantsJson() || $request->ajax()) {
                return redirect()->route('inventory.products.index')->with('success', 
                    "Costing updated for {$product->product_name}. Total Cost: ₱" . 
                    number_format($product->fresh()->total_cost, 2)
                );
            }
            
            return redirect()->back()->with('success', 
                "Costing updated for {$product->product_name}. Total Cost: ₱" . 
                number_format($product->fresh()->total_cost, 2)
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error updating costing: ' . $e->getMessage());
        }
    }

    /**
     * Bulk update costing
     */
    public function bulkUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,product_id',
        ]);

        try {
            $productsData = [];
            foreach ($request->products as $productData) {
                $productsData[$productData['product_id']] = $productData;
            }
            
            $stats = $this->costingService->bulkUpdateCosting($productsData);
            
            return redirect()->back()->with('success', 
                "Bulk update completed! {$stats['updated']} products updated, {$stats['failed']} failed."
            );
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error in bulk update: ' . $e->getMessage());
        }
    }

    /**
     * Get low margin products
     */
    public function lowMargin(Request $request): View
    {
        $threshold = $request->get('threshold', 20);
        $products = $this->costingService->getLowMarginProducts($threshold);
        
        // Calculate stats specific to low margin products
        $stats = [
            'total' => $products->count(),
            'average_margin' => $products->avg('profit_margin') ?? 0,
            'total_value' => $products->sum(function($product) {
                return ($product->price ?? 0) * ($product->quantity ?? 0);
            })
        ];
        
        // Paginate the products
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage($request->get('page', 1), 15),
            $products->count(),
            15,
            $request->get('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('inventory.product-costing.low-margin', compact('products', 'stats', 'threshold'));
    }

    /**
     * Get negative margin products
     */
    public function negativeMargin(Request $request): View
    {
        $products = $this->costingService->getNegativeMarginProducts();
        
        // Calculate stats specific to negative margin products
        $stats = [
            'total' => $products->count(),
            'average_margin' => $products->avg('profit_margin') ?? 0,
            'total_loss' => $products->sum('profit_amount') ?? 0
        ];
        
        // Paginate the products
        $products = new \Illuminate\Pagination\LengthAwarePaginator(
            $products->forPage($request->get('page', 1), 15),
            $products->count(),
            15,
            $request->get('page', 1),
            ['path' => $request->url(), 'query' => $request->query()]
        );
        
        return view('inventory.product-costing.negative-margin', compact('products', 'stats'));
    }

    /**
     * Suggest optimal price (AJAX)
     */
    public function suggestPrice(Request $request, Product $product): JsonResponse
    {
        $desiredMargin = $request->get('desired_margin', 30);
        $suggestion = $this->costingService->suggestOptimalPrice($product, $desiredMargin);
        
        return response()->json($suggestion);
    }

    /**
     * Get cost breakdown (AJAX)
     */
    public function costBreakdown(Product $product): JsonResponse
    {
        $breakdown = $this->costingService->getCostBreakdown($product);
        
        return response()->json($breakdown);
    }

    /**
     * Get costing analytics (AJAX)
     */
    public function analytics(): JsonResponse
    {
        $stats = $this->costingService->getCostingStatistics();
        $lowMarginProducts = $this->costingService->getLowMarginProducts(20);
        $negativeMarginProducts = $this->costingService->getNegativeMarginProducts();
        
        return response()->json([
            'statistics' => $stats,
            'low_margin_products' => $lowMarginProducts,
            'negative_margin_products' => $negativeMarginProducts,
        ]);
    }
}

