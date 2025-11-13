<?php

namespace App\Http\Controllers;

use App\Models\BankTransferPayment;
use App\Models\SalesOrder;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BankTransferPaymentController extends Controller
{
    /**
     * Show the form for submitting bank transfer payment
     */
    public function create(Request $request)
    {
        $orderId = $request->get('order_id');
        
        if (!$orderId) {
            return redirect()->back()->with('error', 'Order ID is required.');
        }

        $order = SalesOrder::with('customer')->findOrFail($orderId);

        // Check if order uses bank transfer
        if ($order->payment_method !== 'bank_transfer') {
            return redirect()->back()->with('error', 'This order does not use bank transfer as payment method.');
        }

        // Check if payment already submitted and pending/confirmed
        $existingPayment = BankTransferPayment::where('order_id', $orderId)
            ->whereIn('status', ['pending', 'confirmed'])
            ->first();

        if ($existingPayment) {
            return redirect()->back()->with('info', 'Payment for this order has already been submitted and is ' . $existingPayment->status . '.');
        }

        return view('sales.bank-transfer-payment', compact('order'));
    }

    /**
     * Display admin dashboard of all bank transfer payments
     */
    public function index(Request $request)
    {
        $query = BankTransferPayment::with(['order.customer', 'reviewer'])
                    ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        // Search by reference number or bank name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference_no', 'LIKE', "%{$search}%")
                  ->orWhere('bank_name', 'LIKE', "%{$search}%");
            });
        }

        $payments = $query->paginate(20);

        // Summary statistics
        $statistics = [
            'total_pending' => BankTransferPayment::pending()->count(),
            'total_confirmed' => BankTransferPayment::confirmed()->count(),
            'total_cancelled' => BankTransferPayment::cancelled()->count(),
            'pending_amount' => BankTransferPayment::pending()->sum('amount'),
            'confirmed_amount' => BankTransferPayment::confirmed()->sum('amount'),
        ];

        return view('admin.bank-transfer-payments.index', compact('payments', 'statistics'));
    }

    /**
     * Store a new bank transfer payment submission
     * Called by customers after making bank transfer
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:sales_orders,order_id',
            'bank_name' => 'required|string|max:255',
            'reference_no' => 'required|string|max:255|unique:bank_transfer_payments,reference_no',
            'amount' => 'required|numeric|min:0.01',
            'proof' => 'required|image|mimes:jpeg,png,jpg,pdf|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please check the form for errors.');
        }

        try {
            DB::beginTransaction();

            // Get the sales order
            $order = SalesOrder::findOrFail($request->order_id);

            // Validate that order payment method is bank_transfer
            if ($order->payment_method !== 'bank_transfer') {
                return redirect()->back()
                    ->with('error', 'This order does not use bank transfer as payment method.')
                    ->withInput();
            }

            // Validate amount matches order total
            if (abs($request->amount - $order->total_amount) > 0.01) {
                return redirect()->back()
                    ->with('error', "Payment amount (₱" . number_format($request->amount, 2) . ") does not match order total (₱" . number_format($order->total_amount, 2) . ").")
                    ->withInput();
            }

            // Handle proof file upload
            $proofPath = null;
            if ($request->hasFile('proof')) {
                $file = $request->file('proof');
                $filename = 'payment_proof_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $proofPath = $file->storeAs('payment_proofs', $filename, 'public');
            }

            // Create bank transfer payment record
            $payment = BankTransferPayment::create([
                'order_id' => $order->order_id,
                'payment_method' => 'bank_transfer',
                'bank_name' => $request->bank_name,
                'reference_no' => $request->reference_no,
                'amount' => $request->amount,
                'proof' => $proofPath,
                'status' => 'pending',
            ]);

            // Update order payment status to pending (if not already)
            if ($order->payment_status === 'unpaid' || $order->payment_status === 'pending') {
                $order->payment_status = 'pending';
                $order->save();
            }

            DB::commit();

            Log::info('Bank Transfer Payment Submitted', [
                'payment_id' => $payment->id,
                'order_id' => $order->order_id,
                'reference_no' => $payment->reference_no,
            ]);

            return redirect()->back()
                ->with('success', 'Bank transfer payment submitted successfully! Payment reference: ' . $payment->reference_no . '. Please wait for admin confirmation.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bank Transfer Payment Submission Failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to submit payment: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Confirm a bank transfer payment
     * Updates order status and deducts inventory
     */
    public function confirm(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Invalid input.');
        }

        try {
            DB::beginTransaction();

            $payment = BankTransferPayment::with('order.items.product')->findOrFail($id);

            // Check if already confirmed
            if ($payment->isConfirmed()) {
                return redirect()->back()
                    ->with('error', 'Payment already confirmed.');
            }

            // Check if cancelled
            if ($payment->isCancelled()) {
                return redirect()->back()
                    ->with('error', 'Cannot confirm a cancelled payment.');
            }

            $order = $payment->order;

            // Update payment status
            $payment->update([
                'status' => 'confirmed',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            // Update order payment status
            $order->payment_status = 'paid';
            
            // For in-store purchases, auto-deliver
            if ($order->purchase_type === 'in_store') {
                $order->status = 'delivered';
                $order->shipped_date = now();
            } else {
                // For online orders, mark as ready for shipment
                if ($order->status === 'pending') {
                    $order->status = 'confirmed';
                }
            }
            
            $order->save();

            // Deduct inventory for each order item
            foreach ($order->items as $item) {
                $product = $item->product;
                
                if ($product) {
                    // Check if sufficient stock
                    if ($product->quantity < $item->quantity_ordered) {
                        DB::rollBack();
                        return redirect()->back()
                            ->with('error', "Insufficient stock for product: {$product->product_name}. Available: {$product->quantity}, Required: {$item->quantity_ordered}");
                    }

                    // Deduct inventory
                    $product->quantity -= $item->quantity_ordered;
                    $product->save();

                    Log::info('Inventory Deducted', [
                        'product_id' => $product->product_id,
                        'product_name' => $product->product_name,
                        'quantity_deducted' => $item->quantity_ordered,
                        'remaining_stock' => $product->quantity,
                    ]);
                }
            }

            DB::commit();

            Log::info('Bank Transfer Payment Confirmed', [
                'payment_id' => $payment->id,
                'order_id' => $order->order_id,
                'confirmed_by' => Auth::id(),
            ]);

            return redirect()->back()
                ->with('success', 'Payment confirmed successfully! Order #' . $order->order_number . ' has been processed and inventory updated.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bank Transfer Payment Confirmation Failed', [
                'payment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to confirm payment: ' . $e->getMessage());
        }
    }

    /**
     * Cancel a bank transfer payment
     */
    public function cancel(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->with('error', 'Please provide a reason for cancellation.');
        }

        try {
            DB::beginTransaction();

            $payment = BankTransferPayment::with('order')->findOrFail($id);

            // Check if already cancelled
            if ($payment->isCancelled()) {
                return redirect()->back()
                    ->with('error', 'Payment already cancelled.');
            }

            // Check if already confirmed
            if ($payment->isConfirmed()) {
                return redirect()->back()
                    ->with('error', 'Cannot cancel a confirmed payment. Please create a refund instead.');
            }

            // Update payment status
            $payment->update([
                'status' => 'cancelled',
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'admin_notes' => $request->admin_notes,
            ]);

            // Update order status if needed
            $order = $payment->order;
            if ($order->payment_status === 'pending') {
                $order->payment_status = 'unpaid';
                $order->save();
            }

            DB::commit();

            Log::info('Bank Transfer Payment Cancelled', [
                'payment_id' => $payment->id,
                'order_id' => $order->order_id,
                'cancelled_by' => Auth::id(),
                'reason' => $request->admin_notes,
            ]);

            return redirect()->back()
                ->with('success', 'Payment cancelled successfully. Customer has been notified to resubmit correct payment proof.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Bank Transfer Payment Cancellation Failed', [
                'payment_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to cancel payment: ' . $e->getMessage());
        }
    }

    /**
     * Show payment proof image
     */
    public function showProof($id)
    {
        $payment = BankTransferPayment::findOrFail($id);
        
        if (!$payment->proof) {
            abort(404, 'Payment proof not found.');
        }

        $path = storage_path('app/public/' . $payment->proof);
        
        if (!file_exists($path)) {
            abort(404, 'Payment proof file not found.');
        }

        return response()->file($path);
    }
}
