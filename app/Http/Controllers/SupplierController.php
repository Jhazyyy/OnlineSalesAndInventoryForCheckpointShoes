<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Imports\SuppliersImport;
use App\Services\SupplierService;

class SupplierController extends Controller
{
    protected SupplierService $supplierService;

    public function __construct(SupplierService $supplierService)
    {
        $this->supplierService = $supplierService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $suppliers = $this->supplierService->getPaginatedSuppliers($request);
        $filterOptions = $this->supplierService->getFilterOptions();

        return view('master_data.suppliers.index', [
            'suppliers' => $suppliers,
            'types' => $filterOptions['types'],
            'cities' => $filterOptions['cities'],
            'countries' => $filterOptions['countries']
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('master_data.suppliers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:suppliers,email|max:255',
            'supplier_type' => 'required|string|in:local,distributor,manufacturer,service_provider',
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $this->supplierService->createSupplier($data);

        return redirect()->route('master_data.suppliers.index')
            ->with('success', 'Supplier created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        // Load relationships for detailed view
        $supplier->load(['purchaseOrders', 'activityLogs.user']);

        // Get recent purchase orders with items
        $recentPurchaseOrders = $supplier->purchaseOrders()
            ->with(['items.product'])
            ->latest('order_date')
            ->take(10)
            ->get();

        // Get supplier statistics and performance
        $performance = $this->supplierService->calculateSupplierPerformance($supplier);

        // Get recent activity logs
        $recentActivities = $supplier->activityLogs()
            ->with('user')
            ->latest()
            ->take(20)
            ->get();

        // Monthly purchase data for chart (based on purchase orders)
        $monthlyPurchases = $supplier->purchaseOrders()
            ->whereYear('order_date', date('Y'))
            ->get()
            ->groupBy(function ($order) {
                return $order->order_date->format('F');
            })
            ->map(function ($orders) {
                return [
                    'total_amount' => $orders->sum('total_amount'),
                    'count' => $orders->count()
                ];
            });

        return view('master_data.suppliers.show', compact('supplier', 'recentPurchaseOrders', 'performance', 'monthlyPurchases', 'recentActivities'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Supplier $supplier)
    {
        return view('master_data.suppliers.edit', compact('supplier'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validator = Validator::make($request->all(), [
            'supplier_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:suppliers,email,' . $supplier->supplier_id . ',supplier_id|max:255',
            'supplier_type' => 'required|string|in:local,distributor,manufacturer,service_provider',
            'address' => 'nullable|string|max:1000',
            'city' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'payment_terms' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $this->supplierService->updateSupplier($supplier, $data);

        // Log supplier update activity
        \App\Models\SupplierActivityLog::log(
            supplierId: $supplier->supplier_id,
            activityType: 'supplier_updated',
            description: "Supplier information updated",
            metadata: [
                'updated_fields' => array_keys($data),
            ]
        );

        return redirect()->route('master_data.suppliers.index')
            ->with('success', 'Supplier updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        try {
            $this->supplierService->deleteSupplier($supplier);
            return redirect()->route('master_data.suppliers.index')
                ->with('success', 'Supplier deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('master_data.suppliers.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('master_data.suppliers.import');
    }

    /**
     * Import suppliers from Excel/CSV file
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
            $import = new SuppliersImport();
            $import->import($request->file('excel_file'));

            $importedCount = $import->getRowCount();

            return redirect()->route('master_data.suppliers.index')
                ->with('success', "Successfully imported {$importedCount} suppliers!");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error importing file: ' . $e->getMessage());
        }
    }

    /**
     * Download sample Excel template
     */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="suppliers_template.csv"',
        ];

        // Create sample data for template
        $sampleData = [
            ['Supplier Name', 'Phone', 'Email', 'Supplier Type', 'Address', 'City', 'State', 'Postal Code', 'Country', 'Tax ID', 'Payment Terms', 'Notes'],
            ['ABC Supplies Inc.', '+1234567890', 'contact@abcsupplies.com', 'local', '123 Industrial St', 'Manila', 'NCR', '1000', 'Philippines', 'TAX123456', 'Net 30', 'Reliable local distributor'],
            ['Global Manufacturing Co.', '+8765432109', 'orders@globalmanuf.com', 'manufacturer', '456 Factory Ave', 'Cebu', 'Cebu', '6000', 'Philippines', 'TAX789012', 'Net 45', 'International manufacturer'],
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
     * Get supplier analytics data
     */
    public function analytics()
    {
        $analytics = $this->supplierService->getSupplierAnalytics();
        return response()->json($analytics);
    }

    /**
     * Toggle supplier status
     */
    public function toggleStatus(Supplier $supplier)
    {
        $updatedSupplier = $this->supplierService->toggleSupplierStatus($supplier);
        $status = $updatedSupplier->status === 'active' ? 'activated' : 'deactivated';

        return redirect()->back()
            ->with('success', "Supplier {$status} successfully!");
    }

    /**
     * Export suppliers to CSV
     */
    public function export(Request $request)
    {
        $suppliers = $this->supplierService->getSuppliersForExport($request->all());

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="suppliers_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($suppliers) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'ID',
                'Supplier Name',
                'Email',
                'Phone',
                'Supplier Type',
                'Address',
                'City',
                'State',
                'Postal Code',
                'Country',
                'Tax ID',
                'Payment Terms',
                'Notes',
                'Status',
                'Total Orders',
                'Total Purchased',
                'Last Order Date',
                'Created At'
            ]);

            // Data
            foreach ($suppliers as $supplier) {
                fputcsv($file, [
                    $supplier->supplier_id,
                    $supplier->supplier_name,
                    $supplier->email,
                    $supplier->phone,
                    $supplier->supplier_type,
                    $supplier->address,
                    $supplier->city,
                    $supplier->state,
                    $supplier->postal_code,
                    $supplier->country,
                    $supplier->tax_id,
                    $supplier->payment_terms,
                    $supplier->notes,
                    $supplier->status,
                    $supplier->total_orders,
                    number_format($supplier->total_purchased, 2),
                    $supplier->last_order_date?->format('Y-m-d'),
                    $supplier->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get suppliers needing attention (for alerts/dashboard)
     */
    public function getAlertsData()
    {
        $suppliersNeedingAttention = $this->supplierService->getSuppliersNeedingAttention();
        return response()->json($suppliersNeedingAttention);
    }
}
