<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $query = Category::query();
        
        // Search functionality
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('category_code', 'like', '%' . $searchTerm . '%')
                  ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Sorting
        $sortField = $request->get('sort', 'updated_at');
        $sortDirection = $request->get('order', 'desc');
        
        if (in_array($sortField, ['name', 'category_code', 'created_at', 'updated_at'])) {
            $query->orderBy($sortField, $sortDirection);
        } else {
            $query->orderBy('updated_at', 'desc');
        }

        $categories = $query->paginate(10)->withQueryString();
        
        return view('master_data.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('master_data.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_code' => 'required|unique:categories,category_code|max:20|alpha_dash',
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

        Category::create($data);

        return redirect()->route('master_data.categories.index')
                         ->with('success', 'Category created successfully!');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        // Get products count for this category
        $productsCount = $category->products()->count();
        
        return view('master_data.categories.show', compact('category', 'productsCount'));
    }

    /**
     * Show the form for editing a category.
     */
    public function edit(Category $category)
    {
        return view('master_data.categories.edit', compact('category'));
    }

    /**
     * Update an existing category.
     */
    public function update(Request $request, Category $category)
    {
        $validator = Validator::make($request->all(), [
            'category_code' => 'required|max:20|alpha_dash|unique:categories,category_code,' . $category->id,
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

        $category->update($data);

        return redirect()->route('master_data.categories.index')
                         ->with('success', 'Category updated successfully!');
    }

    /**
     * Delete a category.
     */
    public function destroy(Category $category)
    {
        // Check if category has associated products
        if ($category->products()->count() > 0) {
            return redirect()->route('master_data.categories.index')
                           ->with('error', 'Cannot delete category. It has associated products.');
        }

        $category->delete();

        return redirect()->route('master_data.categories.index')
                         ->with('success', 'Category deleted successfully!');
    }
}
