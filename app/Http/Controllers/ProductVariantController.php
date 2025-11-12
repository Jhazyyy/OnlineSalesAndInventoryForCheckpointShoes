<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductVariantController extends Controller
{
    /**
     * Display a listing of variants for a product.
     */
    public function index(Product $product)
    {
        $variants = $product->variants()
            ->orderBy('color')
            ->orderBy('size')
            ->paginate(20);

        return view('master_data.products.variants.index', compact('product', 'variants'));
    }

    /**
     * Show the form for creating a new variant.
     */
    public function create(Product $product)
    {
        // Get existing variant options for suggestions
        $existingColors = $product->variants()->distinct()->pluck('color')->filter()->sort();
        $existingSizes = $product->variants()->distinct()->pluck('size')->filter()->sort();
        $existingMaterials = $product->variants()->distinct()->pluck('material')->filter()->sort();

        return view('master_data.products.variants.create', compact(
            'product',
            'existingColors',
            'existingSizes',
            'existingMaterials'
        ));
    }

    /**
     * Store a newly created variant in storage.
     */
    public function store(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'variant_sku' => 'required|string|max:255|unique:product_variants,variant_sku',
            'variant_name' => 'required|string|max:255',
            'color' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'price_adjustment' => 'nullable|numeric',
            'barcode' => 'nullable|string|max:255|unique:product_variants,barcode',
            'quantity' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'critical_level' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('image');
        $data['parent_product_id'] = $product->product_id;
        $data['is_active'] = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('variants', 'public');
            $data['image'] = $imagePath;
        }

        // Create variant using product method (which marks product as parent)
        $variant = $product->createVariant($data);

        return redirect()
            ->route('master_data.products.variants.index', $product)
            ->with('success', 'Variant created successfully!');
    }

    /**
     * Show the form for editing the specified variant.
     */
    public function edit(Product $product, ProductVariant $variant)
    {
        // Ensure variant belongs to product
        if ($variant->parent_product_id !== $product->product_id) {
            abort(404);
        }

        // Get existing variant options for suggestions
        $existingColors = $product->variants()->distinct()->pluck('color')->filter()->sort();
        $existingSizes = $product->variants()->distinct()->pluck('size')->filter()->sort();
        $existingMaterials = $product->variants()->distinct()->pluck('material')->filter()->sort();

        return view('master_data.products.variants.edit', compact(
            'product',
            'variant',
            'existingColors',
            'existingSizes',
            'existingMaterials'
        ));
    }

    /**
     * Update the specified variant in storage.
     */
    public function update(Request $request, Product $product, ProductVariant $variant)
    {
        // Ensure variant belongs to product
        if ($variant->parent_product_id !== $product->product_id) {
            abort(404);
        }

        $validator = Validator::make($request->all(), [
            'variant_sku' => 'required|string|max:255|unique:product_variants,variant_sku,' . $variant->variant_id . ',variant_id',
            'variant_name' => 'required|string|max:255',
            'color' => 'nullable|string|max:255',
            'size' => 'nullable|string|max:255',
            'material' => 'nullable|string|max:255',
            'price_adjustment' => 'nullable|numeric',
            'barcode' => 'nullable|string|max:255|unique:product_variants,barcode,' . $variant->variant_id . ',variant_id',
            'quantity' => 'nullable|integer|min:0',
            'reorder_level' => 'nullable|integer|min:0',
            'critical_level' => 'nullable|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('image');
        $data['is_active'] = $request->has('is_active');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($variant->image) {
                Storage::disk('public')->delete($variant->image);
            }
            $imagePath = $request->file('image')->store('variants', 'public');
            $data['image'] = $imagePath;
        }

        $variant->update($data);

        return redirect()
            ->route('master_data.products.variants.index', $product)
            ->with('success', 'Variant updated successfully!');
    }

    /**
     * Remove the specified variant from storage.
     */
    public function destroy(Product $product, ProductVariant $variant)
    {
        // Ensure variant belongs to product
        if ($variant->parent_product_id !== $product->product_id) {
            abort(404);
        }

        // Delete image if exists
        if ($variant->image) {
            Storage::disk('public')->delete($variant->image);
        }

        $variant->delete();

        // If no variants left, update product
        if ($product->variants()->count() === 0) {
            $product->update(['has_variants' => false, 'is_parent' => false]);
        }

        return redirect()
            ->route('master_data.products.variants.index', $product)
            ->with('success', 'Variant deleted successfully!');
    }

    /**
     * Toggle variant active status.
     */
    public function toggleStatus(Product $product, ProductVariant $variant)
    {
        // Ensure variant belongs to product
        if ($variant->parent_product_id !== $product->product_id) {
            abort(404);
        }

        $variant->update(['is_active' => !$variant->is_active]);

        return redirect()->back()->with('success', 'Variant status updated successfully!');
    }
}
