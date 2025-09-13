<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Customer;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Payment::with(['customer', 'salesOrder'])
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

        // Apply customer filter
        if ($customerId = $request->get('customer_id')) {
            $query->where('customer_id', $customerId);
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
        $customers = Customer::active()->orderBy('first_name')->get(['customer_id', 'first_name', 'last_name']);
        $paymentMethods = ['cash', 'card', 'bank_transfer', 'check', 'online', 'other'];
        $statuses = ['pending', 'completed', 'cancelled', 'refunded'];

        return view('sales.payments.index', compact('payments', 'customers', 'paymentMethods', 'statuses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $customers = Customer::active()->orderBy('first_name')->get();
        $salesOrders = SalesOrder::with('customer')
                                ->whereIn('payment_status', ['pending', 'partial'])
                                ->orderBy('created_at', 'desc')
                                ->get();
        
        // Pre-select customer and order if provided
        $selectedCustomer = $request->get('customer_id');
        $selectedOrder = $request->get('order_id');
        
        return view('sales.payments.create', compact('customers', 'salesOrders', 'selectedCustomer', 'selectedOrder'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,customer_id',
            'order_id' => 'nullable|exists:sales_orders,order_id',
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

        $payment = Payment::create($data);

        // Update sales order payment status if linked
        if ($payment->order_id) {
            $this->updateOrderPaymentStatus($payment->salesOrder);
        }

        return redirect()->route('sales.payments.show', $payment->payment_id)
            ->with('success', 'Payment recorded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        $payment->load(['customer', 'salesOrder']);
        return view('sales.payments.show', compact('payment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Payment $payment)
    {
        if (!$payment->canBeEdited()) {
            return redirect()->route('sales.payments.show', $payment->payment_id)
                ->with('error', 'This payment cannot be edited.');
        }

        $customers = Customer::active()->orderBy('first_name')->get();
        $salesOrders = SalesOrder::with('customer')
                                ->whereIn('payment_status', ['pending', 'partial'])
                                ->orWhere('order_id', $payment->order_id)
                                ->orderBy('created_at', 'desc')
                                ->get();

        return view('sales.payments.edit', compact('payment', 'customers', 'salesOrders'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Payment $payment)
    {
        if (!$payment->canBeEdited()) {
            return redirect()->route('sales.payments.show', $payment->payment_id)
                ->with('error', 'This payment cannot be edited.');
        }

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,customer_id',
            'order_id' => 'nullable|exists:sales_orders,order_id',
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

        $oldOrderId = $payment->order_id;
        $payment->update($validator->validated());

        // Update sales order payment status for old and new orders
        if ($oldOrderId && $oldOrderId != $payment->order_id) {
            $oldOrder = SalesOrder::find($oldOrderId);
            if ($oldOrder) {
                $this->updateOrderPaymentStatus($oldOrder);
            }
        }
        
        if ($payment->order_id) {
            $this->updateOrderPaymentStatus($payment->salesOrder);
        }

        return redirect()->route('sales.payments.show', $payment->payment_id)
            ->with('success', 'Payment updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        if (!$payment->canBeCancelled()) {
            return redirect()->route('sales.payments.index')
                ->with('error', 'This payment cannot be deleted.');
        }

        $orderId = $payment->order_id;
        $payment->delete();

        // Update sales order payment status if linked
        if ($orderId) {
            $order = SalesOrder::find($orderId);
            if ($order) {
                $this->updateOrderPaymentStatus($order);
            }
        }

        return redirect()->route('sales.payments.index')
            ->with('success', 'Payment deleted successfully!');
    }

    /**
     * Mark payment as completed.
     */
    public function markCompleted(Payment $payment)
    {
        $payment->markAsCompleted();
        
        if ($payment->order_id) {
            $this->updateOrderPaymentStatus($payment->salesOrder);
        }

        return redirect()->back()->with('success', 'Payment marked as completed!');
    }

    /**
     * Mark payment as cancelled.
     */
    public function markCancelled(Payment $payment)
    {
        if (!$payment->canBeCancelled()) {
            return redirect()->back()->with('error', 'This payment cannot be cancelled.');
        }

        $payment->markAsCancelled();
        
        if ($payment->order_id) {
            $this->updateOrderPaymentStatus($payment->salesOrder);
        }

        return redirect()->back()->with('success', 'Payment cancelled!');
    }

    /**
     * Update sales order payment status based on payments received.
     */
    private function updateOrderPaymentStatus(SalesOrder $order)
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
