<?php

namespace App\Http\Controllers;

use App\Models\PurchasePayment;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // Apply payment mode filter
        if ($mode = $request->get('payment_mode')) {
            $query->where('payment_mode', $mode);
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
        $suppliers = Supplier::where('status', 'active')->orderBy('company_name')->get(['supplier_id', 'company_name']);
        $paymentMethods = ['cash', 'card', 'bank_transfer', 'check', 'online', 'gcash', 'other'];
        $paymentModes = ['cash', 'bank_transfer', 'icici_bank', 'standard_chartered', 'yes_bank', 'kotak_bank', 'other'];
        $statuses = ['pending', 'completed', 'cancelled', 'refunded'];

        return view('purchases.payments.index', compact('payments', 'suppliers', 'paymentMethods', 'paymentModes', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $suppliers = Supplier::where('status', 'active')->orderBy('company_name')->get();
        $purchaseOrders = PurchaseOrder::with('supplier')
                                ->whereIn('status', ['pending', 'partially_received', 'approved'])
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        // Pre-select supplier and order if provided
        $selectedSupplier = $request->get('supplier_id');
        $selectedOrder = $request->get('purchase_order_id');
        
        // Get pending bills for suppliers (if needed)
        $pendingBills = [];
        if ($selectedSupplier) {
            $pendingBills = PurchaseOrder::where('supplier_id', $selectedSupplier)
                                        ->whereIn('payment_status', ['pending', 'partial'])
                                        ->get();
        }
        
        return view('purchases.payments.create', compact('suppliers', 'purchaseOrders', 'selectedSupplier', 'selectedOrder', 'pendingBills'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,order_id',
            'bill_number' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'bank_charges' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,check,online,gcash,other',
            'payment_mode' => 'required|in:cash,bank_transfer,icici_bank,standard_chartered,yes_bank,kotak_bank,other',
            'bank_account' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'status' => 'required|in:pending,completed,cancelled,refunded',
            'notes' => 'nullable|string|max:2000',
            'paid_by' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        
        try {
            $data = $validator->validated();
            $data['paid_by'] = $data['paid_by'] ?? Auth::user()->name ?? 'System';
            $data['unused_amount'] = $data['amount']; // Initially, all amount is unused
            
            $payment = PurchasePayment::create($data);

            // Update purchase order payment status if linked
            if ($payment->purchase_order_id) {
                $this->updateOrderPaymentStatus($payment->purchaseOrder);
            }
            
            DB::commit();

            return redirect()->route('purchases.payments.show', $payment->payment_id)
                ->with('success', 'Payment recorded successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error creating payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PurchasePayment $payment)
    {
        $payment->load(['supplier', 'purchaseOrder']);
        
        // Get related bills if needed
        $relatedBills = [];
        if ($payment->purchase_order_id) {
            $relatedBills = PurchaseOrder::where('supplier_id', $payment->supplier_id)
                                        ->whereIn('payment_status', ['pending', 'partial', 'paid'])
                                        ->get();
        }
        
        return view('purchases.payments.show', compact('payment', 'relatedBills'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PurchasePayment $payment)
    {
        if (!$payment->canBeEdited()) {
            return redirect()->route('purchases.payments.show', $payment->payment_id)
                ->with('error', 'This payment cannot be edited.');
        }

        $suppliers = Supplier::where('status', 'active')->orderBy('company_name')->get();
        $purchaseOrders = PurchaseOrder::with('supplier')
                                ->whereIn('status', ['pending', 'partially_received', 'approved'])
                                ->orWhere('order_id', $payment->purchase_order_id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        // Get pending bills for the supplier
        $pendingBills = PurchaseOrder::where('supplier_id', $payment->supplier_id)
                                    ->whereIn('payment_status', ['pending', 'partial'])
                                    ->get();

        return view('purchases.payments.edit', compact('payment', 'suppliers', 'purchaseOrders', 'pendingBills'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PurchasePayment $payment)
    {
        if (!$payment->canBeEdited()) {
            return redirect()->route('purchases.payments.show', $payment->payment_id)
                ->with('error', 'This payment cannot be edited.');
        }

        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,order_id',
            'bill_number' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'bank_charges' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,check,online,gcash,other',
            'payment_mode' => 'required|in:cash,bank_transfer,icici_bank,standard_chartered,yes_bank,kotak_bank,other',
            'bank_account' => 'nullable|string|max:255',
            'reference_number' => 'nullable|string|max:255',
            'status' => 'required|in:pending,completed,cancelled,refunded',
            'notes' => 'nullable|string|max:2000',
            'paid_by' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        
        try {
            $oldOrderId = $payment->purchase_order_id;
            $oldAmount = $payment->amount;
            
            $data = $validator->validated();
            
            // Adjust unused amount if the payment amount changed
            if ($data['amount'] != $oldAmount) {
                $difference = $data['amount'] - $oldAmount;
                $data['unused_amount'] = max(0, $payment->unused_amount + $difference);
            }
            
            $payment->update($data);

            // Update purchase order payment status for old and new orders
            if ($oldOrderId && $oldOrderId != $payment->purchase_order_id) {
                $oldOrder = PurchaseOrder::find($oldOrderId);
                if ($oldOrder) {
                    $this->updateOrderPaymentStatus($oldOrder);
                }
            }
            
            if ($payment->purchase_order_id) {
                $this->updateOrderPaymentStatus($payment->purchaseOrder);
            }
            
            DB::commit();

            return redirect()->route('purchases.payments.show', $payment->payment_id)
                ->with('success', 'Payment updated successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PurchasePayment $payment)
    {
        if (!$payment->canBeCancelled()) {
            return redirect()->route('purchases.payments.index')
                ->with('error', 'This payment cannot be deleted.');
        }

        DB::beginTransaction();
        
        try {
            $orderId = $payment->purchase_order_id;
            $payment->delete();
            
            // Update order payment status if linked
            if ($orderId) {
                $order = PurchaseOrder::find($orderId);
                if ($order) {
                    $this->updateOrderPaymentStatus($order);
                }
            }
            
            DB::commit();
            
            return redirect()->route('purchases.payments.index')
                ->with('success', 'Payment deleted successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('purchases.payments.index')
                ->with('error', 'Error deleting payment: ' . $e->getMessage());
        }
    }

    /**
     * Mark payment as completed.
     */
    public function markCompleted(PurchasePayment $payment)
    {
        if (!$payment->markAsCompleted()) {
            return redirect()->back()
                ->with('error', 'Failed to mark payment as completed.');
        }

        return redirect()->back()
            ->with('success', 'Payment marked as completed successfully!');
    }

    /**
     * Mark payment as cancelled.
     */
    public function markCancelled(PurchasePayment $payment)
    {
        DB::beginTransaction();
        
        try {
            $payment->markAsCancelled();
            
            // Update order payment status if linked
            if ($payment->purchase_order_id) {
                $this->updateOrderPaymentStatus($payment->purchaseOrder);
            }
            
            DB::commit();
            
            return redirect()->back()
                ->with('success', 'Payment cancelled successfully!');
                
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error cancelling payment: ' . $e->getMessage());
        }
    }

    /**
     * Update purchase order payment status based on payments.
     */
    protected function updateOrderPaymentStatus(PurchaseOrder $order)
    {
        $totalAmount = $order->total_amount;
        $totalPaid = PurchasePayment::where('purchase_order_id', $order->order_id)
                                   ->whereIn('status', ['completed'])
                                   ->sum('amount');

        if ($totalPaid >= $totalAmount) {
            $order->payment_status = 'paid';
        } elseif ($totalPaid > 0) {
            $order->payment_status = 'partial';
        } else {
            $order->payment_status = 'pending';
        }

        $order->save();
    }

    /**
     * Get purchase order details for AJAX.
     */
    public function getOrderDetails(Request $request, $orderId)
    {
        $order = PurchaseOrder::with('supplier')->find($orderId);
        
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }
        
        $paidAmount = PurchasePayment::where('purchase_order_id', $orderId)
                                    ->whereIn('status', ['completed'])
                                    ->sum('amount');
        
        $remainingAmount = $order->total_amount - $paidAmount;
        
        return response()->json([
            'order' => $order,
            'paid_amount' => $paidAmount,
            'remaining_amount' => $remainingAmount,
            'payment_status' => $order->payment_status,
        ]);
    }

    /**
     * Get supplier bills for AJAX.
     */
    public function getSupplierBills(Request $request, $supplierId)
    {
        $bills = PurchaseOrder::where('supplier_id', $supplierId)
                            ->whereIn('payment_status', ['pending', 'partial'])
                            ->orderBy('created_at', 'desc')
                            ->get(['order_id', 'order_number', 'total_amount', 'payment_status', 'order_date']);
        
        $bills = $bills->map(function($bill) {
            $paidAmount = PurchasePayment::where('purchase_order_id', $bill->order_id)
                                        ->whereIn('status', ['completed'])
                                        ->sum('amount');
            
            $bill->paid_amount = $paidAmount;
            $bill->remaining_amount = $bill->total_amount - $paidAmount;
            
            return $bill;
        });
        
        return response()->json($bills);
    }
}