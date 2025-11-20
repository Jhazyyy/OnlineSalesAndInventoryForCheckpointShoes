<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Services\PurchaseOrderService;

class PurchaseOrderController extends Controller
{
    protected PurchaseOrderService $orderService;

    public function __construct(PurchaseOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $orders = $this->orderService->getPaginatedOrders($request);
        $filterOptions = $this->orderService->getFilterOptions();

        return view('purchases.purchase-orders.index', [
            'orders' => $orders,
            'suppliers' => $filterOptions['suppliers'],
            'products' => $filterOptions['products'],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $filterOptions = $this->orderService->getFilterOptions();
        
        return view('purchases.purchase-orders.create', [
            'suppliers' => $filterOptions['suppliers'],
            'products' => $filterOptions['products'],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'status' => 'nullable|in:pending,approved,ordered,cancelled',
            'payment_status' => 'nullable|in:pending,partial,paid,refunded',
            'payment_method' => 'nullable|in:cash,bank_transfer',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:1000',

            'delivery_address' => 'nullable|string|max:2000',
            'billing_address' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'internal_notes' => 'nullable|string|max:2000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $order = $this->orderService->createOrder($data);

        return redirect()->route('purchases.purchase-orders.show', $order->order_id)
            ->with('success', 'Purchase order created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchaseOrder $order)
    {
        $order->load(['supplier', 'items.product' => function($query) {
            $query->orderBy('product_name', 'asc');
        }]);
        return view('purchases.purchase-orders.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchaseOrder $order)
    {
        $order->load(['supplier', 'items.product']);
        $filterOptions = $this->orderService->getFilterOptions();
        return view('purchases.purchase-orders.edit', [
            'order' => $order,
            'suppliers' => $filterOptions['suppliers'],
            'products' => $filterOptions['products'],
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchaseOrder $order)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date|after_or_equal:order_date',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'status' => 'nullable|in:pending,approved,ordered,partial_received,cancelled',
            'payment_status' => 'nullable|in:pending,partial,paid,refunded',
            'payment_method' => 'nullable|in:cash,bank_transfer',

            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity_ordered' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:1000',

            'delivery_address' => 'nullable|string|max:2000',
            'billing_address' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:2000',
            'internal_notes' => 'nullable|string|max:2000',
            'reference_number' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $this->orderService->updateOrder($order, $data);

        return redirect()->route('purchases.purchase-orders.show', $order->order_id)
            ->with('success', 'Purchase order updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchaseOrder $order)
    {
        try {
            $this->orderService->deleteOrder($order);
            return redirect()->route('purchases.purchase-orders.index')
                ->with('success', 'Purchase order deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('purchases.purchase-orders.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Change order status
     */
    public function changeStatus(Request $request, PurchaseOrder $order)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,approved,ordered,cancelled',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            $this->orderService->changeOrderStatus($order, $request->get('status'));
            return redirect()->back()->with('success', 'Order status updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Analytics
     */
    public function analytics()
    {
        $analytics = $this->orderService->getOrderAnalytics();
        return response()->json($analytics);
    }
    
    /**
     * Receiving Report
     */
    public function receivingReport(Request $request)
    {
        $filters = $request->only(['start_date', 'end_date', 'supplier_id']);
        $reportData = $this->orderService->getReceivingReport($filters);
        $filterOptions = $this->orderService->getFilterOptions();
        
        return view('purchases.purchase-orders.receiving-report', [
            'reportData' => $reportData,
            'suppliers' => $filterOptions['suppliers'],
        ]);
    }
}
