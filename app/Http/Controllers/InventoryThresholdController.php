<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\InventoryAlert;
use App\Services\InventoryThresholdService;
use Illuminate\Support\Facades\Validator;

class InventoryThresholdController extends Controller
{
    protected InventoryThresholdService $thresholdService;

    public function __construct(InventoryThresholdService $thresholdService)
    {
        $this->thresholdService = $thresholdService;
    }

    /**
     * Display a listing of products with threshold management.
     */
    public function index(Request $request)
    {
        $products = $this->thresholdService->getPaginatedProducts($request);
        $stats = $this->thresholdService->getThresholdStatistics();
        $filterOptions = $this->thresholdService->getFilterOptions();

        return view('inventory.thresholds.index', compact('products', 'stats', 'filterOptions'));
    }

    /**
     * Display the specified product's threshold details.
     */
    public function show(Product $product)
    {
        $product->load(['preferredSupplier', 'inventoryAlerts']);
        $thresholdData = $this->thresholdService->getProductThresholdData($product);
        $recentAlerts = $this->thresholdService->getProductRecentAlerts($product);

        return view('inventory.thresholds.show', compact('product', 'thresholdData', 'recentAlerts'));
    }

    /**
     * Show the form for editing the specified product's thresholds.
     */
    public function edit(Product $product)
    {
        $suppliers = $this->thresholdService->getSuppliers();
        return view('inventory.thresholds.edit', compact('product', 'suppliers'));
    }

    /**
     * Update the specified product's threshold settings.
     */
    public function update(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'reorder_level' => 'nullable|numeric|min:0',
            'critical_level' => 'nullable|numeric|min:0',
            'ceiling_level' => 'nullable|numeric|min:0',
            'floor_level' => 'nullable|numeric|min:0',
            'economic_order_quantity' => 'nullable|numeric|min:1',
            'lead_time_days' => 'nullable|integer|min:1',
            'preferred_supplier_id' => 'nullable|exists:suppliers,supplier_id',
            'auto_reorder_enabled' => 'boolean',
            'threshold_alerts_enabled' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->thresholdService->updateProductThresholds($product, $validator->validated(), auth()->user());
            
            return redirect()->route('inventory.thresholds.show', $product)
                ->with('success', 'Threshold settings updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update threshold settings: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Bulk update thresholds for multiple products.
     */
    public function bulkUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array',
            'product_ids.*' => 'exists:products,product_id',
            'reorder_level' => 'nullable|numeric|min:0',
            'critical_level' => 'nullable|numeric|min:0',
            'ceiling_level' => 'nullable|numeric|min:0',
            'floor_level' => 'nullable|numeric|min:0',
            'auto_reorder_enabled' => 'boolean',
            'threshold_alerts_enabled' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $updateData = array_filter($validator->validated(), function($value, $key) {
                return $key !== 'product_ids' && $value !== null;
            }, ARRAY_FILTER_USE_BOTH);
            
            $results = $this->thresholdService->bulkUpdateThresholds(
                $request->product_ids, 
                $updateData, 
                auth()->user()
            );

            if (!empty($results['errors'])) {
                return redirect()->back()->with('warning', 
                    "Updated {$results['updated']} products. Some products had errors.");
            }

            return redirect()->back()->with('success', 
                "Successfully updated thresholds for {$results['updated']} products");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update thresholds: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display a listing of inventory alerts.
     */
    public function alerts(Request $request)
    {
        $alerts = $this->thresholdService->getPaginatedAlerts($request);
        $filterOptions = $this->thresholdService->getAlertFilterOptions();
        
        return view('inventory.thresholds.alerts', compact('alerts', 'filterOptions'));
    }
    
    /**
     * Show the details of a specific inventory alert.
     */
    public function showAlert(InventoryAlert $alert)
    {
        $alert->load(['product']);
        return view('inventory.thresholds.alert-details', compact('alert'));
    }

    /**
     * Resolve an inventory alert.
     */
    public function resolveAlert(Request $request, InventoryAlert $alert)
    {
        try {
            $this->thresholdService->resolveAlert($alert, auth()->user());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Alert resolved successfully'
                ]);
            }
            
            return redirect()->route('inventory.thresholds.alerts')
                ->with('success', 'Alert resolved successfully');
        } catch (\Exception $e) {  
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to resolve alert: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to resolve alert: ' . $e->getMessage());
        }
    }

    /**
     * Bulk resolve multiple inventory alerts.
     */
    public function bulkResolveAlerts(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alert_ids' => 'required|array',
            'alert_ids.*' => 'exists:inventory_alerts,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $results = $this->thresholdService->bulkResolveAlerts($request->alert_ids, auth()->user());
            
            return redirect()->back()->with('success', 
                "Successfully resolved {$results['resolved']} alerts.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to resolve alerts: ' . $e->getMessage());
        }
    }

    /**
     * Run threshold checks for all products.
     */
    public function runThresholdCheck(Request $request)
    {
        try {
            $results = $this->thresholdService->runThresholdChecks();
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Threshold check completed',
                    'results' => $results
                ]);
            }
            
            return redirect()->back()->with('success', 
                "Threshold check completed. Generated {$results['alerts_generated']} new alerts.");
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to run threshold check: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to run threshold check: ' . $e->getMessage());
        }
    }
    
    /**
     * Get analytics data for the dashboard.
     */
    public function analytics(Request $request)
    {
        $analytics = $this->thresholdService->getAnalytics($request);
        
        return response()->json($analytics);
    }
    
    /**
     * Export threshold data.
     */
    public function export(Request $request)
    {
        return $this->thresholdService->exportThresholdData($request);
    }
}
