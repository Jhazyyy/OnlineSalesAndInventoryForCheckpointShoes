<?php

namespace App\Http\Controllers;

use App\Models\Returns;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReturnsController extends Controller
{
    /**
     * Display a listing of the returns.
     */
    public function index(Request $request)
    {
        $query = Returns::with('product');

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('return_date', [$request->start_date, $request->end_date]);
        }

        // Search by product name
        if ($request->filled('search')) {
            $query->whereHas('product', function ($q) use ($request) {
                $q->where('product_name', 'like', '%' . $request->search . '%');
            });
        }

        $returns = $query->orderBy('created_at', 'desc')->paginate(15);

        // Get statistics for the filter sidebar
        $statistics = [
            'total_returns' => Returns::count(),
            'pending_returns' => Returns::pending()->count(),
            'approved_returns' => Returns::approved()->count(),
            'total_return_value' => Returns::approved()->get()->sum('total_amount'),
            'today_returns' => Returns::today()->count(),
            'this_week_returns' => Returns::thisWeek()->count(),
            'this_month_returns' => Returns::thisMonth()->count(),
        ];

        return view('sales.returns.index', compact('returns', 'statistics'));
    }

    /**
     * Show the form for creating a new return.
     */
    public function create()
    {
        $products = Product::where('quantity', '>', 0)
                          ->orderBy('product_name')
                          ->get();
        
        $statuses = Returns::getStatuses();

        return view('sales.returns.create', compact('products', 'statuses'));
    }

    /**
     * Store a newly created return in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'return_status' => 'required|in:' . implode(',', Returns::getStatuses()),
            'return_date' => 'required|date|before_or_equal:today',
            'reason' => 'nullable|string|max:500',
        ]);

        try {
            DB::beginTransaction();

            $return = Returns::processReturn(
                $validated['product_id'],
                $validated['quantity'],
                $validated['price'],
                $validated['return_status'],
                $validated['return_date']
            );

            if (!$return) {
                DB::rollBack();
                return back()->withErrors(['product_id' => 'Product not found.'])->withInput();
            }

            DB::commit();

            return redirect()->route('sales.returns.index')
                           ->with('success', 'Return created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating return: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to create return. Please try again.'])
                        ->withInput();
        }
    }

    /**
     * Display the specified return.
     */
    public function show(Returns $return)
    {
        $return->load('product');
        
        return view('sales.returns.show', compact('return'));
    }

    /**
     * Show the form for editing the specified return.
     */
    public function edit(Returns $return)
    {
        // Only allow editing of pending returns
        if (!$return->isPending()) {
            return redirect()->route('sales.returns.show', $return)
                           ->with('error', 'Only pending returns can be edited.');
        }

        $products = Product::orderBy('product_name')->get();
        $statuses = Returns::getStatuses();

        return view('sales.returns.edit', compact('return', 'products', 'statuses'));
    }

    /**
     * Update the specified return in storage.
     */
    public function update(Request $request, Returns $return)
    {
        // Only allow updating of pending returns
        if (!$return->isPending()) {
            return redirect()->route('sales.returns.show', $return)
                           ->with('error', 'Only pending returns can be updated.');
        }

        $validated = $request->validate([
            'product_id' => 'required|exists:products,product_id',
            'quantity' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
            'return_status' => 'required|in:' . implode(',', Returns::getStatuses()),
            'return_date' => 'required|date|before_or_equal:today',
        ]);

        try {
            $return->update($validated);

            return redirect()->route('sales.returns.show', $return)
                           ->with('success', 'Return updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating return: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to update return. Please try again.'])
                        ->withInput();
        }
    }

    /**
     * Remove the specified return from storage.
     */
    public function destroy(Returns $return)
    {
        // Only allow deletion of pending or rejected returns
        if (!in_array($return->return_status, [Returns::STATUS_PENDING, Returns::STATUS_REJECTED])) {
            return back()->with('error', 'Cannot delete processed returns.');
        }

        try {
            $return->delete();

            return redirect()->route('sales.returns.index')
                           ->with('success', 'Return deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting return: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to delete return. Please try again.');
        }
    }

    /**
     * Approve a return.
     */
    public function approve(Returns $return)
    {
        if (!$return->isPending()) {
            return back()->with('error', 'Only pending returns can be approved.');
        }

        try {
            DB::beginTransaction();

            if ($return->approve()) {
                DB::commit();
                return back()->with('success', 'Return approved successfully. Inventory has been updated.');
            } else {
                DB::rollBack();
                return back()->with('error', 'Failed to approve return.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error approving return: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to approve return. Please try again.');
        }
    }

    /**
     * Reject a return.
     */
    public function reject(Returns $return)
    {
        if (!$return->isPending()) {
            return back()->with('error', 'Only pending returns can be rejected.');
        }

        try {
            if ($return->reject()) {
                return back()->with('success', 'Return rejected successfully.');
            } else {
                return back()->with('error', 'Failed to reject return.');
            }

        } catch (\Exception $e) {
            Log::error('Error rejecting return: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to reject return. Please try again.');
        }
    }

    /**
     * Mark a return as processed.
     */
    public function markAsProcessed(Returns $return)
    {
        if (!$return->isApproved()) {
            return back()->with('error', 'Only approved returns can be marked as processed.');
        }

        try {
            if ($return->markAsProcessed()) {
                return back()->with('success', 'Return marked as processed successfully.');
            } else {
                return back()->with('error', 'Failed to mark return as processed.');
            }

        } catch (\Exception $e) {
            Log::error('Error marking return as processed: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to mark return as processed. Please try again.');
        }
    }

    /**
     * Get return analytics data.
     */
    public function analytics(Request $request)
    {
        $startDate = $request->get('start_date', now()->subMonth()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $analytics = [
            'total_returns' => Returns::whereBetween('return_date', [$startDate, $endDate])->count(),
            'total_return_value' => Returns::approved()
                                         ->whereBetween('return_date', [$startDate, $endDate])
                                         ->get()
                                         ->sum('total_amount'),
            'returns_by_status' => Returns::returnsByStatus()
                                        ->whereBetween('return_date', [$startDate, $endDate]),
            'most_returned_products' => Returns::mostReturnedProducts(10),
            'daily_returns' => Returns::selectRaw('DATE(return_date) as date, COUNT(*) as count, SUM(quantity * price) as total_amount')
                                    ->whereBetween('return_date', [$startDate, $endDate])
                                    ->groupBy('date')
                                    ->orderBy('date')
                                    ->get(),
        ];

        if ($request->wantsJson()) {
            return response()->json($analytics);
        }

        return view('sales.returns.analytics', compact('analytics', 'startDate', 'endDate'));
    }

    /**
     * Bulk approve returns.
     */
    public function bulkApprove(Request $request)
    {
        $validated = $request->validate([
            'return_ids' => 'required|array',
            'return_ids.*' => 'exists:returns,return_id',
        ]);

        try {
            DB::beginTransaction();

            $approvedCount = 0;
            $returns = Returns::whereIn('return_id', $validated['return_ids'])
                            ->pending()
                            ->get();

            foreach ($returns as $return) {
                if ($return->approve()) {
                    $approvedCount++;
                }
            }

            DB::commit();

            if ($approvedCount > 0) {
                return back()->with('success', "Successfully approved {$approvedCount} return(s).");
            } else {
                return back()->with('warning', 'No returns were approved.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error bulk approving returns: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to bulk approve returns. Please try again.');
        }
    }

    /**
     * Bulk reject returns.
     */
    public function bulkReject(Request $request)
    {
        $validated = $request->validate([
            'return_ids' => 'required|array',
            'return_ids.*' => 'exists:returns,return_id',
        ]);

        try {
            $rejectedCount = 0;
            $returns = Returns::whereIn('return_id', $validated['return_ids'])
                            ->pending()
                            ->get();

            foreach ($returns as $return) {
                if ($return->reject()) {
                    $rejectedCount++;
                }
            }

            if ($rejectedCount > 0) {
                return back()->with('success', "Successfully rejected {$rejectedCount} return(s).");
            } else {
                return back()->with('warning', 'No returns were rejected.');
            }

        } catch (\Exception $e) {
            Log::error('Error bulk rejecting returns: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to bulk reject returns. Please try again.');
        }
    }
}
