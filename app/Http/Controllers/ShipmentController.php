<?php

namespace App\Http\Controllers;

use App\Models\Shipment;
use App\Models\SalesOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\ShipmentService;

class ShipmentController extends Controller
{
    protected ShipmentService $shipmentService;

    public function __construct(ShipmentService $shipmentService)
    {
        $this->shipmentService = $shipmentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $shipments = $this->shipmentService->getPaginatedShipments($request);
        $filterOptions = $this->shipmentService->getFilterOptions();

        return view('sales.shipments.index', [
            'shipments' => $shipments,
            'carriers' => $filterOptions['carriers'],
            'salesOrders' => $filterOptions['salesOrders'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $filterOptions = $this->shipmentService->getFilterOptions();
        return view('sales.shipments.create', [
            'salesOrders' => $filterOptions['salesOrders'],
            'products' => $filterOptions['products'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sales_order_id' => 'required|exists:sales_orders,order_id',
            'carrier' => 'nullable|string|max:255',
            'service_type' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255|unique:shipments,tracking_number',
            'reference_number' => 'nullable|string|max:255',
            'status' => 'nullable|in:pending,preparing,shipped,in_transit,out_for_delivery,delivered,exception,returned,cancelled',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'shipment_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:shipment_date',
            'shipping_address' => 'required|string',
            'billing_address' => 'nullable|string',
            'return_address' => 'nullable|string',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'nullable|string|max:20',
            'recipient_email' => 'nullable|email|max:255',
            'total_packages' => 'required|integer|min:1',
            'total_weight' => 'nullable|numeric|min:0',
            'package_dimensions' => 'nullable|array',
            'shipping_cost' => 'nullable|numeric|min:0',
            'insurance_cost' => 'nullable|numeric|min:0',
            'additional_fees' => 'nullable|numeric|min:0',
            'is_insured' => 'boolean',
            'insurance_value' => 'nullable|numeric|min:0',
            'requires_signature' => 'boolean',
            'is_fragile' => 'boolean',
            'is_perishable' => 'boolean',
            'special_instructions' => 'nullable|string',
            'internal_notes' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity_shipped' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.package_number' => 'nullable|string|max:50',
            'items.*.item_weight' => 'nullable|numeric|min:0',
            'items.*.condition' => 'nullable|in:new,used,refurbished,damaged',
            'items.*.serial_numbers' => 'nullable|array',
            'items.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Check inventory availability before creating shipment
        $stockIssues = $this->shipmentService->checkInventoryAvailability($data['items']);
        if (!empty($stockIssues)) {
            return redirect()->back()->withErrors(['items' => implode(', ', $stockIssues)])->withInput();
        }

        $shipment = $this->shipmentService->createShipment($data);

        return redirect()->route('sales.shipments.show', $shipment->shipment_id)
            ->with('success', 'Shipment created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Shipment $shipment)
    {
        $shipment->load(['salesOrder.customer', 'items.product']);
        return view('sales.shipments.show', compact('shipment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Shipment $shipment)
    {
        if (!$shipment->canBeEdited()) {
            return redirect()->route('sales.shipments.show', $shipment->shipment_id)
                ->with('error', 'This shipment cannot be edited in its current status.');
        }

        $shipment->load(['salesOrder.customer', 'items.product']);
        $filterOptions = $this->shipmentService->getFilterOptions();
        return view('sales.shipments.edit', [
            'shipment' => $shipment,
            'salesOrders' => $filterOptions['salesOrders'],
            'products' => $filterOptions['products'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Shipment $shipment)
    {
        if (!$shipment->canBeEdited()) {
            return redirect()->route('sales.shipments.show', $shipment->shipment_id)
                ->with('error', 'This shipment cannot be edited in its current status.');
        }

        $validator = Validator::make($request->all(), [
            'carrier' => 'nullable|string|max:255',
            'service_type' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255|unique:shipments,tracking_number,' . $shipment->shipment_id . ',shipment_id',
            'reference_number' => 'nullable|string|max:255',
            'status' => 'nullable|in:pending,preparing,shipped,in_transit,out_for_delivery,delivered,exception,returned,cancelled',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'shipment_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:shipment_date',
            'shipping_address' => 'required|string',
            'billing_address' => 'nullable|string',
            'return_address' => 'nullable|string',
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'nullable|string|max:20',
            'recipient_email' => 'nullable|email|max:255',
            'total_packages' => 'required|integer|min:1',
            'total_weight' => 'nullable|numeric|min:0',
            'package_dimensions' => 'nullable|array',
            'shipping_cost' => 'nullable|numeric|min:0',
            'insurance_cost' => 'nullable|numeric|min:0',
            'additional_fees' => 'nullable|numeric|min:0',
            'is_insured' => 'boolean',
            'insurance_value' => 'nullable|numeric|min:0',
            'requires_signature' => 'boolean',
            'is_fragile' => 'boolean',
            'is_perishable' => 'boolean',
            'special_instructions' => 'nullable|string',
            'internal_notes' => 'nullable|string',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity_shipped' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.package_number' => 'nullable|string|max:50',
            'items.*.item_weight' => 'nullable|numeric|min:0',
            'items.*.condition' => 'nullable|in:new,used,refurbished,damaged',
            'items.*.serial_numbers' => 'nullable|array',
            'items.*.notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Check inventory availability before updating
        if (isset($data['items'])) {
            $stockIssues = $this->shipmentService->checkInventoryAvailability($data['items']);
            if (!empty($stockIssues)) {
                return redirect()->back()->withErrors(['items' => implode(', ', $stockIssues)])->withInput();
            }
        }

        $this->shipmentService->updateShipment($shipment, $data);

        return redirect()->route('sales.shipments.show', $shipment->shipment_id)
            ->with('success', 'Shipment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Shipment $shipment)
    {
        try {
            $this->shipmentService->deleteShipment($shipment);
            return redirect()->route('sales.shipments.index')
                ->with('success', 'Shipment deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('sales.shipments.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Change shipment status
     */
    public function changeStatus(Request $request, Shipment $shipment)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,preparing,shipped,in_transit,out_for_delivery,delivered,exception,returned,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $this->shipmentService->changeShipmentStatus($shipment, $request->get('status'));
            return redirect()->back()->with('success', 'Shipment status updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Process shipment for delivery
     */
    public function ship(Shipment $shipment)
    {
        try {
            $this->shipmentService->processShipmentForDelivery($shipment);
            return redirect()->back()->with('success', 'Shipment processed for delivery successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Add tracking update
     */
    public function addTracking(Request $request, Shipment $shipment)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'location' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $this->shipmentService->addTrackingUpdate($shipment, [
                'status' => $request->status,
                'description' => $request->description,
                'location' => $request->location,
                'updated_by' => auth()->user()?->name ?? 'System',
            ]);
            
            return redirect()->back()->with('success', 'Tracking update added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Create shipment from sales order
     */
    public function createFromOrder(Request $request, SalesOrder $salesOrder)
    {
        try {
            $shipment = $this->shipmentService->createShipmentFromSalesOrder(
                $salesOrder,
                $request->all()
            );
            
            return redirect()->route('sales.shipments.show', $shipment->shipment_id)
                ->with('success', 'Shipment created from sales order successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Analytics
     */
    public function analytics()
    {
        $analytics = $this->shipmentService->getShipmentAnalytics();
        return response()->json($analytics);
    }

    /**
     * Get shipments requiring attention
     */
    public function attention()
    {
        $data = $this->shipmentService->getShipmentsRequiringAttention();
        return view('sales.shipments.attention', $data);
    }

    /**
     * Show tracking page
     */
    public function tracking(Request $request)
    {
        $shipment = null;
        $trackingNumber = $request->get('tracking_number');
        
        if ($trackingNumber) {
            $shipment = Shipment::where('tracking_number', $trackingNumber)
                              ->with(['salesOrder.customer', 'items.product'])
                              ->first();
        }
        
        return view('sales.shipments.tracking', compact('shipment', 'trackingNumber'));
    }

    /**
     * Process inventory updates
     */
    public function processInventory(Shipment $shipment)
    {
        try {
            $this->shipmentService->processInventoryUpdates($shipment);
            return redirect()->back()->with('success', 'Inventory updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Export shipments
     */
    public function export(Request $request)
    {
        $query = Shipment::with(['salesOrder.customer', 'items.product']);

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $query->search($request->search);
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('priority') && $request->priority) {
            $query->where('priority', $request->priority);
        }

        if ($request->has('carrier') && $request->carrier) {
            $query->where('carrier', $request->carrier);
        }

        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('shipment_date', '>=', $request->start_date);
        }

        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('shipment_date', '<=', $request->end_date);
        }

        $shipments = $query->get();

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="shipments_export_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($shipments) {
            $file = fopen('php://output', 'w');
            
            // Headers
            fputcsv($file, [
                'Shipment ID', 'Shipment Number', 'Sales Order', 'Customer', 'Carrier', 
                'Tracking Number', 'Status', 'Priority', 'Shipment Date', 'Expected Delivery', 
                'Actual Delivery', 'Recipient Name', 'Recipient Phone', 'Total Items', 
                'Total Weight', 'Shipping Cost', 'Total Cost', 'Created At'
            ]);
            
            // Data
            foreach ($shipments as $shipment) {
                fputcsv($file, [
                    $shipment->shipment_id,
                    $shipment->shipment_number,
                    $shipment->salesOrder?->order_number,
                    $shipment->salesOrder?->customer?->display_name,
                    $shipment->carrier,
                    $shipment->tracking_number,
                    ucfirst($shipment->status),
                    ucfirst($shipment->priority),
                    $shipment->shipment_date?->format('Y-m-d'),
                    $shipment->expected_delivery_date?->format('Y-m-d'),
                    $shipment->actual_delivery_date?->format('Y-m-d'),
                    $shipment->recipient_name,
                    $shipment->recipient_phone,
                    $shipment->total_items,
                    number_format($shipment->total_weight, 2),
                    number_format($shipment->shipping_cost, 2),
                    number_format($shipment->total_shipping_cost, 2),
                    $shipment->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show import form
     */
    public function showImportForm()
    {
        return view('sales.shipments.import');
    }

    /**
     * Import shipments from Excel/CSV file
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
            $import = new \App\Imports\ShipmentsImport();
            $import->import($request->file('excel_file'));

            $importedCount = $import->getRowCount();
            
            return redirect()->route('sales.shipments.index')
                ->with('success', "Successfully imported {$importedCount} shipments!");

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
            'Content-Disposition' => 'attachment; filename="shipments_template.csv"',
        ];

        // Create sample data for template
        $sampleData = [
            [
                'Sales Order Number', 'Carrier', 'Service Type', 'Tracking Number', 'Priority', 
                'Shipment Date', 'Expected Delivery Date', 'Recipient Name', 'Recipient Phone', 
                'Recipient Email', 'Shipping Address', 'Total Packages', 'Total Weight', 
                'Shipping Cost', 'Insurance Cost', 'Additional Fees', 'Is Insured', 
                'Requires Signature', 'Is Fragile', 'Is Perishable', 'Special Instructions', 'Notes'
            ],
            [
                'SO20241201-0001', 'FedEx', 'Express', 'FX123456789', 'high', 
                '2024-12-01', '2024-12-03', 'John Doe', '+63 912 345 6789', 
                'john.doe@example.com', '123 Main St, Manila, Philippines', '1', '2.50', 
                '350.00', '50.00', '25.00', 'yes', 'yes', 'no', 'no', 
                'Handle with care', 'Sample express shipment'
            ],
            [
                'SO20241201-0002', 'DHL', 'Standard', 'DH987654321', 'normal', 
                '2024-12-01', '2024-12-05', 'Jane Smith', '+63 917 654 3210', 
                'jane.smith@company.com', '456 Business Ave, Cebu, Philippines', '2', '5.00', 
                '250.00', '0.00', '0.00', 'no', 'no', 'no', 'no', 
                '', 'Sample standard shipment'
            ],
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
}
