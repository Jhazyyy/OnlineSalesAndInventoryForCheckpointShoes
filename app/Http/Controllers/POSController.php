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

        // Filter by date range (no default dates - show all)
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

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
                                  'image' => $product->image ? $product->image_url : null,
                                  'unit' => 'pcs',
                              ];
                          });

        // Get active customer taxes and discounts
        $activeTaxes = \App\Models\TaxDiscount::active()->taxes()->forCustomer()->orderBy('priority')->get();
        $activeDiscounts = \App\Models\TaxDiscount::active()->discounts()->forCustomer()->orderBy('priority')->get();

        // Get VAT-12 as default tax (find by name or code)
        $defaultTax = \App\Models\TaxDiscount::active()
            ->taxes()
            ->where(function($query) {
                $query->where('name', 'VAT-12')
                      ->orWhere('code', 'VAT-12');
            })
            ->first();

        return view('pos.create', compact('customers', 'products', 'activeTaxes', 'activeDiscounts', 'defaultTax'));
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
            'new_customer_first_name' => 'nullable|required_without:customer_id|string|max:255',
            'new_customer_last_name' => 'nullable|string|max:255',
            'new_customer_phone' => 'nullable|string|max:20',
            'new_customer_email' => 'nullable|email|max:255',
            
            // Order data
            'order_date' => 'required|date',
            'payment_method' => 'required|in:cash,bank_transfer,gcash',
            'payment_status' => 'required|in:pending,paid',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,product_id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.discount_amount' => 'nullable|numeric|min:0',
            
            // Tax and discount
            'tax_rule_id' => 'nullable|exists:tax_discounts,id',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_rule_id' => 'nullable|exists:tax_discounts,id',
            'discount_amount' => 'nullable|numeric|min:0',
            'subtotal_amount' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            
            'notes' => 'nullable|string|max:2000',
            'amount_received' => 'nullable|numeric|min:0',
            
            // Bank transfer fields (required if payment method is bank_transfer)
            'bank_name' => 'nullable|required_if:payment_method,bank_transfer|string|max:255',
            'reference_no' => 'nullable|required_if:payment_method,bank_transfer|string|max:255',
            'payment_proof' => 'nullable|required_if:payment_method,bank_transfer|file|mimes:jpeg,jpg,png,pdf|max:5120',
            
            // GCash fields (required if payment method is gcash)
            'gcash_reference_no' => 'nullable|required_if:payment_method,gcash|string|size:13|regex:/^[0-9]{13}$/',
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
                // Check if customer with this email already exists
                $email = $request->new_customer_email;
                
                if (!empty($email)) {
                    // Try to find existing customer by email
                    $existingCustomer = Customer::where('email', $email)->first();
                    
                    if ($existingCustomer) {
                        $customerId = $existingCustomer->customer_id;
                        Log::info('Found existing customer by email:', ['customer_id' => $customerId, 'email' => $email]);
                    } else {
                        // Email provided but doesn't exist, create new customer
                        $customer = Customer::create([
                            'first_name' => $request->new_customer_first_name,
                            'last_name' => $request->new_customer_last_name ?? '',
                            'phone' => $request->new_customer_phone ?? '',
                            'email' => $email,
                            'customer_type' => 'individual',
                            'status' => 'active',
                        ]);
                        $customerId = $customer->customer_id;
                        Log::info('Created new customer with email:', ['customer_id' => $customerId, 'email' => $email]);
                    }
                } else {
                    // No email provided, generate unique placeholder for walk-in customers
                    $email = 'walkin_' . time() . '_' . rand(1000, 9999) . '@pos.local';
                    
                    $customer = Customer::create([
                        'first_name' => $request->new_customer_first_name,
                        'last_name' => $request->new_customer_last_name ?? '',
                        'phone' => $request->new_customer_phone ?? '',
                        'email' => $email,
                        'customer_type' => 'individual',
                        'status' => 'active',
                    ]);
                    $customerId = $customer->customer_id;
                    Log::info('Created walk-in customer:', ['customer_id' => $customerId, 'email' => $email]);
                }
            }

            // Prepare order data
            Log::info('POS Tax/Discount values from request:', [
                'tax_rule_id' => $request->tax_rule_id,
                'tax_amount' => $request->tax_amount,
                'discount_rule_id' => $request->discount_rule_id,
                'discount_amount' => $request->discount_amount,
            ]);
            
            $taxRuleId = !empty($request->tax_rule_id) ? $request->tax_rule_id : null;
            $discountRuleId = !empty($request->discount_rule_id) ? $request->discount_rule_id : null;
            
            // Check if amount received matches total amount
            $totalAmount = (float) $request->total_amount;
            $amountReceived = (float) ($request->amount_received ?? 0);
            $paymentStatus = $request->payment_status;
            
            // For bank transfer, always set to pending until admin confirms
            if ($request->payment_method === 'bank_transfer') {
                $paymentStatus = 'pending';
            } 
            // If amount received is less than total, set to pending/partial
            elseif ($amountReceived > 0 && $amountReceived < $totalAmount) {
                $paymentStatus = 'partial';
            } elseif ($amountReceived >= $totalAmount && $request->payment_status === 'paid') {
                $paymentStatus = 'paid';
            }
            
            $orderData = [
                'customer_id' => $customerId,
                'order_date' => $request->order_date,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'amount_received' => $amountReceived > 0 ? $amountReceived : null,
                'purchase_type' => 'in_store',
                'items' => $request->items,
                'notes' => $request->notes,
                'tax_rule_id' => $taxRuleId,
                'tax_amount' => $taxRuleId ? ($request->tax_amount ?? 0) : 0,
                'discount_rule_id' => $discountRuleId,
                'discount_amount' => $discountRuleId ? ($request->discount_amount ?? 0) : 0,
                'subtotal' => $request->subtotal_amount,
                'total_amount' => $totalAmount,
            ];

            Log::info('Creating order with data:', $orderData);

            // Create the order (service will auto-set status to delivered if paid)
            $order = $this->orderService->createOrder($orderData);

            Log::info('Order created successfully:', ['order_id' => $order->order_id, 'order_number' => $order->order_number]);

            // If payment method is bank_transfer, create bank transfer payment record
            if ($request->payment_method === 'bank_transfer' && $request->hasFile('payment_proof')) {
                // Handle proof file upload
                $file = $request->file('payment_proof');
                $filename = 'pos_payment_proof_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $proofPath = $file->storeAs('payment_proofs', $filename, 'public');

                // Create bank transfer payment record
                $bankPayment = \App\Models\BankTransferPayment::create([
                    'order_id' => $order->order_id,
                    'payment_method' => 'bank_transfer',
                    'bank_name' => $request->bank_name,
                    'reference_no' => $request->reference_no,
                    'amount' => $totalAmount,
                    'proof' => $proofPath,
                    'status' => 'pending', // Pending until admin confirms
                ]);

                Log::info('Bank transfer payment created:', [
                    'payment_id' => $bankPayment->id,
                    'order_id' => $order->order_id,
                    'reference_no' => $bankPayment->reference_no,
                ]);
            }

            // If payment method is gcash, create gcash payment record
            if ($request->payment_method === 'gcash' && $request->filled('gcash_reference_no')) {
                // Create GCash payment record
                $gcashPayment = \App\Models\GcashPayment::create([
                    'sales_order_id' => $order->order_id,
                    'reference_number' => $request->gcash_reference_no,
                    'amount' => $totalAmount,
                    'status' => 'verified', // Automatically verified for POS transactions
                    'payment_date' => now(),
                    'verified_by' => auth()->id(),
                    'verified_at' => now(),
                    'notes' => 'POS walk-in customer payment via GCash QR scan',
                ]);

                Log::info('GCash payment created:', [
                    'payment_id' => $gcashPayment->id,
                    'order_id' => $order->order_id,
                    'reference_number' => $gcashPayment->reference_number,
                ]);
            }

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
        $order->load(['customer', 'items.product', 'taxRule', 'discountRule']);
        
        // Use amount_received from database first, then fall back to session
        $amountReceived = $order->amount_received ?? session('amount_received');
        $change = $amountReceived ? $amountReceived - $order->total_amount : null;
        
        return view('pos.show', compact('order', 'amountReceived', 'change'));
    }

    /**
     * Complete a partial payment
     */
    public function completePayment(Request $request, SalesOrder $order)
    {
        // Validate that this is a POS order and not already fully paid
        if ($order->purchase_type !== 'in_store') {
            return redirect()->back()->withErrors(['error' => 'This is not a POS order.']);
        }

        if ($order->payment_status === 'paid') {
            return redirect()->back()->with('info', 'This order is already fully paid.');
        }

        $validator = Validator::make($request->all(), [
            'additional_payment' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|in:cash,card,bank_transfer,check,online,other',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $additionalPayment = $request->additional_payment;
            $currentPaid = $order->amount_received ?? 0;
            $totalPaid = $currentPaid + $additionalPayment;
            
            // Update payment method if provided
            if ($request->payment_method) {
                $order->payment_method = $request->payment_method;
            }
            
            // Update payment status and order status
            if ($totalPaid >= $order->total_amount) {
                $order->payment_status = 'paid';
                $order->status = 'delivered'; // Mark order as completed/delivered
                $order->amount_received = $order->total_amount; // Set exactly to total
                $successMessage = 'Payment completed successfully! Order is now fully paid and completed.';
            } else {
                $order->payment_status = 'partial';
                $order->amount_received = $totalPaid;
                $remaining = $order->total_amount - $totalPaid;
                $successMessage = 'Payment updated successfully! Remaining balance: ₱' . number_format($remaining, 2);
            }
            
            $order->save();

            Log::info('Payment updated for POS order:', [
                'order_id' => $order->order_id,
                'order_number' => $order->order_number,
                'additional_payment' => $additionalPayment,
                'total_paid' => $totalPaid,
                'payment_status' => $order->payment_status,
                'order_status' => $order->status
            ]);

            DB::commit();

            return redirect()->route('pos.show', $order->order_id)
                ->with('success', $successMessage)
                ->with('amount_received', $order->amount_received);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment update failed:', [
                'error' => $e->getMessage(),
                'order_id' => $order->order_id
            ]);
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update payment: ' . $e->getMessage()])
                ->withInput();
        }
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
