<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PurchasePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PurchasePayment::with(['supplier', 'purchaseOrder'])
            ->orderBy('created_at', 'desc');

        // Apply search filter
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Apply status filter
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        // Apply payment method filter
        if ($method = $request->get('payment_method')) {
            $query->where('payment_method', $method);
        }

        // Apply supplier filter
        if ($supplierId = $request->get('supplier_id')) {
            $query->where('supplier_id', $supplierId);
        }

        // Apply date range filter
        if ($startDate = $request->get('start_date')) {
            $query->where('payment_date', '>=', $startDate);
        }
        if ($endDate = $request->get('end_date')) {
            $query->where('payment_date', '<=', $endDate);
        }

        $payments = $query->paginate(15)->withQueryString();

        // Get filter options
        $suppliers = Supplier::active()->orderBy('supplier_name')->get(['supplier_id', 'supplier_name']);
        $paymentMethods = ['cash', 'card', 'bank_transfer', 'check', 'online', 'other'];
        $statuses = ['pending', 'completed', 'cancelled', 'refunded'];

        return view('purchases.payments.index', compact('payments', 'suppliers', 'paymentMethods', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $suppliers = Supplier::active()->orderBy('supplier_name')->get();
        $purchaseOrders = PurchaseOrder::with('supplier')
            ->whereIn('payment_status', ['pending', 'partial'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Pre-select customer and order if provided
        $selectedSupplier = $request->get('supplier_id');
        $selectedOrder = $request->get('purchase_order_id');

        return view('purchases.payments.create', compact('suppliers', 'purchaseOrders', 'selectedSupplier', 'selectedOrder'));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Debug: Log the incoming request data
        Log::info('Purchase Payment Creation Request', [
            'request_data' => $request->all(),
            'validation_errors' => []
        ]);

        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,order_id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,check,online,other',
            'bank_account' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'bank_charges' => 'nullable|numeric|min:0',
            'bill_number' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:2000',
            'received_by' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            Log::error('Purchase Payment Validation Failed', [
                'validation_errors' => $validator->errors()->toArray(),
                'request_data' => $request->all()
            ]);
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        // Individual payment status is 'completed' when recorded (the payment itself is complete)
        // The purchase order's payment_status (pending/partial/paid) is calculated separately
        $data['status'] = 'completed';
        $data['recieved_by'] = $data['received_by'] ?? Auth::user()->name ?? 'System';

        try {
            $payment = PurchasePayment::create($data);

            Log::info('Purchase Payment Created Successfully', [
                'payment_id' => $payment->payment_id,
                'payment_number' => $payment->payment_number,
                'amount' => $payment->amount,
                'status' => $payment->status
            ]);

            // Update purchase order payment status if linked
            // This will set the order's payment_status to pending/partial/paid based on total paid
            if ($payment->purchase_order_id) {
                $this->updateOrderPaymentStatus($payment->purchaseOrder);
            }

            return redirect()->route('purchases.payments.show', $payment->payment_id)
                ->with('success', 'Payment recorded successfully!');
        } catch (\Exception $e) {
            Log::error('Purchase Payment Creation Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'data' => $data
            ]);

            return redirect()->back()
                ->with('error', 'Failed to create payment: ' . $e->getMessage())
                ->withInput();
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(PurchasePayment $purchasePayment)
    {
        $purchasePayment->load(['supplier', 'purchaseOrder']);
        
        // Get related bills (purchase orders) for this supplier
        $relatedBills = PurchaseOrder::where('supplier_id', $purchasePayment->supplier_id)
            ->whereIn('payment_status', ['pending', 'partial', 'paid'])
            ->orderBy('order_date', 'desc')
            ->get();
        
        return view('purchases.payments.show', compact('purchasePayment', 'relatedBills'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchasePayment $purchasePayment)
    {
        if (!$purchasePayment->canBeEdited()) {
            return redirect()->route('purchases.payments.show', $purchasePayment->payment_id)
                ->with('error', 'This payment cannot be edited.');
        }

        $suppliers = Supplier::active()->orderBy('supplier_name')->get();
        $purchaseOrders = PurchaseOrder::with('supplier')
            ->whereIn('payment_status', ['pending', 'partial'])
            ->orWhere('purchase_order_id', $purchasePayment->purchase_order_id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Alias for view consistency
        $payment = $purchasePayment;

        return view('purchases.payments.edit', compact('payment', 'suppliers', 'purchaseOrders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchasePayment $purchasePayment)
    {
        if (!$purchasePayment->canBeEdited()) {
            return redirect()->route('purchases.payments.show', $purchasePayment->payment_id)
                ->with('error', 'This payment cannot be edited.');
        }
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,order_id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,check,online,other',
            'bank_account' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'bank_charges' => 'nullable|numeric|min:0',
            'bill_number' => 'nullable|string|max:255',
            'status' => 'required|in:pending,completed,cancelled,refunded',
            'notes' => 'nullable|string|max:2000',
            'received_by' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldOrderId = $purchasePayment->purchase_order_id;
        $purchasePayment->update($validator->validated());

        // Update purchase order payment status for old and new orders
        if ($oldOrderId && $oldOrderId != $purchasePayment->purchase_order_id) {
            $oldOrder = PurchaseOrder::find($oldOrderId);
            if ($oldOrder) {
                $this->updateOrderPaymentStatus($oldOrder);
            }
        }

        if ($purchasePayment->purchase_order_id) {
            $this->updateOrderPaymentStatus($purchasePayment->purchaseOrder);
        }

        return redirect()->route('purchases.payments.show', $purchasePayment->payment_id)
            ->with('success', 'Payment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchasePayment $purchasePayment)
    {
        if (!$purchasePayment->canBeCancelled()) {
            return redirect()->route('purchases.payments.index')
                ->with('error', 'This payment cannot be deleted.');
        }

        $purchaseOrderId = $purchasePayment->purchase_order_id;
        $purchasePayment->delete();

        // Update purchase order payment status if linked
        if ($purchaseOrderId) {
            $order = PurchaseOrder::find($purchaseOrderId);
            if ($order) {
                $this->updateOrderPaymentStatus($order);
            }
        }

        return redirect()->route('purchases.payments.index')
            ->with('success', 'Payment deleted successfully!');
    }

/**
     * Mark payment as completed.
     */
    public function markCompleted(PurchasePayment $purchasePayment)
    {
        $purchasePayment->markAsCompleted();
        
        if ($purchasePayment->purchase_order_id) {
            $this->updateOrderPaymentStatus($purchasePayment->purchaseOrder);
        }

        return redirect()->back()->with('success', 'Payment marked as completed!');
    }


    /**
     * Mark payment as cancelled.
     */
    public function markCancelled(PurchasePayment $purchasePayment)
    {
        if (!$purchasePayment->canBeCancelled()) {
            return redirect()->back()->with('error', 'This payment cannot be cancelled.');
        }

        $purchasePayment->markAsCancelled();
        
        if ($purchasePayment->purchase_order_id) {
            $this->updateOrderPaymentStatus($purchasePayment->purchaseOrder);
        }

        return redirect()->back()->with('success', 'Payment cancelled!');
    }

    /**
     * Get supplier bills via AJAX
     */
    public function getSupplierBills($supplierId)
    {
        $bills = PurchaseOrder::where('supplier_id', $supplierId)
            ->whereIn('payment_status', ['pending', 'partial'])
            ->with('supplier')
            ->orderBy('order_date', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'purchase_order_id' => $order->purchase_order_id,
                    'order_number' => $order->order_number,
                    'order_date' => $order->order_date->format('Y-m-d'),
                    'total_amount' => number_format($order->total_amount, 2),
                    'paid_amount' => number_format($order->paid_amount ?? 0, 2),
                    'remaining_amount' => number_format($order->total_amount - ($order->paid_amount ?? 0), 2),
                ];
            });

        return response()->json($bills);
    }

    /**
     * Get order details via AJAX
     */
    public function getOrderDetails($orderId)
    {
        $order = PurchaseOrder::with('supplier')->findOrFail($orderId);

        return response()->json([
            'purchase_order_id' => $order->purchase_order_id,
            'order_number' => $order->order_number,
            'supplier_name' => $order->supplier->supplier_name ?? 'N/A',
            'total_amount' => $order->total_amount,
            'paid_amount' => $order->paid_amount ?? 0,
            'remaining_amount' => $order->total_amount - ($order->paid_amount ?? 0),
            'order_date' => $order->order_date instanceof \Carbon\Carbon 
                ? $order->order_date->format('Y-m-d') 
                : \Carbon\Carbon::parse($order->order_date)->format('Y-m-d'),
        ]);
    }

    private function updateOrderPaymentStatus(PurchaseOrder $order)
    {
        // Use the model's new method to update paid amount and payment status
        $order->updatePaidAmount();
    }
}
