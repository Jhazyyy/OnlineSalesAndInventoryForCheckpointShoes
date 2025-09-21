<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReceive;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\PurchaseReceiveService;

class PurchaseReceiveController extends Controller
{
    protected PurchaseReceiveService $receiveService;

    public function __construct(PurchaseReceiveService $receiveService)
    {
        $this->receiveService = $receiveService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $receives = $this->receiveService->getPaginatedReceives($request);
        $filterOptions = $this->receiveService->getFilterOptions();

        return view('inventory.purchase-receives.index', [
            'receives' => $receives,
            'suppliers' => $filterOptions['suppliers'],
            'purchase_orders' => $filterOptions['purchase_orders'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $filterOptions = $this->receiveService->getFilterOptions();
        return view('inventory.purchase-receives.create', [
            'suppliers' => $filterOptions['suppliers'],
            'purchase_orders' => $filterOptions['purchase_orders'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'purchase_order_id' => 'required|exists:purchase_orders,order_id',
            'receive_date' => 'required|date',
            'status' => 'nullable|in:in_transit,received,partially_received,damaged,cancelled',
            'receiver_name' => 'nullable|string|max:255',
            'receiving_notes' => 'nullable|string|max:2000',
            'damage_notes' => 'nullable|string|max:2000',
            'delivery_address' => 'nullable|string|max:2000',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.purchase_order_item_id' => 'nullable|exists:purchase_order_items,item_id',
            'items.*.quantity_expected' => 'required|integer|min:0',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.quantity_damaged' => 'nullable|integer|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.condition' => 'nullable|in:good,damaged,expired,partial',
            'items.*.item_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $receive = $this->receiveService->createReceive($data);

        return redirect()->route('inventory.purchase-receives.show', $receive->receive_id)
            ->with('success', 'Purchase receive created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseReceive $receive)
    {
        $receive->load(['supplier', 'purchaseOrder', 'items.product']);
        return view('inventory.purchase-receives.show', compact('receive'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseReceive $receive)
    {
        $receive->load(['supplier', 'purchaseOrder', 'items.product']);
        $filterOptions = $this->receiveService->getFilterOptions();
        return view('inventory.purchase-receives.edit', [
            'receive' => $receive,
            'suppliers' => $filterOptions['suppliers'],
            'purchase_orders' => $filterOptions['purchase_orders'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseReceive $receive)
    {
        $validator = Validator::make($request->all(), [
            'purchase_order_id' => 'required|exists:purchase_orders,order_id',
            'receive_date' => 'required|date',
            'status' => 'nullable|in:in_transit,received,partially_received,damaged,cancelled',
            'receiver_name' => 'nullable|string|max:255',
            'receiving_notes' => 'nullable|string|max:2000',
            'damage_notes' => 'nullable|string|max:2000',
            'delivery_address' => 'nullable|string|max:2000',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.purchase_order_item_id' => 'nullable|exists:purchase_order_items,item_id',
            'items.*.quantity_expected' => 'required|integer|min:0',
            'items.*.quantity_received' => 'required|integer|min:0',
            'items.*.quantity_damaged' => 'nullable|integer|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.condition' => 'nullable|in:good,damaged,expired,partial',
            'items.*.item_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $this->receiveService->updateReceive($receive, $data);

        return redirect()->route('inventory.purchase-receives.show', $receive->receive_id)
            ->with('success', 'Purchase receive updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseReceive $receive)
    {
        try {
            $this->receiveService->deleteReceive($receive);
            return redirect()->route('inventory.purchase-receives.index')
                ->with('success', 'Purchase receive deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('inventory.purchase-receives.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Change receive status
     */
    public function changeStatus(Request $request, PurchaseReceive $receive)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:in_transit,received,partially_received,damaged,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $this->receiveService->changeReceiveStatus($receive, $request->get('status'));
            return redirect()->back()->with('success', 'Receive status updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Get purchase order items for AJAX
     */
    public function getPurchaseOrderItems(Request $request, PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load('items.product');
        
        $items = $purchaseOrder->items->map(function ($item) {
            return [
                'item_id' => $item->item_id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->product_name,
                'quantity_ordered' => $item->quantity_ordered,
                'quantity_received' => $item->quantity_received,
                'quantity_pending' => $item->quantity_ordered - $item->quantity_received,
                'unit_price' => $item->unit_price,
            ];
        });

        return response()->json([
            'success' => true,
            'items' => $items
        ]);
    }

    /**
     * Analytics dashboard for purchase receives
     */
    public function analytics(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'supplier_id']);
        $analytics = $this->receiveService->getAnalytics($filters);
        $filterOptions = $this->receiveService->getFilterOptions();

        return view('inventory.purchase-receives.analytics', [
            'analytics' => $analytics,
            'filters' => $filters,
            'suppliers' => $filterOptions['suppliers'],
        ]);
    }
}
