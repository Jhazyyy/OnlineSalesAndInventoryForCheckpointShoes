<?php

namespace App\Http\Controllers;

use App\Models\PurchaseDelivery;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\PurchaseDeliveryService;

class PurchaseDeliveryController extends Controller
{
    protected PurchaseDeliveryService $deliveryService;

    public function __construct(PurchaseDeliveryService $deliveryService)
    {
        $this->deliveryService = $deliveryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $deliveries = $this->deliveryService->getPaginatedDeliveries($request);
        $filterOptions = $this->deliveryService->getFilterOptions();

        return view('purchases.deliveries.index', [
            'deliveries' => $deliveries,
            'suppliers' => $filterOptions['suppliers'],
            'purchase_orders' => $filterOptions['purchase_orders'],
            'carriers' => $filterOptions['carriers'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $filterOptions = $this->deliveryService->getFilterOptions();
        return view('purchases.deliveries.create', [
            'suppliers' => $filterOptions['suppliers'],
            'purchase_orders' => $filterOptions['purchase_orders'],
            'carriers' => $filterOptions['carriers'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Check if purchase order can accept a new delivery
        if ($request->has('purchase_order_id')) {
            $purchaseOrder = PurchaseOrder::find($request->purchase_order_id);
            if ($purchaseOrder && !$purchaseOrder->canCreateDelivery()) {
                $reason = '';
                if ($purchaseOrder->hasDeliveredStatus()) {
                    $reason = 'This purchase order has already been delivered.';
                } elseif ($purchaseOrder->hasShortClosedReceive()) {
                    $reason = 'This purchase order has been short-closed.';
                } elseif (!in_array($purchaseOrder->status, ['approved', 'ordered'])) {
                    $reason = 'Purchase order status must be approved or ordered.';
                }
                
                return redirect()->back()
                    ->withErrors(['purchase_order_id' => "Cannot create delivery. {$reason}"])
                    ->withInput();
            }
        }

        $validator = Validator::make($request->all(), [
            'purchase_order_id' => 'required|exists:purchase_orders,order_id',
            'supplier_id' => 'nullable|exists:suppliers,supplier_id',
            'carrier' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
            'service_type' => 'nullable|string|max:255',
            'delivery_date' => 'required|date',
            'scheduled_delivery_date' => 'nullable|date',
            'actual_delivery_date' => 'nullable|date',
            'status' => 'nullable|in:scheduled,in_transit,out_for_delivery,delivered,delayed,failed,cancelled',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'delivery_address' => 'nullable|string|max:2000',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_phone' => 'nullable|string|max:255',
            'recipient_email' => 'nullable|email|max:255',
            'total_packages' => 'nullable|integer|min:1',
            'total_weight' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'insurance_cost' => 'nullable|numeric|min:0',
            'additional_fees' => 'nullable|numeric|min:0',
            'is_insured' => 'nullable|boolean',
            'insurance_value' => 'nullable|numeric|min:0',
            'requires_signature' => 'nullable|boolean',
            'is_fragile' => 'nullable|boolean',
            'delivery_notes' => 'nullable|string|max:2000',
            'internal_notes' => 'nullable|string|max:2000',
            'damage_notes' => 'nullable|string|max:2000',
            'special_instructions' => 'nullable|string|max:2000',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.purchase_order_item_id' => 'nullable|exists:purchase_order_items,item_id',
            'items.*.quantity_expected' => 'required|integer|min:0',
            'items.*.quantity_delivered' => 'required|integer|min:0',
            'items.*.quantity_damaged' => 'nullable|integer|min:0',
            'items.*.quantity_missing' => 'nullable|integer|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.condition' => 'nullable|in:good,damaged,partial,missing',
            'items.*.item_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $delivery = $this->deliveryService->createDelivery($data);

        return redirect()->route('purchases.deliveries.show', $delivery->delivery_id)
            ->with('success', 'Purchase delivery created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseDelivery $delivery)
    {
        $delivery->load(['supplier', 'purchaseOrder', 'items.product']);
        return view('purchases.deliveries.show', compact('delivery'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseDelivery $delivery)
    {
        $delivery->load(['supplier', 'purchaseOrder', 'items.product']);
        $filterOptions = $this->deliveryService->getFilterOptions();
        return view('purchases.deliveries.edit', [
            'delivery' => $delivery,
            'suppliers' => $filterOptions['suppliers'],
            'purchase_orders' => $filterOptions['purchase_orders'],
            'carriers' => $filterOptions['carriers'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseDelivery $delivery)
    {
        $validator = Validator::make($request->all(), [
            'purchase_order_id' => 'required|exists:purchase_orders,order_id',
            'supplier_id' => 'nullable|exists:suppliers,supplier_id',
            'carrier' => 'nullable|string|max:255',
            'tracking_number' => 'nullable|string|max:255',
            'service_type' => 'nullable|string|max:255',
            'delivery_date' => 'required|date',
            'scheduled_delivery_date' => 'nullable|date',
            'actual_delivery_date' => 'nullable|date',
            'status' => 'nullable|in:scheduled,in_transit,out_for_delivery,delivered,delayed,failed,cancelled',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'delivery_address' => 'nullable|string|max:2000',
            'recipient_name' => 'nullable|string|max:255',
            'recipient_phone' => 'nullable|string|max:255',
            'recipient_email' => 'nullable|email|max:255',
            'total_packages' => 'nullable|integer|min:1',
            'total_weight' => 'nullable|numeric|min:0',
            'shipping_cost' => 'nullable|numeric|min:0',
            'insurance_cost' => 'nullable|numeric|min:0',
            'additional_fees' => 'nullable|numeric|min:0',
            'is_insured' => 'nullable|boolean',
            'insurance_value' => 'nullable|numeric|min:0',
            'requires_signature' => 'nullable|boolean',
            'is_fragile' => 'nullable|boolean',
            'delivery_notes' => 'nullable|string|max:2000',
            'internal_notes' => 'nullable|string|max:2000',
            'damage_notes' => 'nullable|string|max:2000',
            'special_instructions' => 'nullable|string|max:2000',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.purchase_order_item_id' => 'nullable|exists:purchase_order_items,item_id',
            'items.*.quantity_expected' => 'required|integer|min:0',
            'items.*.quantity_delivered' => 'required|integer|min:0',
            'items.*.quantity_damaged' => 'nullable|integer|min:0',
            'items.*.quantity_missing' => 'nullable|integer|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.condition' => 'nullable|in:good,damaged,partial,missing',
            'items.*.item_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $this->deliveryService->updateDelivery($delivery, $data);

        return redirect()->route('purchases.deliveries.show', $delivery->delivery_id)
            ->with('success', 'Purchase delivery updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseDelivery $delivery)
    {
        if (!$delivery->canBeCancelled()) {
            return redirect()->back()
                ->with('error', 'This delivery cannot be deleted.');
        }

        $this->deliveryService->deleteDelivery($delivery);

        return redirect()->route('purchases.deliveries.index')
            ->with('success', 'Purchase delivery deleted successfully!');
    }

    /**
     * Change delivery status.
     */
    public function changeStatus(Request $request, PurchaseDelivery $delivery)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:scheduled,in_transit,out_for_delivery,delivered,delayed,failed,cancelled',
            'notes' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $this->deliveryService->changeStatus($delivery, $data['status'], $data['notes'] ?? null);

        return redirect()->back()
            ->with('success', 'Delivery status updated successfully!');
    }

    /**
     * Get purchase order items for AJAX requests.
     */
    public function getPurchaseOrderItems(PurchaseOrder $purchaseOrder)
    {
        try {
            $items = $this->deliveryService->getPurchaseOrderItems($purchaseOrder->order_id);
            return response()->json([
                'success' => true,
                'items' => $items,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update tracking information.
     */
    public function updateTracking(Request $request, PurchaseDelivery $delivery)
    {
        $validator = Validator::make($request->all(), [
            'tracking_number' => 'required|string|max:255',
            'carrier' => 'nullable|string|max:255',
            'tracking_status' => 'nullable|string|max:255',
            'tracking_location' => 'nullable|string|max:255',
            'tracking_notes' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        
        $trackingUpdate = [
            'status' => $data['tracking_status'] ?? null,
            'location' => $data['tracking_location'] ?? null,
            'notes' => $data['tracking_notes'] ?? null,
        ];

        $this->deliveryService->updateTracking(
            $delivery, 
            $data['tracking_number'], 
            $data['carrier'] ?? null,
            $trackingUpdate
        );

        return redirect()->back()
            ->with('success', 'Tracking information updated successfully!');
    }

    /**
     * Show analytics page.
     */
    public function analytics(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        $statistics = $this->deliveryService->getStatistics($startDate, $endDate);
        $carrierPerformance = $this->deliveryService->getCarrierPerformance();

        return view('purchases.deliveries.analytics', [
            'statistics' => $statistics,
            'carrierPerformance' => $carrierPerformance,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }
}
