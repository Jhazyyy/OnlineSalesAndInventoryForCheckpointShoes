<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
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

        // Filter by stock status
        if ($request->has('stock_status') && $request->stock_status) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->inStock();
                    break;
                case 'low_stock':
                    $query->lowStock();
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
            }
        }

        // Price range filters
        if ($request->has('min_price') && $request->min_price) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && $request->max_price) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sortBy = $request->get('sort', 'product_name');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->with('lastSupplier')->paginate(15)->withQueryString();

        // Get unique brands for filter dropdown
        $brands = Product::distinct()->pluck('product_brand')->filter()->sort();

        // Get unique categories for filter dropdown
        $categories = Product::distinct()->pluck('product_category')->filter()->sort();

        return view('master_data.products.index', compact('products', 'brands', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get active brands for dropdown
        $brands = Brand::where('is_active', true)->orderBy('name')->pluck('name', 'name');

        // Get active categories for dropdown
        $categories = Category::where('is_active', true)->orderBy('name')->pluck('name', 'name');

        return view('master_data.products.create', compact('brands', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'property_name' => 'nullable|string|max:255',
            'property_value' => 'nullable|string|max:255',
            'product_category' => 'nullable|string|max:255',
            'custom_category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:1000',
            // Allow either product_brand OR custom_brand to be filled
            'product_brand' => 'nullable|string|max:255',
            'custom_brand' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Determine the brand name to use
        $brandName = $request->product_brand === 'custom' ? $request->custom_brand : $request->product_brand;

        if (! $brandName) {
            return redirect()->back()->withErrors(['product_brand' => 'Please select or enter a brand.'])->withInput();
        }

        // Create brand if it doesn't exist
        $brand = Brand::firstOrCreate(
            ['name' => $brandName],
            [
                'brand_code' => strtoupper(str_replace([' ', '-'], '_', $brandName)),
                'description' => 'Auto-created brand from product: '.$request->product_name,
                'is_active' => true,
            ]
        );

        // Assign brand name
        $data['product_brand'] = $brand->name;

        // Determine the category name to use
        $categoryName = $request->product_category === 'custom' ? $request->custom_category : $request->product_category;

        if (! $categoryName) {
            return redirect()->back()->withErrors(['product_category' => 'Please select or enter a category.'])->withInput();
        }

        // Create category if it doesn't exist
        $category = Category::firstOrCreate(
            ['name' => $categoryName],
            [
                'category_code' => strtoupper(str_replace([' ', '-'], '_', $categoryName)),
                'description' => 'Auto-created category from product: '.$request->product_name,
                'is_active' => true,
            ]
        );

        // Assign category name
        $data['product_category'] = $category->name;

        // Auto-generate SKU if not provided
        if (empty($data['sku'])) {
            // Generate SKU based on brand and product name
            $brandPrefix = strtoupper(substr(str_replace([' ', '-'], '', $brand->name), 0, 3));
            $namePrefix = strtoupper(substr(str_replace([' ', '-'], '', $request->product_name), 0, 3));
            $randomSuffix = strtoupper(substr(md5(uniqid()), 0, 4));
            $data['sku'] = "{$brandPrefix}-{$namePrefix}-{$randomSuffix}";
            
            // Ensure uniqueness
            $counter = 1;
            $originalSku = $data['sku'];
            while (Product::where('sku', $data['sku'])->exists()) {
                $data['sku'] = "{$originalSku}-{$counter}";
                $counter++;
            }
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time().'_'.$image->getClientOriginalName();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        // Create the product
        $product = Product::create($data);

        return redirect()->route('master_data.products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Load relationships for detailed view
        $product->load(['sales', 'purchases', 'returns', 'lastSupplier', 'preferredSupplier']);

        // Calculate additional metrics
        $stockMovement = $product->stock_movement;
        $recentSales = $product->sales()->latest()->take(5)->get();
        $recentPurchases = $product->purchases()->latest()->take(5)->get();

        return view('master_data.products.show', compact('product', 'stockMovement', 'recentSales', 'recentPurchases'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $brands = Brand::pluck('name');  // get brand names as a collection
        $categories = Category::pluck('name');  // get category names as a collection

        return view('master_data.products.edit', compact('product', 'brands', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->product_id . ',product_id',
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->product_id . ',product_id',
            'property_name' => 'nullable|string|max:255',
            'property_value' => 'nullable|string|max:255',
            'product_category' => 'nullable|string|max:255',
            'custom_category' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string|max:1000',
            'product_brand' => 'nullable|string|max:255',
            'custom_brand' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Handle brand selection
        $brandName = $request->product_brand === 'custom' ? $request->custom_brand : $request->product_brand;

        if (! $brandName) {
            return redirect()->back()->withErrors(['product_brand' => 'Please select or enter a brand.'])->withInput();
        }

        // Create brand if not existing
        $brand = Brand::firstOrCreate(
            ['name' => $brandName],
            [
                'brand_code' => strtoupper(str_replace([' ', '-'], '_', $brandName)),
                'description' => 'Auto-created brand from product: '.$request->product_name,
                'is_active' => true,
            ]
        );

        // Assign the brand name
        $data['product_brand'] = $brand->name;

        // Handle category selection
        $categoryName = $request->product_category === 'custom' ? $request->custom_category : $request->product_category;

        if (! $categoryName) {
            return redirect()->back()->withErrors(['product_category' => 'Please select or enter a category.'])->withInput();
        }

        // Create category if not existing
        $category = Category::firstOrCreate(
            ['name' => $categoryName],
            [
                'category_code' => strtoupper(str_replace([' ', '-'], '_', $categoryName)),
                'description' => 'Auto-created category from product: '.$request->product_name,
                'is_active' => true,
            ]
        );

        // Assign the category name
        $data['product_category'] = $category->name;

        // Handle image upload
        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $image = $request->file('image');
            $imageName = time().'_'.$image->getClientOriginalName();
            $imagePath = $image->storeAs('products', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);

        return redirect()->route('master_data.products.index')->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        // Delete associated image
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('master_data.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('master_data.products.import');
    }

    /**
     * Import products from Excel file
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120', // Max 5MB
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $import = new ProductsImport;
            $import->import($request->file('excel_file'));

            $importedCount = $import->getRowCount();

            return redirect()->route('master_data.products.index')
                ->with('success', "Successfully imported {$importedCount} products!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing file: '.$e->getMessage());
        }
    }

    /**
     * Download sample Excel template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="products_template.xlsx"',
        ];

        // Create sample data for template
        $sampleData = [
            ['Product Name', 'Brand', 'Category', 'Quantity', 'Price', 'Description'],
            ['Sample Product 1', 'Sample Brand', 'Sample Category', 100, 29.99, 'Sample description'],
            ['Sample Product 2', 'Another Brand', 'Sample Category', 50, 49.99, 'Another description'],
        ];

        $callback = function () use ($sampleData) {
            $file = fopen('php://output', 'w');

            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Bulk update stock quantities
     */
    public function bulkUpdateStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'updates' => 'required|array',
            'updates.*.product_id' => 'required|exists:products,product_id',
            'updates.*.quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $updates = [];
        foreach ($request->updates as $update) {
            $updates[$update['product_id']] = $update['quantity'];
        }

        $results = Product::bulkUpdateStock($updates);
        $successCount = array_sum(array_map('intval', $results));

        return response()->json([
            'success' => true,
            'message' => "Updated stock for {$successCount} products",
            'results' => $results,
        ]);
    }

    /**
     * Get products that need attention (low stock, etc.)
     */
    public function getAlertsData()
    {
        $lowStockProducts = Product::needsReordering(10);
        $outOfStockProducts = Product::outOfStock();

        return response()->json([
            // 'low_stock_count' => $lowStockProducts->count(),
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_count' => $outOfStockProducts->count(),
            'out_of_stock_products' => $outOfStockProducts,
            'total_products' => Product::count(),
            'total_inventory_value' => Product::totalInventoryValue(),
        ]);
    }
}
