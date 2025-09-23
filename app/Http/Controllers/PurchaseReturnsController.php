<?php

namespace App\Http\Controllers;

use App\Models\PurchaseReturn;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class PurchaseReturnsController extends Controller
{
    /**
     * Display a listing of the purchase returns.
     */
    public function index(Request $request)
    {
        $query = PurchaseReturn::with(['product', 'supplier', 'purchaseOrder', 'creator']);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by supplier if provided
        if ($request->filled('supplier_id')) {
            $query->bySupplier($request->supplier_id);
        }

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('return_date', [$request->start_date, $request->end_date]);
        }

        // Search by product name
        if ($request->filled('search')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('product_name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            })->orWhereHas('supplier', function ($q) use ($request) {
                $q->where('company_name', 'like', '%' . $request->search . '%');
            });
        }

        $returns = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics for the dashboard
        $statistics = [
            'total_returns' => PurchaseReturn::count(),
            'pending_returns' => PurchaseReturn::pending()->count(),
            'approved_returns' => PurchaseReturn::approved()->count(),
            'processed_returns' => PurchaseReturn::where('return_status', 'processed')->count(),
            'total_return_value' => PurchaseReturn::approved()->get()->sum('total_amount'),
            'today_returns' => PurchaseReturn::today()->count(),
            'this_week_returns' => PurchaseReturn::thisWeek()->count(),
            'this_month_returns' => PurchaseReturn::thisMonth()->count(),
        ];

        $suppliers = Supplier::where('status', 'active')->orderBy('company_name')->get();

        return view('purchases.purchase-returns.index', compact('returns', 'statistics', 'suppliers'));
    }

    /**
     * Show the form for creating a new purchase return.
     */
    public function create()
    {
        $products = Product::where('stock_quantity', '>', 0)
                          ->orderBy('product_name')
                          ->get();
        
        $suppliers = Supplier::where('status', 'active')
                           ->orderBy('company_name')
                           ->get();
        
        $purchaseOrders = PurchaseOrder::whereIn('status', ['completed', 'partially_received'])
                                     ->orderBy('created_at', 'desc')
                                     ->get();
        
        $statuses = PurchaseReturn::getStatuses();

        return view('purchases.purchase-returns.create', compact('products', 'suppliers', 'purchaseOrders', 'statuses'));
    }

    /**
     * Store a newly created purchase return in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'product_id' => 'required|exists:products,product_id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'return_status' => 'required|in:' . implode(',', PurchaseReturn::getStatuses()),
            'return_date' => 'required|date|before_or_equal:today',
            'reason' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::beginTransaction();

            $purchaseReturn = PurchaseReturn::processPurchaseReturn(
                $validated['product_id'],
                $validated['supplier_id'],
                $validated['quantity'],
                $validated['price'],
                $validated['return_status'],
                $validated['return_date'],
                $validated['purchase_order_id'] ?? null,
                $validated['reason'] ?? null,
                Auth::id()
            );

            if (!$purchaseReturn) {
                DB::rollBack();
                return back()->withErrors(['error' => 'Failed to create purchase return. Product or supplier not found.'])->withInput();
            }

            DB::commit();

            return redirect()->route('purchases.purchase-returns.index')
                           ->with('success', 'Purchase return created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating purchase return: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to create purchase return. Please try again.'])
                        ->withInput();
        }
    }

    /**
     * Display the specified purchase return.
     */
    public function show(PurchaseReturn $purchaseReturn)
    {
        $purchaseReturn->load(['product', 'supplier', 'purchaseOrder', 'creator']);
        
        return view('purchases.purchase-returns.show', compact('purchaseReturn'));
    }

    /**
     * Show the form for editing the specified purchase return.
     */
    public function edit(PurchaseReturn $purchaseReturn)
    {
        // Only allow editing of pending returns
        if (!$purchaseReturn->isPending()) {
            return redirect()->route('purchases.purchase-returns.show', $purchaseReturn)
                           ->with('error', 'Only pending purchase returns can be edited.');
        }

        $products = Product::orderBy('product_name')->get();
        $suppliers = Supplier::where('status', 'active')->orderBy('company_name')->get();
        $purchaseOrders = PurchaseOrder::whereIn('status', ['completed', 'partially_received'])
                                     ->orderBy('created_at', 'desc')
                                     ->get();
        $statuses = PurchaseReturn::getStatuses();

        return view('purchases.purchase-returns.edit', compact('purchaseReturn', 'products', 'suppliers', 'purchaseOrders', 'statuses'));
    }

    /**
     * Update the specified purchase return in storage.
     */
    public function update(Request $request, PurchaseReturn $purchaseReturn)
    {
        // Only allow updating of pending returns
        if (!$purchaseReturn->isPending()) {
            return redirect()->route('purchases.purchase-returns.show', $purchaseReturn)
                           ->with('error', 'Only pending purchase returns can be updated.');
        }

        $validated = $request->validate([
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'product_id' => 'required|exists:products,product_id',
            'supplier_id' => 'required|exists:suppliers,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'return_status' => 'required|in:' . implode(',', PurchaseReturn::getStatuses()),
            'return_date' => 'required|date|before_or_equal:today',
            'reason' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $validated['total_amount'] = $validated['quantity'] * $validated['price'];
            $purchaseReturn->update($validated);

            return redirect()->route('purchases.purchase-returns.show', $purchaseReturn)
                           ->with('success', 'Purchase return updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating purchase return: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to update purchase return. Please try again.'])
                        ->withInput();
        }
    }

    /**
     * Remove the specified purchase return from storage.
     */
    public function destroy(PurchaseReturn $purchaseReturn)
    {
        // Only allow deletion of pending or rejected returns
        if (!in_array($purchaseReturn->return_status, [PurchaseReturn::STATUS_PENDING, PurchaseReturn::STATUS_REJECTED])) {
            return back()->with('error', 'Cannot delete processed purchase returns.');
        }

        try {
            $purchaseReturn->delete();

            return redirect()->route('purchases.purchase-returns.index')
                           ->with('success', 'Purchase return deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting purchase return: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to delete purchase return. Please try again.');
        }
    }

    /**
     * Approve a purchase return.
     */
    public function approve(PurchaseReturn $purchaseReturn)
    {
        if (!$purchaseReturn->isPending()) {
            return back()->with('error', 'Only pending purchase returns can be approved.');
        }

        try {
            DB::beginTransaction();

            if ($purchaseReturn->approve()) {
                DB::commit();
                return back()->with('success', 'Purchase return approved successfully. Inventory has been updated.');
            } else {
                DB::rollBack();
                return back()->with('error', 'Failed to approve purchase return.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving purchase return: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to approve purchase return. Please try again.');
        }
    }

    /**
     * Reject a purchase return.
     */
    public function reject(PurchaseReturn $purchaseReturn)
    {
        if (!$purchaseReturn->isPending()) {
            return back()->with('error', 'Only pending purchase returns can be rejected.');
        }

        try {
            if ($purchaseReturn->reject()) {
                return back()->with('success', 'Purchase return rejected successfully.');
            } else {
                return back()->with('error', 'Failed to reject purchase return.');
            }

        } catch (\Exception $e) {
            Log::error('Error rejecting purchase return: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to reject purchase return. Please try again.');
        }
    }

    /**
     * Mark a purchase return as processed.
     */
    public function markAsProcessed(PurchaseReturn $purchaseReturn)
    {
        if (!$purchaseReturn->isApproved()) {
            return back()->with('error', 'Only approved purchase returns can be marked as processed.');
        }

        try {
            if ($purchaseReturn->markAsProcessed()) {
                return back()->with('success', 'Purchase return marked as processed successfully.');
            } else {
                return back()->with('error', 'Failed to mark purchase return as processed.');
            }

        } catch (\Exception $e) {
            Log::error('Error marking purchase return as processed: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to mark purchase return as processed. Please try again.');
        }
    }

    /**
     * Mark a purchase return as refunded.
     */
    public function markAsRefunded(PurchaseReturn $purchaseReturn)
    {
        if (!$purchaseReturn->isProcessed()) {
            return back()->with('error', 'Only processed purchase returns can be marked as refunded.');
        }

        try {
            if ($purchaseReturn->markAsRefunded()) {
                return back()->with('success', 'Purchase return marked as refunded successfully.');
            } else {
                return back()->with('error', 'Failed to mark purchase return as refunded.');
            }

        } catch (\Exception $e) {
            Log::error('Error marking purchase return as refunded: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to mark purchase return as refunded. Please try again.');
        }
    }

    /**
     * Get purchase return analytics data.
     */
    public function analytics(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $analytics = [
            'total_returns' => PurchaseReturn::whereBetween('return_date', [$startDate, $endDate])->count(),
            'total_return_value' => PurchaseReturn::approved()
                                         ->whereBetween('return_date', [$startDate, $endDate])
                                         ->get()
                                         ->sum('total_amount'),
            'returns_by_status' => PurchaseReturn::returnsByStatus()
                                        ->where(function($query) use ($startDate, $endDate) {
                                            $query->whereBetween('return_date', [$startDate, $endDate]);
                                        }),
            'returns_by_supplier' => PurchaseReturn::returnsBySupplier(),
            'most_returned_products' => PurchaseReturn::mostReturnedProducts(10),
            'daily_returns' => PurchaseReturn::selectRaw('DATE(return_date) as date, COUNT(*) as count, SUM(total_amount) as total_amount')
                                    ->whereBetween('return_date', [$startDate, $endDate])
                                    ->groupBy('date')
                                    ->orderBy('date')
                                    ->get(),
        ];

        if ($request->wantsJson()) {
            return response()->json($analytics);
        }

        return view('purchases.purchase-returns.analytics', compact('analytics', 'startDate', 'endDate'));
    }

    /**
     * Bulk approve purchase returns.
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'return_ids' => 'required|json',
        ]);

        $returnIds = json_decode($validated['return_ids'], true);
        
        if (!is_array($returnIds)) {
            return back()->with('error', 'Invalid return IDs provided.');
        }

        try {
            DB::beginTransaction();

            $approvedCount = 0;
            $returns = PurchaseReturn::whereIn('return_id', $returnIds)
                            ->pending()
                            ->get();

            foreach ($returns as $return) {
                if ($return->approve()) {
                    $approvedCount++;
                }
            }

            DB::commit();

            if ($approvedCount > 0) {
                return back()->with('success', "Successfully approved {$approvedCount} purchase return(s).");
            } else {
                return back()->with('warning', 'No purchase returns were approved.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk approving purchase returns: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to bulk approve purchase returns. Please try again.');
        }
    }

    /**
     * Bulk reject purchase returns.
     */
    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'return_ids' => 'required|json',
        ]);

        $returnIds = json_decode($validated['return_ids'], true);
        
        if (!is_array($returnIds)) {
            return back()->with('error', 'Invalid return IDs provided.');
        }

        try {
            $rejectedCount = 0;
            $returns = PurchaseReturn::whereIn('return_id', $returnIds)
                            ->pending()
                            ->get();

            foreach ($returns as $return) {
                if ($return->reject()) {
                    $rejectedCount++;
                }
            }

            if ($rejectedCount > 0) {
                return back()->with('success', "Successfully rejected {$rejectedCount} purchase return(s).");
            } else {
                return back()->with('warning', 'No purchase returns were rejected.');
            }

        } catch (\Exception $e) {
            Log::error('Error bulk rejecting purchase returns: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to bulk reject purchase returns. Please try again.');
        }
    }
}
