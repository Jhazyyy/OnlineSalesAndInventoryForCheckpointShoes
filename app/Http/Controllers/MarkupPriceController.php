<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class MarkupPriceController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display the markup price management page
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by brand
        if ($request->has('brand') && $request->brand) {
            $query->where('product_brand', 'like', '%'.$request->brand.'%');
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('product_category', 'like', '%'.$request->category.'%');
        }

        // Filter by price source
        if ($request->has('price_source') && $request->price_source) {
            $query->where('price_source', $request->price_source);
        }

        // Filter by markup status
        if ($request->has('markup_status')) {
            if ($request->markup_status === 'with_markup') {
                $query->whereNotNull('markup_percentage')->where('markup_percentage', '>', 0);
            } elseif ($request->markup_status === 'without_markup') {
                $query->where(function($q) {
                    $q->whereNull('markup_percentage')
                      ->orWhere('markup_percentage', '<=', 0);
                });
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'product_name');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->paginate(20)->withQueryString();

        // Get unique brands and categories for filters
        $brands = Product::distinct()->pluck('product_brand')->filter()->sort()->values();
        $categories = Product::distinct()->pluck('product_category')->filter()->sort()->values();

        return view('master_data.markup_prices.index', compact('products', 'brands', 'categories'));
    }

    /**
     * Update markup price for a product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'markup_percentage' => 'nullable|numeric|min:0|max:1000',
            'price_source' => 'required|in:manual,markup,costing',
            'manual_price' => 'nullable|numeric|min:0',
        ]);

        // Enable costing fields temporarily to update markup fields
        $product->enableCostingFields();

        $product->markup_percentage = $validated['markup_percentage'] ?? null;
        $product->price_source = $validated['price_source'];

        // Update the main price based on price source
        if ($validated['price_source'] === 'manual') {
            // Manual: Use the provided price or keep existing
            $product->price = $validated['manual_price'] ?? $product->price;
            $product->markup_price = null; // Clear markup price for manual pricing
            
        } elseif ($validated['price_source'] === 'markup') {
            // Markup: Calculate from cost + markup percentage
            if (!$product->total_cost) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot use markup pricing: Product has no cost set. Please set product cost first.',
                ], 400);
            }
            
            if (!$validated['markup_percentage']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Markup percentage is required when using markup pricing.',
                ], 400);
            }
            
            // Calculate and apply markup price
            $product->markup_price = $product->calculateMarkupPrice();
            $product->price = $product->markup_price;
            
        } elseif ($validated['price_source'] === 'costing') {
            // Costing: Calculate from cost + profit margin
            if (!$product->total_cost) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot use costing pricing: Product has no cost set. Please set product cost first.',
                ], 400);
            }
            
            $margin = $product->profit_margin ?? 30; // Default 30% margin
            $product->price = $product->total_cost * (1 + ($margin / 100));
            $product->markup_price = null; // Clear markup price for costing pricing
        }

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'Markup price updated successfully',
            'product' => [
                'id' => $product->product_id,
                'markup_percentage' => $product->markup_percentage,
                'markup_price' => $product->markup_price,
                'price' => $product->price,
                'price_source' => $product->price_source,
                'total_cost' => $product->total_cost,
                'effective_price' => $product->getEffectivePrice(),
            ]
        ]);
    }

    /**
     * Bulk update markup prices
     */
    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,product_id',
            'markup_percentage' => 'nullable|numeric|min:0|max:1000',
            'price_source' => 'required|in:manual,markup,costing',
        ]);

        $updated = 0;
        $failed = 0;

        foreach ($validated['product_ids'] as $productId) {
            try {
                $product = Product::find($productId);
                
                if (!$product) {
                    $failed++;
                    continue;
                }

                $product->enableCostingFields();
                $product->markup_percentage = $validated['markup_percentage'] ?? null;
                $product->price_source = $validated['price_source'];

                // Apply pricing based on source
                if ($validated['price_source'] === 'manual') {
                    // Keep existing price for manual
                    $product->markup_price = null;
                    
                } elseif ($validated['price_source'] === 'markup') {
                    // Require cost and markup percentage
                    if (!$product->total_cost) {
                        $failed++;
                        continue; // Skip products without cost
                    }
                    
                    if (!$validated['markup_percentage']) {
                        $failed++;
                        continue; // Skip if no markup percentage provided
                    }
                    
                    $product->markup_price = $product->calculateMarkupPrice();
                    $product->price = $product->markup_price;
                    
                } elseif ($validated['price_source'] === 'costing') {
                    // Require cost for costing pricing
                    if (!$product->total_cost) {
                        $failed++;
                        continue; // Skip products without cost
                    }
                    
                    $margin = $product->profit_margin ?? 30;
                    $product->price = $product->total_cost * (1 + ($margin / 100));
                    $product->markup_price = null;
                }

                $product->save();
                $updated++;
            } catch (\Exception $e) {
                $failed++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => "Updated {$updated} products successfully" . ($failed > 0 ? ", {$failed} failed" : ""),
            'updated' => $updated,
            'failed' => $failed,
        ]);
    }

    /**
     * Calculate preview of markup price
     */
    public function preview(Request $request, Product $product)
    {
        $markupPercentage = $request->input('markup_percentage', 0);
        $priceSource = $request->input('price_source', 'manual');

        $preview = [
            'current_price' => $product->price,
            'total_cost' => $product->total_cost,
            'current_markup_percentage' => $product->markup_percentage,
            'current_price_source' => $product->price_source,
        ];

        if ($markupPercentage && $product->total_cost) {
            $calculatedMarkupPrice = $product->total_cost * (1 + ($markupPercentage / 100));
            $preview['new_markup_price'] = round($calculatedMarkupPrice, 2);
            $preview['new_markup_percentage'] = $markupPercentage;
            
            if ($priceSource === 'markup') {
                $preview['new_selling_price'] = round($calculatedMarkupPrice, 2);
                $preview['price_change'] = round($calculatedMarkupPrice - $product->price, 2);
                $preview['price_change_percentage'] = $product->price > 0 
                    ? round((($calculatedMarkupPrice - $product->price) / $product->price) * 100, 2)
                    : 0;
            }
        }

        if ($priceSource === 'costing' && $product->total_cost) {
            $margin = $product->profit_margin ?? 30;
            $costBasedPrice = $product->total_cost * (1 + ($margin / 100));
            $preview['new_selling_price'] = round($costBasedPrice, 2);
            $preview['profit_margin'] = $margin;
            $preview['price_change'] = round($costBasedPrice - $product->price, 2);
        }

        return response()->json($preview);
    }
}
