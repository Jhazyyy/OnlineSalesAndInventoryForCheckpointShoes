<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\SalesOrderService;

/**
 * SalesOrderController - READ ONLY
 * 
 * Sales orders are received from e-commerce application.
 * This controller only displays and tracks orders.
 * Create/Edit/Delete operations are handled by the e-commerce system.
 */
class SalesOrderController extends Controller
{
    protected SalesOrderService $orderService;

    public function __construct(SalesOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of sales orders received from e-commerce.
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
     * Display the specified sales order details.
     */
    public function show(SalesOrder $order)
    {
        $order->load(['customer', 'items.product', 'shipments']);
        return view('sales.orders.show', compact('order'));
    }

    /**
     * Analytics for orders received from e-commerce.
     */
    public function analytics()
    {
        $analytics = $this->orderService->getOrderAnalytics();
        return response()->json($analytics);
    }
}
