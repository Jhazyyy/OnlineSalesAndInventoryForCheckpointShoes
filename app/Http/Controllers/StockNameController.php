<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\StockName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StockNameController extends Controller
{
    /**
     * Display a listing of stock names.
     */
    public function index(Request $request)
    {
        $query = StockName::query();
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('stock_code', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Sorting
        $sortField = $request->get('sort', 'name');
        $sortDirection = $request->get('order', 'asc');
        
        if (in_array($sortField, ['name', 'stock_code', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        $stockNames = $query->paginate(10)->withQueryString();
        
        return view('master_data.stock_names.index', compact('stockNames'));
    }

    /**
     * Show the form for creating a new stock name.
     */
    public function create()
    {
        return view('master_data.stock_names.create');
    }

    /**
     * Store a newly created stock name.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'stock_code' => 'required|unique:stock_names,stock_code|max:20|alpha_dash',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_active'] = $request->has('is_active');

        StockName::create($data);

        return redirect()->route('master_data.stock_names.index')
                         ->with('success', 'Stock name created successfully!');
    }

    /**
     * Display the specified stock name.
     */
    public function show(StockName $stockName)
    {
        // Get products count for this stock name
        $productsCount = $stockName->products()->count();
        
        return view('master_data.stock_names.show', compact('stockName', 'productsCount'));
    }

    /**
     * Show the form for editing a stock name.
     */
    public function edit(StockName $stockName)
    {
        return view('master_data.stock_names.edit', compact('stockName'));
    }

    /**
     * Update an existing stock name.
     */
    public function update(Request $request, StockName $stockName)
    {
        $validator = Validator::make($request->all(), [
            'stock_code' => 'required|max:20|alpha_dash|unique:stock_names,stock_code,' . $stockName->id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_active'] = $request->has('is_active');

        $stockName->update($data);

        return redirect()->route('master_data.stock_names.index')
                         ->with('success', 'Stock name updated successfully!');
    }

    /**
     * Delete a stock name.
     */
    public function destroy(StockName $stockName)
    {
        // Check if stock name has associated products
        if ($stockName->products()->count() > 0) {
            return redirect()->route('master_data.stock_names.index')
                           ->with('error', 'Cannot delete stock name. It has associated products.');
        }

        $stockName->delete();

        return redirect()->route('master_data.stock_names.index')
                         ->with('success', 'Stock name deleted successfully!');
    }
}
