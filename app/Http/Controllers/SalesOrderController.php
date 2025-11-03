<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\SalesOrderService;

/**
 * SalesOrderController
 * 
 * Manages sales orders - can receive orders from e-commerce application or created manually.
 * Provides full CRUD operations for sales order management.
 */
class SalesOrderController extends Controller
{
    protected SalesOrderService $orderService;

    public function __construct(SalesOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of sales orders.
     */
    public function index(Request $request)
    {
        $orders = $this->orderService->getPaginatedOrders($request);
        $filterOptions = $this->orderService->getFilterOptions();

        return view('sales.orders.index', [
            'orders' => $orders,
            'customers' => $filterOptions['customers'],
            'products' => $filterOptions['products'],
        ]);
    }

    /**
     * Show the form for creating a new sales order.
     */
    public function create()
    {
        $filterOptions = $this->orderService->getFilterOptions();
        
        // Get categories for filtering
        $categories = Category::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('sales.orders.create', [
            'customers' => $filterOptions['customers'],
            'products' => $filterOptions['products'],
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created sales order in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,customer_id',
            'order_date' => 'required|date',
            'expected_delivery_date' => 'nullable|date|after_or_equal:order_date',
            'priority' => 'nullable|in:low,normal,high,urgent',
            'status' => 'nullable|in:pending,confirmed,processing,ready,shipped,delivered,cancelled',
            'payment_status' => 'nullable|in:pending,partial,paid,refunded',
            'payment_method' => 'nullable|in:cash,card,bank_transfer,check,online,other',
            
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            'items.*.notes' => 'nullable|string|max:1000',
            
            'shipping_address' => 'nullable|string|max:2000',
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
        $order = $this->orderService->createOrder($data);

        return redirect()->route('sales.orders.show', $order->order_id)
            ->with('success', 'Sales order created successfully.');
    }

    /**
     * Display the specified sales order details.
     */
    public function show(SalesOrder $order)
    {
        $order->load(['customer', 'items.product.inventories', 'shipments']);
        return view('sales.orders.show', compact('order'));
    }

    /**
     * Analytics for orders.
     */
    public function analytics()
    {
        $analytics = $this->orderService->getOrderAnalytics();
        return response()->json($analytics);
    }
}
