<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Services\SalesOrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class POSController extends Controller
{
    protected $orderService;

    public function __construct(SalesOrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display today's POS transactions
     */
    public function index(Request $request)
    {
        $query = SalesOrder::with(['customer', 'items.product'])
                          ->where('purchase_type', 'in_store')
                          ->orderBy('created_at', 'desc');

        // Filter by date range
        $startDate = $request->get('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->get('end_date', Carbon::today()->format('Y-m-d'));

        if ($startDate) {
            $query->whereDate('order_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->whereDate('order_date', '<=', $endDate);
        }

        // Filter by payment status
        if ($status = $request->get('payment_status')) {
            $query->where('payment_status', $status);
        }

        $orders = $query->paginate(20)->withQueryString();

        // Get today's summary
        $todaySummary = [
            'total_sales' => SalesOrder::where('purchase_type', 'in_store')
                                      ->whereDate('order_date', Carbon::today())
                                      ->sum('total_amount'),
            'total_orders' => SalesOrder::where('purchase_type', 'in_store')
                                       ->whereDate('order_date', Carbon::today())
                                       ->count(),
            'cash_sales' => SalesOrder::where('purchase_type', 'in_store')
                                     ->where('payment_method', 'cash')
                                     ->whereDate('order_date', Carbon::today())
                                     ->sum('total_amount'),
            'card_sales' => SalesOrder::where('purchase_type', 'in_store')
                                     ->where('payment_method', 'card')
                                     ->whereDate('order_date', Carbon::today())
                                     ->sum('total_amount'),
        ];

        return view('pos.index', compact('orders', 'todaySummary'));
    }

    /**
     * Show POS interface for creating new sale
     */
    public function create()
    {
        // Get active customers for quick selection
        $customers = Customer::active()
                            ->orderBy('created_at', 'desc')
                            ->limit(100)
                            ->get()
                            ->map(function($customer) {
                                return [
                                    'id' => $customer->customer_id,
                                    'name' => trim($customer->first_name . ' ' . $customer->last_name),
                                    'phone' => $customer->phone,
                                    'email' => $customer->email,
                                ];
                            });

        // Get available products with stock
        $products = Product::where('quantity', '>', 0)
                          ->orderBy('product_name')
                          ->get()
                          ->map(function($product) {
                              return [
                                  'id' => $product->product_id,
                                  'name' => $product->name,
                                  'sku' => $product->sku,
                                  'price' => $product->price,
                                  'stock' => $product->quantity,
                                  'category' => $product->product_category ?? 'Uncategorized',
                                  'brand' => $product->product_brand ?? 'N/A',
                                  'image' => $product->image ? asset('storage/' . $product->image) : null,
                                  'unit' => 'pcs',
                              ];
                          });

        return view('pos.create', compact('customers', 'products'));
    }

    /**
     * Store a new POS sale
     */
    public function store(Request $request)
    {
        // Log the request for debugging
        Log::info('POS Store Request:', $request->all());

        $validator = Validator::make($request->all(), [
            // Customer data (either existing or new)
            'customer_id' => 'nullable|exists:customers,customer_id',
            'new_customer_first_name' => 'required_without:customer_id|string|max:255',
            'new_customer_last_name' => 'nullable|string|max:255',
            'new_customer_phone' => 'nullable|string|max:20',
            'new_customer_email' => 'nullable|email|max:255',
            
            // Order data
            'order_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,check,online,other',
            'payment_status' => 'required|in:pending,paid',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            
            'notes' => 'nullable|string|max:2000',
            'amount_received' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            Log::error('POS Validation Failed:', $validator->errors()->toArray());
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check the form for errors.');
        }

        try {
            DB::beginTransaction();

            // Handle customer (create new or use existing)
            if ($request->filled('customer_id')) {
                $customerId = $request->customer_id;
                Log::info('Using existing customer:', ['customer_id' => $customerId]);
            } else {
                // Create quick customer with generated email if not provided
                $email = $request->new_customer_email;
                if (empty($email)) {
                    // Generate a unique placeholder email for walk-in customers
                    $email = 'walkin_' . time() . '_' . rand(1000, 9999) . '@pos.local';
                }

                $customer = Customer::create([
                    'first_name' => $request->new_customer_first_name,
                    'last_name' => $request->new_customer_last_name ?? '',
                    'phone' => $request->new_customer_phone ?? '',
                    'email' => $email,
                    'customer_type' => 'individual',
                    'status' => 'active',
                ]);
                $customerId = $customer->customer_id;
                Log::info('Created new customer:', ['customer_id' => $customerId, 'email' => $email]);
            }

            // Prepare order data
            $orderData = [
                'customer_id' => $customerId,
                'order_date' => $request->order_date,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_status,
                'purchase_type' => 'in_store',
                'items' => $request->items,
                'notes' => $request->notes,
            ];

            Log::info('Creating order with data:', $orderData);

            // Create the order (service will auto-set status to delivered if paid)
            $order = $this->orderService->createOrder($orderData);

            Log::info('Order created successfully:', ['order_id' => $order->order_id, 'order_number' => $order->order_number]);

            DB::commit();

            return redirect()->route('pos.show', $order->order_id)
                ->with('success', 'Sale completed successfully! Order #' . $order->order_number . ' has been recorded.')
                ->with('amount_received', $request->amount_received);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('POS Store Failed:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->back()
                ->withErrors(['error' => 'Failed to process sale: ' . $e->getMessage()])
                ->withInput()
                ->with('error', 'Failed to process sale. Please try again.');
        }
    }

    /**
     * Display receipt for a POS sale
     */
    public function show(SalesOrder $order)
    {
        $order->load(['customer', 'items.product']);
        
        // Calculate change if amount_received is in session
        $amountReceived = session('amount_received');
        $change = $amountReceived ? $amountReceived - $order->total_amount : null;
        
        return view('pos.show', compact('order', 'amountReceived', 'change'));
    }

    /**
     * Quick customer search for autocomplete
     */
    public function searchCustomers(Request $request)
    {
        $search = $request->get('q', '');
        
        $customers = Customer::active()
                            ->where(function($query) use ($search) {
                                $query->where('first_name', 'like', "%{$search}%")
                                      ->orWhere('last_name', 'like', "%{$search}%")
                                      ->orWhere('phone', 'like', "%{$search}%")
                                      ->orWhere('email', 'like', "%{$search}%");
                            })
                            ->limit(10)
                            ->get()
                            ->map(function($customer) {
                                return [
                                    'id' => $customer->customer_id,
                                    'name' => trim($customer->first_name . ' ' . $customer->last_name),
                                    'phone' => $customer->phone,
                                    'email' => $customer->email,
                                ];
                            });

        return response()->json($customers);
    }
}
