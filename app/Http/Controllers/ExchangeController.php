<?php

namespace App\Http\Controllers;

use App\Models\Exchange;
use App\Models\ExchangeItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\SalesOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ExchangeController extends Controller
{
    /**
     * Display a listing of the exchanges.
     */
    public function index(Request $request)
    {
        $query = Exchange::with(['customer', 'salesOrder']);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by exchange type if provided
        if ($request->filled('exchange_type')) {
            $query->byType($request->exchange_type);
        }

        // Filter by date range if provided
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('exchange_date', [$request->start_date, $request->end_date]);
        }

        // Search by exchange number or customer name
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('exchange_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('customer', function ($customerQuery) use ($request) {
                      $customerQuery->where('customer_name', 'like', '%' . $request->search . '%');
                  });
            });
        }

        // Sorting
        $sortField = $request->get('sort', 'exchange_date');
        $sortOrder = $request->get('order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $exchanges = $query->paginate(15);

        // Get statistics for the filter sidebar
        $statistics = [
            'total_exchanges' => Exchange::count(),
            'pending_exchanges' => Exchange::pending()->count(),
            'approved_exchanges' => Exchange::approved()->count(),
            'processing_exchanges' => Exchange::processing()->count(),
            'completed_exchanges' => Exchange::completed()->count(),
            'today_exchanges' => Exchange::today()->count(),
            'this_week_exchanges' => Exchange::thisWeek()->count(),
            'this_month_exchanges' => Exchange::thisMonth()->count(),
        ];

        // Get filter options
        $statuses = Exchange::getStatuses();
        $types = Exchange::getTypes();

        return view('sales.exchanges.index', compact('exchanges', 'statistics', 'statuses', 'types'));
    }

    /**
     * Show the form for creating a new exchange.
     */
    public function create(Request $request)
    {
        $customers = Customer::where('status', 'active')
                           ->orderBy('customer_name')
                           ->get();

        $products = Product::where('stock_quantity', '>', 0)
                          ->orderBy('product_name')
                          ->get();
        
        $statuses = Exchange::getStatuses();
        $types = Exchange::getTypes();

        // If creating from a sales order
        $salesOrder = null;
        if ($request->filled('sales_order_id')) {
            $salesOrder = SalesOrder::with('items.product')
                                   ->find($request->sales_order_id);
        }

        return view('sales.exchanges.create', compact('customers', 'products', 'statuses', 'types', 'salesOrder'));
    }

    /**
     * Store a newly created exchange in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'sales_order_id' => 'nullable|exists:sales_orders,sales_order_id',
            'exchange_type' => 'required|in:' . implode(',', Exchange::getTypes()),
            'status' => 'required|in:' . implode(',', Exchange::getStatuses()),
            'reason' => 'nullable|string|max:500',
            'exchange_date' => 'required|date|before_or_equal:today',
            'requested_completion_date' => 'nullable|date|after_or_equal:exchange_date',
            'notes' => 'nullable|string|max:1000',
            'internal_notes' => 'nullable|string|max:1000',
            
            // Original items (being returned)
            'original_items' => 'required|array|min:1',
            'original_items.*.product_id' => 'required|exists:products,product_id',
            'original_items.*.quantity' => 'required|integer|min:1',
            'original_items.*.unit_price' => 'required|numeric|min:0',
            'original_items.*.condition' => 'nullable|string|max:50',
            'original_items.*.notes' => 'nullable|string|max:255',
            
            // New items (being given)
            'new_items' => 'nullable|array',
            'new_items.*.product_id' => 'required|exists:products,product_id',
            'new_items.*.quantity' => 'required|integer|min:1',
            'new_items.*.unit_price' => 'required|numeric|min:0',
            'new_items.*.condition' => 'nullable|string|max:50',
            'new_items.*.notes' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Calculate totals
            $originalTotal = 0;
            foreach ($validated['original_items'] as $item) {
                $originalTotal += $item['quantity'] * $item['unit_price'];
            }

            $newTotal = 0;
            if (isset($validated['new_items'])) {
                foreach ($validated['new_items'] as $item) {
                    $newTotal += $item['quantity'] * $item['unit_price'];
                }
            }

            // Create the exchange
            $exchange = Exchange::create([
                'customer_id' => $validated['customer_id'],
                'sales_order_id' => $validated['sales_order_id'] ?? null,
                'exchange_type' => $validated['exchange_type'],
                'status' => $validated['status'],
                'reason' => $validated['reason'] ?? null,
                'original_total_amount' => $originalTotal,
                'new_total_amount' => $newTotal,
                'difference_amount' => $newTotal - $originalTotal,
                'exchange_date' => $validated['exchange_date'],
                'requested_completion_date' => $validated['requested_completion_date'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'internal_notes' => $validated['internal_notes'] ?? null,
                'processed_by' => Auth::id(),
            ]);

            // Create original items
            foreach ($validated['original_items'] as $item) {
                ExchangeItem::create([
                    'exchange_id' => $exchange->exchange_id,
                    'item_type' => ExchangeItem::TYPE_ORIGINAL,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'condition' => $item['condition'] ?? null,
                    'notes' => $item['notes'] ?? null,
                ]);
            }

            // Create new items if provided
            if (isset($validated['new_items'])) {
                foreach ($validated['new_items'] as $item) {
                    ExchangeItem::create([
                        'exchange_id' => $exchange->exchange_id,
                        'item_type' => ExchangeItem::TYPE_NEW,
                        'product_id' => $item['product_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['quantity'] * $item['unit_price'],
                        'condition' => $item['condition'] ?? null,
                        'notes' => $item['notes'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('sales.exchanges.index')
                           ->with('success', 'Exchange created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating exchange: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to create exchange. Please try again.'])
                        ->withInput();
        }
    }

    /**
     * Display the specified exchange.
     */
    public function show(Exchange $exchange)
    {
        $exchange->load([
            'customer',
            'salesOrder',
            'processedBy',
            'items.product',
            'originalItems.product',
            'newItems.product'
        ]);
        
        return view('sales.exchanges.show', compact('exchange'));
    }

    /**
     * Show the form for editing the specified exchange.
     */
    public function edit(Exchange $exchange)
    {
        // Only allow editing of pending or approved exchanges
        if (!in_array($exchange->status, [Exchange::STATUS_PENDING, Exchange::STATUS_APPROVED])) {
            return redirect()->route('sales.exchanges.show', $exchange)
                           ->with('error', 'Only pending or approved exchanges can be edited.');
        }

        $exchange->load(['items.product', 'originalItems.product', 'newItems.product']);

        $customers = Customer::where('status', 'active')
                           ->orderBy('customer_name')
                           ->get();

        $products = Product::orderBy('product_name')->get();
        $statuses = Exchange::getStatuses();
        $types = Exchange::getTypes();

        return view('sales.exchanges.edit', compact('exchange', 'customers', 'products', 'statuses', 'types'));
    }

    /**
     * Update the specified exchange in storage.
     */
    public function update(Request $request, Exchange $exchange)
    {
        // Only allow updating of pending or approved exchanges
        if (!in_array($exchange->status, [Exchange::STATUS_PENDING, Exchange::STATUS_APPROVED])) {
            return redirect()->route('sales.exchanges.show', $exchange)
                           ->with('error', 'Only pending or approved exchanges can be updated.');
        }

        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,customer_id',
            'sales_order_id' => 'nullable|exists:sales_orders,sales_order_id',
            'exchange_type' => 'required|in:' . implode(',', Exchange::getTypes()),
            'status' => 'required|in:' . implode(',', Exchange::getStatuses()),
            'reason' => 'nullable|string|max:500',
            'exchange_date' => 'required|date|before_or_equal:today',
            'requested_completion_date' => 'nullable|date|after_or_equal:exchange_date',
            'notes' => 'nullable|string|max:1000',
            'internal_notes' => 'nullable|string|max:1000',
        ]);

        try {
            $exchange->update($validated);

            return redirect()->route('sales.exchanges.show', $exchange)
                           ->with('success', 'Exchange updated successfully.');

        } catch (\Exception $e) {
            Log::error('Error updating exchange: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Failed to update exchange. Please try again.'])
                        ->withInput();
        }
    }

    /**
     * Remove the specified exchange from storage.
     */
    public function destroy(Exchange $exchange)
    {
        // Only allow deletion of pending or cancelled exchanges
        if (!in_array($exchange->status, [Exchange::STATUS_PENDING, Exchange::STATUS_CANCELLED])) {
            return back()->with('error', 'Cannot delete processed exchanges.');
        }

        try {
            $exchange->delete();

            return redirect()->route('sales.exchanges.index')
                           ->with('success', 'Exchange deleted successfully.');

        } catch (\Exception $e) {
            Log::error('Error deleting exchange: ' . $e->getMessage());
            
            return back()->with('error', 'Failed to delete exchange. Please try again.');
        }
    }

    /**
     * Approve an exchange.
     */
    public function approve(Exchange $exchange)
    {
        if (!$exchange->isPending()) {
            return back()->with('error', 'Only pending exchanges can be approved.');
        }

        try {
            $exchange->update([
                'status' => Exchange::STATUS_APPROVED,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            return back()->with('success', 'Exchange approved successfully.');

        } catch (\Exception $e) {
            Log::error('Error approving exchange: ' . $e->getMessage());
            return back()->with('error', 'Failed to approve exchange. Please try again.');
        }
    }

    /**
     * Start processing an exchange.
     */
    public function startProcessing(Exchange $exchange)
    {
        if (!$exchange->isApproved()) {
            return back()->with('error', 'Only approved exchanges can be processed.');
        }

        try {
            $exchange->update([
                'status' => Exchange::STATUS_PROCESSING,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            return back()->with('success', 'Exchange processing started.');

        } catch (\Exception $e) {
            Log::error('Error starting exchange processing: ' . $e->getMessage());
            return back()->with('error', 'Failed to start processing. Please try again.');
        }
    }

    /**
     * Complete an exchange.
     */
    public function complete(Exchange $exchange)
    {
        if (!$exchange->isProcessing()) {
            return back()->with('error', 'Only processing exchanges can be completed.');
        }

        try {
            $exchange->update([
                'status' => Exchange::STATUS_COMPLETED,
                'actual_completion_date' => now(),
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            return back()->with('success', 'Exchange completed successfully.');

        } catch (\Exception $e) {
            Log::error('Error completing exchange: ' . $e->getMessage());
            return back()->with('error', 'Failed to complete exchange. Please try again.');
        }
    }

    /**
     * Cancel an exchange.
     */
    public function cancel(Exchange $exchange)
    {
        if (in_array($exchange->status, [Exchange::STATUS_COMPLETED, Exchange::STATUS_CANCELLED])) {
            return back()->with('error', 'Cannot cancel completed or already cancelled exchanges.');
        }

        try {
            $exchange->update([
                'status' => Exchange::STATUS_CANCELLED,
                'processed_by' => Auth::id(),
                'processed_at' => now(),
            ]);

            return back()->with('success', 'Exchange cancelled successfully.');

        } catch (\Exception $e) {
            Log::error('Error cancelling exchange: ' . $e->getMessage());
            return back()->with('error', 'Failed to cancel exchange. Please try again.');
        }
    }
}
