<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Imports\PackagesImport;
use Maatwebsite\Excel\Facades\Excel;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Package::query();

        // Search functionality
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        // Filter by package type
        if ($request->has('package_type') && $request->package_type) {
            $query->byType($request->package_type);
        }

        // Filter by status
        if ($request->has('status') && $request->status) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'inactive':
                    $query->inactive();
                    break;
                case 'discontinued':
                    $query->discontinued();
                    break;
            }
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

        // Weight range filters
        if ($request->has('min_weight') && $request->min_weight) {
            $query->where('weight', '>=', $request->min_weight);
        }
        if ($request->has('max_weight') && $request->max_weight) {
            $query->where('weight', '<=', $request->max_weight);
        }

        // Sorting
        $sortBy = $request->get('sort', 'package_name');
        $sortOrder = $request->get('order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $packages = $query->paginate(15)->withQueryString();

        // Get unique package types for filter dropdown
        $packageTypes = Package::distinct()->pluck('package_type')->filter()->sort();

        return view('sales.packages.index', compact('packages', 'packageTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::orderBy('product_name')->get();
        return view('sales.packages.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'package_name' => 'required|string|max:255',
            'package_type' => 'required|string|in:standard,custom,bundle',
            'description' => 'nullable|string|max:1000',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'tracking_code' => 'nullable|string|max:255|unique:packages,tracking_code',
            'status' => 'required|string|in:active,inactive,discontinued',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contents' => 'nullable|array',
            'contents.*.product_id' => 'required|exists:products,product_id',
            'contents.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Generate tracking code if not provided
        if (empty($data['tracking_code'])) {
            $data['tracking_code'] = Package::generateTrackingCode();
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('packages', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        // Process contents
        if (isset($data['contents']) && is_array($data['contents'])) {
            $processedContents = [];
            foreach ($data['contents'] as $item) {
                if (isset($item['product_id']) && isset($item['quantity'])) {
                    $processedContents[] = [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity']
                    ];
                }
            }
            $data['contents'] = $processedContents;
        }

        Package::create($data);

        return redirect()->route('sales.packages.index')
            ->with('success', 'Package created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Package $package)
    {
        // Load relationships for detailed view
        $package->load(['products']);
        
        // Get formatted contents
        $formattedContents = $package->contents_formatted;

        return view('sales.packages.show', compact('package', 'formattedContents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Package $package)
    {
        $products = Product::orderBy('product_name')->get();
        return view('sales.packages.edit', compact('package', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Package $package)
    {
        $validator = Validator::make($request->all(), [
            'package_name' => 'required|string|max:255',
            'package_type' => 'required|string|in:standard,custom,bundle',
            'description' => 'nullable|string|max:1000',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'tracking_code' => 'nullable|string|max:255|unique:packages,tracking_code,' . $package->package_id . ',package_id',
            'status' => 'required|string|in:active,inactive,discontinued',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contents' => 'nullable|array',
            'contents.*.product_id' => 'required|exists:products,product_id',
            'contents.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Generate tracking code if not provided
        if (empty($data['tracking_code'])) {
            $data['tracking_code'] = Package::generateTrackingCode();
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($package->image && Storage::disk('public')->exists($package->image)) {
                Storage::disk('public')->delete($package->image);
            }

            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $imagePath = $image->storeAs('packages', $imageName, 'public');
            $data['image'] = $imagePath;
        }

        // Process contents
        if (isset($data['contents']) && is_array($data['contents'])) {
            $processedContents = [];
            foreach ($data['contents'] as $item) {
                if (isset($item['product_id']) && isset($item['quantity'])) {
                    $processedContents[] = [
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity']
                    ];
                }
            }
            $data['contents'] = $processedContents;
        }

        $package->update($data);

        return redirect()->route('sales.packages.index')
            ->with('success', 'Package updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Package $package)
    {
        // Delete associated image
        if ($package->image && Storage::disk('public')->exists($package->image)) {
            Storage::disk('public')->delete($package->image);
        }

        $package->delete();

        return redirect()->route('sales.packages.index')
            ->with('success', 'Package deleted successfully!');
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('sales.packages.import');
    }

    /**
     * Import packages from Excel file
     */
    public function import(Request $request)
    { 
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,csv|max:10240', // 10MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Please upload a valid Excel or CSV file.');
        }

        try {
            $file = $request->file('file');
            Excel::import(new PackagesImport, $file);

            return redirect()->route('sales.packages.index')
                ->with('success', 'Packages imported successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing packages: ' . $e->getMessage());
        }
    }

    /**
     * Download import template
     */
    public function downloadTemplate()
    {
        $headers = [
            'package_name',
            'package_type',
            'description',
            'weight',
            'dimensions',
            'price',
            'quantity',
            'tracking_code',
            'status'
        ];

        $callback = function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            // Add sample data
            fputcsv($file, [
                'Sample Package',
                'standard',
                'Sample description',
                '1.50',
                '20x15x10',
                '99.99',
                '10',
                'PKG-SAMPLE01',
                'active'
            ]);
            
            fclose($file);
        };

        return response()->stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="packages_template.csv"',
        ]);
    }

    /**
     * Bulk update stock for packages
     */
    public function bulkUpdateStock(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'updates' => 'required|array',
            'updates.*.package_id' => 'required|exists:packages,package_id',
            'updates.*.quantity' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()]);
        }

        $updates = [];
        foreach ($request->updates as $update) {
            $updates[$update['package_id']] = $update['quantity'];
        }

        $results = Package::bulkUpdateStock($updates);
        $successCount = array_sum(array_map('intval', $results));
        $totalCount = count($results);

        return response()->json([
            'success' => $successCount === $totalCount,
            'message' => "Updated {$successCount} out of {$totalCount} packages successfully.",
            'updated_count' => $successCount,
            'total_count' => $totalCount,
        ]);
    }

    /**
     * Get alerts data for packages
     */
    public function getAlertsData()
    {
        $lowStockPackages = Package::needsReordering(5);
        $outOfStockPackages = Package::outOfStock();

        return response()->json([
            'low_stock' => $lowStockPackages->map(function($package) {
                return [
                    'id' => $package->package_id,
                    'name' => $package->full_name,
                    'current_stock' => $package->quantity,
                    'type' => 'low_stock'
                ];
            }),
            'out_of_stock' => $outOfStockPackages->map(function($package) {
                return [
                    'id' => $package->package_id,
                    'name' => $package->full_name,
                    'current_stock' => $package->quantity,
                    'type' => 'out_of_stock'
                ];
            })
        ]);
    }

    /**
     * Toggle package status
     */
    public function toggleStatus(Package $package)
    {
        if ($package->status === Package::STATUS_ACTIVE) {
            $package->deactivate();
            $message = 'Package deactivated successfully!';
        } else {
            $package->activate();
            $message = 'Package activated successfully!';
        }

        return response()->json([
            'success' => true,
            'message' => $message,
            'new_status' => $package->status
        ]);
    }

    /**
     * Get package analytics
     */
    public function analytics()
    {
        $totalPackages = Package::count();
        $activePackages = Package::active()->count();
        $inStockPackages = Package::inStock()->count();
        $lowStockPackages = Package::needsReordering();
        $outOfStockPackages = Package::outOfStock();
        $totalInventoryValue = Package::totalInventoryValue();

        $packagesByType = [
            'standard' => Package::getByType(Package::TYPE_STANDARD)->count(),
            'custom' => Package::getByType(Package::TYPE_CUSTOM)->count(),
            'bundle' => Package::getByType(Package::TYPE_BUNDLE)->count(),
        ];

        return response()->json([
            'total_packages' => $totalPackages,
            'active_packages' => $activePackages,
            'in_stock_packages' => $inStockPackages,
            'low_stock_count' => $lowStockPackages->count(),
            'out_of_stock_count' => $outOfStockPackages->count(),
            'total_inventory_value' => $totalInventoryValue,
            'average_package_value' => $totalPackages > 0 ? $totalInventoryValue / $totalPackages : 0,
            'packages_by_type' => $packagesByType,
        ]);
    }
}
