<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    /**
     * Display a listing of brand.
     */
    public function index(Request $request)
    {
        $query = Brand::query();
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('brand_code', 'like', '%' . $searchTerm . '%')
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
        
        if (in_array($sortField, ['name', 'brand_code', 'created_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('name', 'asc');
        }

        $brands = $query->paginate(10)->withQueryString();
        
        return view('master_data.brands.index', compact('brands'));
    }

    /**
     * Show the form for creating a new brand.
     */
    public function create()
    {
        return view('master_data.brands.create');
    }

    /**
     * Store a newly created brand.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'brand_code' => 'required|unique:brands,brand_code|max:20|alpha_dash',
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'logo' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_active'] = $request->has('is_active');

        Brand::create($data);

        return redirect()->route('master_data.brands.index')
                         ->with('success', 'Brand created successfully!');
    }

    /**
     * Display the specified brand.
     */
    public function show(Brand $brand)
    {
        // Get products count for this brand
        $productsCount = $brand->products()->count();
        
        return view('master_data.brands.show', compact('brand', 'productsCount'));
    }

    /**
     * Show the form for editing a brand.
     */
    public function edit(Brand $brand)
    {
        return view('master_data.brands.edit', compact('brand'));
    }

    /**
     * Update an existing brand.
     */
    public function update(Request $request, Brand $brand)
    {
        $validator = Validator::make($request->all(), [
            'brand_code' => 'required|max:20|alpha_dash|unique:brands,brand_code,' . $brand->id,
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'logo' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_active'] = $request->has('is_active');

        $brand->update($data);

        return redirect()->route('master_data.brands.index')
                         ->with('success', 'Brand updated successfully!');
    }

    /**
     * Delete a brand.
     */
    public function destroy(Brand $brand)
    {
        // Check if brand has associated products
        if ($brand->products()->count() > 0) {
            return redirect()->route('master_data.brands.index')
                           ->with('error', 'Cannot delete brand. It has associated products.');
        }

        $brand->delete();

        return redirect()->route('master_data.brands.index')
                         ->with('success', 'Brand deleted successfully!');
    }
}
