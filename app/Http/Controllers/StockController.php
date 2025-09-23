<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Models\Product;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class StockController extends Controller
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Display a listing of stock movements.
     */
    public function index(Request $request)
    {
        $movements = $this->stockService->getPaginatedMovements($request);
        $filterOptions = $this->stockService->getFilterOptions();

        return view('inventory.product_stock_adjustment.index', [
            'movements' => $movements,
            'movement_types' => $filterOptions['movement_types'],
            'products' => $filterOptions['products'],
            'users' => $filterOptions['users'],
        ]);
    }

    /**
     * Show the form for creating a new stock adjustment.
     */
    public function create()
    {
        $products = Product::orderBy('product_name')->get(['product_id', 'product_name', 'product_brand', 'quantity']);
        return view('inventory.product_stock_adjustment.create', compact('products'));
    }

    /**
     * Store a newly created stock adjustment.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,product_id',
            'new_quantity' => 'required|integer|min:0',
            'unit_cost' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'movement_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $result = $this->stockService->createStockAdjustment($validator->validated());

        if ($result['success']) {
            return redirect()->route('inventory.product_stock_adjustment.index')
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->with('error', $result['message'])
                ->withInput();
        }
    }

    /**
     * Display the specified stock movement.
     */
    public function show(StockMovement $stock)
    {
        $stock->load(['product', 'user']);
        return view('inventory.product_stock_adjustment.show', compact('stock'));
    }

    /**
     * Show the form for editing the specified stock movement (only pending).
     */
    public function edit(StockMovement $stock)
    {
        if ($stock->status !== StockMovement::STATUS_PENDING) {
            return redirect()->route('inventory.product_stock_adjustment.index')
                ->with('error', 'Only pending movements can be edited.');
        }

        $products = Product::orderBy('product_name')->get(['product_id', 'product_name', 'product_brand', 'quantity']);
        return view('inventory.product_stock_adjustment.edit', compact('stock', 'products'));
    }

    /**
     * Update the specified stock movement (only pending).
     */
    public function update(Request $request, StockMovement $stock)
    {
        if ($stock->status !== StockMovement::STATUS_PENDING) {
            return redirect()->route('inventory.product_stock_adjustment.index')
                ->with('error', 'Only pending movements can be updated.');
        }

        $validator = Validator::make($request->all(), [
            'quantity_change' => 'required|integer',
            'unit_cost' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'movement_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $totalValue = isset($data['unit_cost']) ? ($data['unit_cost'] * abs($data['quantity_change'])) : null;
        
        $stock->update([
            'quantity_change' => $data['quantity_change'],
            'quantity_after' => $stock->quantity_before + $data['quantity_change'],
            'unit_cost' => $data['unit_cost'] ?? $stock->unit_cost,
            'total_value' => $totalValue,
            'reason' => $data['reason'] ?? $stock->reason,
            'notes' => $data['notes'] ?? $stock->notes,
            'movement_date' => isset($data['movement_date']) ? Carbon::parse($data['movement_date']) : $stock->movement_date,
        ]);

        return redirect()->route('inventory.product_stock_adjustment.index')
            ->with('success', 'Stock movement updated successfully!');
    }

    /**
     * Remove the specified stock movement (only pending).
     */
    public function destroy(StockMovement $stock)
    {
        $result = $this->stockService->deleteMovement($stock);

        if ($result['success']) {
            return redirect()->route('inventory.product_stock_adjustment.index')
                ->with('success', $result['message']);
        } else {
            return redirect()->route('inventory.product_stock_adjustment.index')
                ->with('error', $result['message']);
        }
    }

    /**
     * Show stock transfer form.
     */
    public function showTransferForm()
    {
        $products = Product::orderBy('product_name')->get(['product_id', 'product_name', 'product_brand', 'quantity']);
        return view('inventory.product_stock_adjustment.transfer', compact('products'));
    }

    /**
     * Process stock transfer.
     */
    public function processTransfer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id_from' => 'required|exists:products,product_id',
            'product_id_to' => 'required|exists:products,product_id|different:product_id_from',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'nullable|numeric|min:0',
            'location_from' => 'nullable|string|max:255',
            'location_to' => 'nullable|string|max:255',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'movement_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $result = $this->stockService->createStockTransfer($validator->validated());

        if ($result['success']) {
            return redirect()->route('inventory.product_stock_adjustment.index')
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->with('error', $result['message'])
                ->withInput();
        }
    }

    /**
     * Show waste/damage form.
     */
    public function showWasteForm()
    {
        $products = Product::where('quantity', '>', 0)
                          ->orderBy('product_name')
                          ->get(['product_id', 'product_name', 'product_brand', 'quantity']);
        return view('inventory.product_stock_adjustment.waste', compact('products'));
    }

    /**
     * Process waste/damage.
     */
    public function processWaste(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'nullable|numeric|min:0',
            'reason' => 'required|string|max:500',
            'notes' => 'nullable|string|max:1000',
            'movement_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $result = $this->stockService->recordWaste($validator->validated());

        if ($result['success']) {
            return redirect()->route('inventory.product_stock_adjustment.index')
                ->with('success', $result['message']);
        } else {
            return redirect()->back()
                ->with('error', $result['message'])
                ->withInput();
        }
    }

    /**
     * Confirm a pending stock movement.
     */
    public function confirm(StockMovement $stock)
    {
        $result = $this->stockService->confirmMovement($stock);

        return redirect()->back()
            ->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    /**
     * Show stock analytics dashboard.
     */
    public function analytics(Request $request)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : now()->startOfMonth();
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : now()->endOfMonth();

        $analytics = $this->stockService->getStockAnalytics($startDate, $endDate);

        return view('inventory.product_stock_adjustment.analytics', compact('analytics'));
    }

    /**
     * Get stock movement history for a specific product.
     */
    public function productHistory(Request $request, Product $product)
    {
        $startDate = $request->start_date ? Carbon::parse($request->start_date) : null;
        $endDate = $request->end_date ? Carbon::parse($request->end_date) : null;

        $history = $this->stockService->getProductStockHistory(
            $product->product_id, 
            $startDate, 
            $endDate
        );

        return view('inventory.product_stock_adjustment.product-history', compact('history'));
    }

    /**
     * Show import form for bulk stock adjustments.
     */
    public function showImportForm()
    {
        return view('inventory.product_stock_adjustment.import');
    }

    /**
     * Process bulk stock import.
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
            // Import logic will be implemented with StockImport class
            $import = new \App\Imports\StockImport();
            $import->import($request->file('excel_file'));

            $importedCount = $import->getRowCount();
            
            return redirect()->route('inventory.product_stock_adjustment.index')
                ->with('success', "Successfully processed {$importedCount} stock adjustments!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    /**
     * Download sample template for stock import.
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="stock_adjustments_template.csv"',
        ];

        // Create sample data for template
        $sampleData = [
            ['Product ID', 'New Quantity', 'Unit Cost', 'Reason', 'Notes'],
            [1, 100, 25.99, 'Stock count adjustment', 'Physical inventory count'],
            [2, 50, 15.50, 'Damaged goods removal', 'Water damage to 5 units'],
            [3, 75, 30.00, 'Audit correction', 'System error correction'],
        ];

        $callback = function() use ($sampleData) {
            $file = fopen('php://output', 'w');
            
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export stock movements to CSV.
     */
    public function export(Request $request)
    {
        $movements = StockMovement::with(['product', 'user'])
                        ->confirmed()
                        ->orderBy('movement_date', 'desc');

        // Apply filters if provided
        if ($request->has('start_date') && $request->start_date) {
            $movements->where('movement_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $movements->where('movement_date', '<=', $request->end_date);
        }
        if ($request->has('movement_type') && $request->movement_type) {
            $movements->ofType($request->movement_type);
        }

        $movements = $movements->get();

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="stock_movements_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($movements) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Movement ID', 'Date', 'Product', 'Brand', 'Movement Type', 
                'Quantity Before', 'Quantity Change', 'Quantity After', 
                'Unit Cost', 'Total Value', 'User', 'Reason', 'Notes', 'Status'
            ]);
            
            // Data
            foreach ($movements as $movement) {
                fputcsv($file, [
                    $movement->movement_id,
                    $movement->movement_date->format('Y-m-d H:i:s'),
                    $movement->product->product_name ?? 'N/A',
                    $movement->product->product_brand ?? 'N/A',
                    $movement->movement_type_label,
                    $movement->quantity_before,
                    $movement->quantity_change,
                    $movement->quantity_after,
                    $movement->unit_cost ? number_format($movement->unit_cost, 2) : '',
                    $movement->total_value ? number_format($movement->total_value, 2) : '',
                    $movement->user->name ?? 'System',
                    $movement->reason ?? '',
                    $movement->notes ?? '',
                    ucfirst($movement->status),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get stock alerts data for API/AJAX calls.
     */
    public function getAlertsData()
    {
        $analytics = $this->stockService->getStockAnalytics();
        
        return response()->json([
            'low_stock_count' => $analytics['alerts']['low_stock_count'],
            'low_stock_products' => $analytics['alerts']['low_stock_products'],
            'out_of_stock_count' => $analytics['alerts']['out_of_stock_count'],
            'out_of_stock_products' => $analytics['alerts']['out_of_stock_products'],
            'recent_movements' => $analytics['recent_movements'],
        ]);
    }
}
