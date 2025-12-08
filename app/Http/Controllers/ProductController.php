<?php

namespace App\Http\Controllers;

use App\Imports\ProductsImport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\StockName;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    use AuthorizesRequests;

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

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('product_category', 'like', '%'.$request->category.'%');
        }

        // Filter by stock name
        if ($request->has('stock_names') && $request->stock_names) {
            $query->where('stock_name', 'like', '%'.$request->stock_names.'%');
        }

        // Filter by preferred supplier
        if ($request->has('preferred_supplier_id') && $request->preferred_supplier_id) {
            $query->where('preferred_supplier_id', $request->preferred_supplier_id);
        }

        //Filter by last supplier
        if ($request->has('last_supplier_id') && $request->last_supplier_id) {
            $query->where('last_supplier_id', $request->last_supplier_id);
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
                case 'critical_stock':
                    $query->criticalStock();
                    break;
                case 'out_of_stock':
                    $query->outOfStock();
                    break;
            }
        }

        // Sorting
        $sortBy = $request->get('sort', 'updated_at');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $products = $query->with('lastSupplier')->paginate(10)->withQueryString();

        // Get unique stock names for filter dropdown
        $stockNames = StockName::where('is_active', true)->orderBy('name')->pluck('name')->filter()->sort()->values();

        // Get unique brands for filter dropdown
        $brands = Product::distinct()->pluck('product_brand')->filter()->sort()->values();

        // Get unique categories for filter dropdown
        $categories = Product::distinct()->pluck('product_category')->filter()->sort()->values();

        // Get active suppliers for dropdown
        $suppliers = \App\Models\Supplier::where('status', 'active')->orderBy('supplier_name')->get();

        // Get active markup prices for dropdown
        $markupPrices = \App\Models\MarkupPrice::where('is_active', true)->orderBy('name')->get();

        return view('inventory.products.index', compact('products', 'stockNames', 'brands', 'categories', 'suppliers', 'markupPrices'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create products');

        // Get active stock names for dropdown
        $stockNames = StockName::where('is_active', true)->orderBy('name')->pluck('name', 'name');

        // Get active brands for dropdown
        $brands = Brand::where('is_active', true)->orderBy('name')->pluck('name', 'name');

        // Get active categories for dropdown
        $categories = Category::where('is_active', true)->orderBy('name')->pluck('name', 'name');

        // Get active suppliers for dropdown
        $suppliers = \App\Models\Supplier::where('status', 'active')->orderBy('supplier_name')->get();

        // Get active markup prices for dropdown
        $markupPrices = \App\Models\MarkupPrice::where('is_active', true)->orderBy('name')->get();

        return view('inventory.products.create', compact('stockNames', 'brands', 'categories', 'suppliers', 'markupPrices'));
    }

    public function store(Request $request)
    {
        $this->authorize('create products');

        $validator = Validator::make($request->all(), [
            'stock_name' => 'nullable|string|max:255',
            'custom_stock_name' => 'nullable|string|max:255',
            'product_name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'preferred_supplier_id' => 'nullable|exists:suppliers,supplier_id',
            'assigned_suppliers' => 'nullable|array',
            'assigned_suppliers.*.supplier_id' => 'required|exists:suppliers,supplier_id',
            'assigned_suppliers.*.cost' => 'nullable|numeric|min:0',
            'assigned_suppliers.*.is_primary' => 'nullable|boolean',
            'assigned_suppliers.*.notes' => 'nullable|string|max:500',
            'product_category' => 'nullable|string|max:255',
            'custom_category' => 'nullable|string|max:255',
            'price' => 'nullable|numeric|min:0',
            'pricing_method' => 'required|in:manual,costing,markup',
            'markup_price_id' => 'nullable|required_if:pricing_method,markup|exists:markup_prices,id',
            'description' => 'nullable|string|max:1000',
            'image_url' => 'nullable|url|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            // Allow either product_brand OR custom_brand to be filled
            'product_brand' => 'nullable|string|max:255',
            'custom_brand' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Handle stock name selection/creation
        $stockNameId = null;
        if ($request->stock_name) {
            $stockNameValue = $request->stock_name === 'custom' ? $request->custom_stock_name : $request->stock_name;

            if ($stockNameValue) {
                // Create stock name if it doesn't exist
                $stockName = StockName::firstOrCreate(
                    ['name' => $stockNameValue],
                    [
                        'stock_code' => strtoupper(str_replace([' ', '-'], '_', $stockNameValue)),
                        'description' => 'Auto-created stock name from product: '.$request->product_name,
                        'is_active' => true,
                    ]
                );
                $stockNameId = $stockName->id;
                // Keep the string version for backward compatibility
                $data['stock_name'] = $stockName->name;
            }
        }
        $data['stock_name_id'] = $stockNameId;

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

        // Handle image upload or URL
        if ($request->hasFile('image_file')) {
            // Store uploaded file
            $imagePath = $request->file('image_file')->store('products', 'public');
            $data['image'] = $imagePath;
        } elseif ($request->filled('image_url')) {
            // Use provided URL
            $data['image'] = $request->image_url;
        }

        // Auto-generate SKU if not provided
        if (empty($data['sku'])) {
            $stockNamePrefix = strtoupper(substr(str_replace([' ', '-'], '', $stockName->name), 0, 5));
            $namePrefix = strtoupper(substr(str_replace([' ', '-'], '', $request->product_name), 0, 5));
            
            $randomSuffix = strtoupper(substr(md5(uniqid()), 0, 5));
            $data['sku'] = "{$stockNamePrefix}-{$namePrefix}-{$randomSuffix}";

            // Ensure uniqueness
            $counter = 1;
            $originalSku = $data['sku'];
            while (Product::where('sku', $data['sku'])->exists()) {
                $data['sku'] = "{$originalSku}-{$counter}";
                $counter++;
            }
        }

        // Create the product
        $product = Product::create($data);

        // Sync assigned suppliers if provided
        if ($request->has('assigned_suppliers') && is_array($request->assigned_suppliers)) {
            $syncData = [];
            foreach ($request->assigned_suppliers as $supplier) {
                if (!empty($supplier['supplier_id'])) {
                    $syncData[$supplier['supplier_id']] = [
                        'cost' => $supplier['cost'] ?? null,
                        'is_primary' => !empty($supplier['is_primary']),
                        'notes' => $supplier['notes'] ?? null,
                    ];
                }
            }
            $product->suppliers()->sync($syncData);
        }

        // Log to audit trail
        \App\Models\AuditLog::logAction(
            \App\Models\AuditLog::ACTION_CREATE,
            \App\Models\AuditLog::MODULE_INVENTORY,
            "Product {$product->product_name} (SKU: {$product->sku}) created",
            'Product',
            $product->product_id,
            $product->product_name,
            null,
            [
                'product_name' => $product->product_name,
                'sku' => $product->sku,
                'product_brand' => $product->product_brand,
                'product_category' => $product->product_category,
                'price' => $product->price,
                'quantity' => $product->quantity,
            ],
            \App\Models\AuditLog::SEVERITY_INFO
        );

        return redirect()->route('inventory.products.index')->with('success', 'Product created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        // Check if request wants JSON (AJAX request for modal)
        if (request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            // Load relationships for modal view
            $product->load(['lastSupplier', 'suppliers', 'markupPrice']);

            return response()->json($product);
        }

        // Load relationships for detailed view
        $product->load(['sales', 'purchases', 'returns', 'lastSupplier', 'suppliers']);

        // Calculate additional metrics
        $stockMovement = $product->stock_movement;
        $recentSales = $product->sales()->latest()->take(5)->get();
        $recentPurchases = $product->purchases()->latest()->take(5)->get();

        return view('inventory.products.show', compact('product', 'stockMovement', 'recentSales', 'recentPurchases'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $this->authorize('edit products');

        $stockNames = StockName::pluck('name');  // get stock names as a collection
        $brands = Brand::pluck('name');  // get brand names as a collection
        $categories = Category::pluck('name');  // get category names as a collection
        $suppliers = \App\Models\Supplier::where('status', 'active')->orderBy('supplier_name')->get();
        $markupPrices = \App\Models\MarkupPrice::where('is_active', true)->orderBy('name')->get();

        // Check if request wants JSON (AJAX request for modal)
        if (request()->wantsJson() || request()->header('X-Requested-With') === 'XMLHttpRequest') {
            $product->load('markupPrice', 'suppliers');
            return response()->json([
                'product' => $product,
                'stockNames' => $stockNames,
                'brands' => $brands,
                'categories' => $categories,
                'suppliers' => $suppliers,
                'markupPrices' => $markupPrices,
            ]);
        }

        $product->load('suppliers');
        return view('inventory.products.edit', compact('product', 'stockNames', 'brands', 'categories', 'suppliers', 'markupPrices'));
    }

    public function update(Request $request, Product $product)
    {
        $this->authorize('edit products');

        $validator = Validator::make($request->all(), [
            'stock_name' => 'nullable|string|max:255',
            'custom_stock_name' => 'nullable|string|max:255',
            'product_name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:255|unique:products,sku,'.$product->product_id.',product_id',
            'barcode' => 'nullable|string|max:255|unique:products,barcode,'.$product->product_id.',product_id',
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'preferred_supplier_id' => 'nullable|exists:suppliers,supplier_id',
            'assigned_suppliers' => 'nullable|array',
            'assigned_suppliers.*.supplier_id' => 'required|exists:suppliers,supplier_id',
            'assigned_suppliers.*.cost' => 'nullable|numeric|min:0',
            'assigned_suppliers.*.is_primary' => 'nullable|boolean',
            'assigned_suppliers.*.notes' => 'nullable|string|max:500',
            'product_category' => 'nullable|string|max:255',
            'custom_category' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'pricing_method' => 'required|in:manual,markup',
            'markup_price_id' => 'nullable|required_if:pricing_method,markup|exists:markup_prices,id',
            'description' => 'nullable|string|max:1000',
            'image_url' => 'nullable|url|max:500',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'product_brand' => 'nullable|string|max:255',
            'custom_brand' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        // Handle stock name selection/creation
        $stockNameId = null;
        if ($request->stock_name) {
            $stockNameValue = $request->stock_name === 'custom' ? $request->custom_stock_name : $request->stock_name;

            if ($stockNameValue) {
                // Create stock name if it doesn't exist
                $stockName = StockName::firstOrCreate(
                    ['name' => $stockNameValue],
                    [
                        'stock_code' => strtoupper(str_replace([' ', '-'], '_', $stockNameValue)),
                        'description' => 'Auto-created stock name from product: '.$request->product_name,
                        'is_active' => true,
                    ]
                );
                $stockNameId = $stockName->id;
                // Keep the string version for backward compatibility
                $data['stock_name'] = $stockName->name;
            }
        }
        $data['stock_name_id'] = $stockNameId;

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
        if ($request->hasFile('image_file')) {
            // Delete old image if it exists and is a local file (not a URL)
            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            // Store new image
            $imagePath = $request->file('image_file')->store('products', 'public');
            $data['image'] = $imagePath;
        } elseif ($request->filled('image_url')) {
            // Delete old local image if replacing with URL
            if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            // If URL is provided, use it
            $data['image'] = $request->image_url;
        }

        // Capture old values before update
        $oldValues = [
            'product_name' => $product->product_name,
            'sku' => $product->sku,
            'product_brand' => $product->product_brand,
            'product_category' => $product->product_category,
            'price' => $product->price,
            'quantity' => $product->quantity,
        ];

        $product->update($data);

        // Refresh product data
        $product->refresh();

        // Sync assigned suppliers if provided
        if ($request->has('assigned_suppliers') && is_array($request->assigned_suppliers)) {
            $syncData = [];
            foreach ($request->assigned_suppliers as $supplier) {
                if (!empty($supplier['supplier_id'])) {
                    $syncData[$supplier['supplier_id']] = [
                        'cost' => $supplier['cost'] ?? null,
                        'is_primary' => !empty($supplier['is_primary']),
                        'notes' => $supplier['notes'] ?? null,
                    ];
                }
            }
            $product->suppliers()->sync($syncData);
        }


        // Capture new values after update
        $product->refresh();
        $newValues = [
            'product_name' => $product->product_name,
            'sku' => $product->sku,
            'product_brand' => $product->product_brand,
            'product_category' => $product->product_category,
            'price' => $product->price,
            'quantity' => $product->quantity,
        ];

        // Log to audit trail
        \App\Models\AuditLog::logAction(
            \App\Models\AuditLog::ACTION_UPDATE,
            \App\Models\AuditLog::MODULE_INVENTORY,
            "Product {$product->product_name} (SKU: {$product->sku}) updated",
            'Product',
            $product->product_id,
            $product->product_name,
            $oldValues,
            $newValues,
            \App\Models\AuditLog::SEVERITY_INFO
        );

        return redirect()->back()->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $this->authorize('delete products');

        // Capture product details before deletion
        $productName = $product->product_name;
        $productId = $product->product_id;
        $productData = [
            'product_name' => $product->product_name,
            'sku' => $product->sku,
            'product_brand' => $product->product_brand,
            'product_category' => $product->product_category,
            'price' => $product->price,
            'quantity' => $product->quantity,
        ];

        // Delete associated image (only if it's a local file, not a URL)
        if ($product->image && !filter_var($product->image, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        // Log to audit trail
        \App\Models\AuditLog::logAction(
            \App\Models\AuditLog::ACTION_DELETE,
            \App\Models\AuditLog::MODULE_INVENTORY,
            "Product {$productName} (SKU: {$productData['sku']}) deleted",
            'Product',
            $productId,
            $productName,
            $productData,
            null,
            \App\Models\AuditLog::SEVERITY_WARNING
        );

        return redirect()->route('inventory.products.index')
            ->with('success', 'Product deleted successfully!');
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('inventory.products.import');
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

            return redirect()->route('inventory.products.index')
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
        $lowStockProducts = Product::needsReordering()->get();
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
