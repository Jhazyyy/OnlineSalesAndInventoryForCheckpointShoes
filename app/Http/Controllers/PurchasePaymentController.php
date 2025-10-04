<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PurchasePaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = PurchasePayment::with(['supplier_name', 'salesOrder'])
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
        $purchasePayment = PurchasePayment::active()->orderBy('supplier_name')->get(['supplier_id', 'first_name', 'last_name']);
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
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|exists:suppliers,supplier_id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,purchase_order_id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,check,online,other',
            'reference_number' => 'nullable|string|max:255',
            'status' => 'required|in:pending,completed,cancelled,refunded',
            'notes' => 'nullable|string|max:2000',
            'received_by' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['received_by'] = $data['received_by'] ?? Auth::user()->name ?? 'System';

        $payment = PurchasePayment::create($data);

        // Update purchase order payment status if linked
        if ($payment->purchase_order_id) {
            $this->updateOrderPaymentStatus($payment->purchaseOrder);
        }

        return redirect()->route('purchases.payments.show', $payment->payment_id)
            ->with('success', 'Payment recorded successfully!');

    }

    /**
     * Display the specified resource.
     */
    public function show(PurchasePayment $purchasePayment)
    {
        $purchasePayment->load(['supplier', 'purchaseOrder']);
        return view('purchases.payments.show', compact('purchasePayment'));
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
        $salesOrders = PurchaseOrder::with('customer')
            ->whereIn('payment_status', ['pending', 'partial'])
            ->orWhere('order_id', $purchasePayment->purchase_order_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('purchases.payments.edit', compact('purchasePayment', 'suppliers', 'purchaseOrders'));
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
            'purchase_order_id' => 'nullable|exists:purchase_orders,purchase_order_id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,card,bank_transfer,check,online,other',
            'reference_number' => 'nullable|string|max:255',
            'status' => 'required|in:pending,completed,cancelled,refunded',
            'notes' => 'nullable|string|max:2000',
            'received_by' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $oldOrderId = $purchasePayment->order_id;
        $purchasePayment->update($validator->validated());

        // Update purchase order payment status for old and new orders
        if ($oldOrderId && $oldOrderId != $purchasePayment->order_id) {
            $oldOrder = PurchaseOrder::find($oldOrderId);
            if ($oldOrder) {
                $this->updateOrderPaymentStatus($oldOrder);
            }
        }

        if ($purchasePayment->order_id) {
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

        $purchaseorderId = $purchasePayment->purchase_order_id;
        $purchasePayment->delete();

        // Update purchase order payment status if linked
        if ($purchaseorderId) {
            $order = PurchaseOrder::find($purchaseorderId);
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
        
        if ($purchasePayment->order_id) {
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

    private function updateOrderPaymentStatus(PurchaseOrder $order)
    {
        $totalPaid = $order->payments()->where('status', 'completed')->sum('amount');
        $orderTotal = $order->total_amount;

        if ($totalPaid >= $orderTotal) {
            $order->update(['payment_status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $order->update(['payment_status' => 'partial']);
        } else {
            $order->update(['payment_status' => 'pending']);
        }
    }
}
