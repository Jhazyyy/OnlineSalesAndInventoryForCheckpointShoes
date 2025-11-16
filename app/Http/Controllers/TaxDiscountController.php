<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\TaxDiscount;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaxDiscountController extends Controller
{
    /**
     * Display a listing of tax/discounts.
     */
    public function index(Request $request)
    {
        $query = TaxDiscount::query();
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('code', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by type
        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Filter by calculation method
        if ($request->has('calculation_method') && $request->calculation_method !== '') {
            $query->where('calculation_method', $request->calculation_method);
        }

        // Sorting
        $sortField = $request->get('sort', 'priority');
        $sortDirection = $request->get('order', 'asc');
        
        if (in_array($sortField, ['name', 'code', 'type', 'rate', 'priority', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('priority', 'asc');
        }

        $taxDiscounts = $query->paginate(10)->withQueryString();
        
        return view('master_data.tax_discounts.index', compact('taxDiscounts'));
    }

    /**
     * Show the form for creating a new tax/discount.
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $products = Product::orderBy('product_name')->get();
        
        return view('master_data.tax_discounts.create', compact('categories', 'products'));
    }

    /**
     * Store a newly created tax/discount.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|unique:tax_discounts,code|max:20|alpha_dash',
            'name' => 'required|string|max:100',
            'type' => 'required|in:tax,discount',
            'rate' => 'required_if:calculation_method,percentage|nullable|numeric|min:0|max:100',
            'calculation_method' => 'required|in:percentage,fixed',
            'fixed_amount' => 'required_if:calculation_method,fixed|nullable|numeric|min:0',
            'description' => 'nullable|string',
            'applies_to' => 'required|in:all,specific',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'is_compound' => 'boolean',
            // 'priority' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_active'] = $request->has('is_active');
        $data['is_compound'] = $request->has('is_compound');
        
        // Handle specific applications
        if ($data['applies_to'] === 'all') {
            $data['applicable_categories'] = null;
            $data['applicable_products'] = null;
        }

        TaxDiscount::create($data);

        return redirect()->route('master_data.tax_discounts.index')
                         ->with('success', ucfirst($data['type']) . ' created successfully!');
    }

    /**
     * Display the specified tax/discount.
     */
    public function show(TaxDiscount $taxDiscount)
    {
        $categories = $taxDiscount->categories();
        $products = $taxDiscount->products();
        
        return view('master_data.tax_discounts.show', compact('taxDiscount', 'categories', 'products'));
    }

    /**
     * Show the form for editing a tax/discount.
     */
    public function edit(TaxDiscount $taxDiscount)
    {
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        $products = Product::orderBy('product_name')->get();
        
        return view('master_data.tax_discounts.edit', compact('taxDiscount', 'categories', 'products'));
    }

    /**
     * Update an existing tax/discount.
     */
    public function update(Request $request, TaxDiscount $taxDiscount)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|max:20|alpha_dash|unique:tax_discounts,code,' . $taxDiscount->id,
            'name' => 'required|string|max:100',
            'type' => 'required|in:tax,discount',
            'rate' => 'required_if:calculation_method,percentage|nullable|numeric|min:0|max:100',
            'calculation_method' => 'required|in:percentage,fixed',
            'fixed_amount' => 'required_if:calculation_method,fixed|nullable|numeric|min:0',
            'description' => 'nullable|string',
            'applies_to' => 'required|in:all,specific',
            'applicable_categories' => 'nullable|array',
            'applicable_products' => 'nullable|array',
            'is_compound' => 'boolean',
            // 'priority' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'valid_from' => 'nullable|date',
            'valid_to' => 'nullable|date|after_or_equal:valid_from',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_active'] = $request->has('is_active');
        $data['is_compound'] = $request->has('is_compound');
        
        // Handle specific applications
        if ($data['applies_to'] === 'all') {
            $data['applicable_categories'] = null;
            $data['applicable_products'] = null;
        }

        $taxDiscount->update($data);

        return redirect()->route('master_data.tax_discounts.index')
                         ->with('success', ucfirst($data['type']) . ' updated successfully!');
    }

    /**
     * Remove the specified tax/discount.
     */
    public function destroy(TaxDiscount $taxDiscount)
    {
        $type = $taxDiscount->type;
        $taxDiscount->delete();

        return redirect()->route('master_data.tax_discounts.index')
                         ->with('success', ucfirst($type) . ' deleted successfully!');
    }

    /**
     * Toggle the active status of a tax/discount.
     */
    public function toggleStatus(TaxDiscount $taxDiscount)
    {
        $taxDiscount->is_active = !$taxDiscount->is_active;
        $taxDiscount->save();

        $status = $taxDiscount->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
                         ->with('success', ucfirst($taxDiscount->type) . ' ' . $status . ' successfully!');
    }

    /**
     * Calculate profit breakdown for demonstration.
     */
    public function profitBreakdown(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $breakdown = TaxDiscount::calculateProfitBreakdown(
            $request->cost_price,
            $request->selling_price,
            $request->quantity
        );

        return response()->json([
            'success' => true,
            'breakdown' => $breakdown
        ]);
    }

    /**
     * Calculate taxes and discounts for an order
     */
    public function calculateForOrder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:sales_order,purchase_order',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Create a temporary order object
        $items = collect($request->items)->map(function ($item) {
            return (object) [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
            ];
        });

        // Calculate subtotal
        $subtotal = (float) $items->sum(function ($item) {
            return (float) $item->quantity * (float) $item->unit_price;
        });

        $orderType = $request->type === 'purchase_order' ? 'purchase' : 'sales';
        $taxDiscountService = new \App\Services\TaxDiscountService();
        $result = $taxDiscountService->calculateForOrder($subtotal, $items, $orderType);

        return response()->json([
            'success' => true,
            'taxes' => $result['total_tax'],
            'discounts' => $result['total_discount'],
            'breakdown' => $result
        ]);
    }
}

