<?php

/**
 * Example Routes with Role-Based Access Control
 * 
 * This file demonstrates how to protect routes using roles and permissions
 * Copy these examples to your routes/web.php file
 */

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingsController;

// ============================================
// ADMIN ONLY ROUTES
// ============================================

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // User Management (Admin only)
    Route::resource('users', UserController::class);
    
    // System Settings (Admin only)
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    
    // Advanced Reports (Admin only)
    Route::get('/reports/advanced', [ReportController::class, 'advanced'])->name('reports.advanced');
});

// ============================================
// ADMIN OR USER ROUTES (Both roles)
// ============================================

Route::middleware(['auth', 'role:admin,user'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Products (View for both, edit based on permission)
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    
    // Sales (View for both, create for both)
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/{sale}', [SaleController::class, 'show'])->name('sales.show');
});

// ============================================
// PERMISSION-BASED ROUTES
// ============================================

// Create Products (requires 'create products' permission)
Route::middleware(['auth', 'permission:create products'])
    ->post('/products', [ProductController::class, 'store'])
    ->name('products.store');

// Edit Products (requires 'edit products' permission)
Route::middleware(['auth', 'permission:edit products'])->group(function () {
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
});

// Delete Products (requires 'delete products' permission)
Route::middleware(['auth', 'permission:delete products'])
    ->delete('/products/{product}', [ProductController::class, 'destroy'])
    ->name('products.destroy');

// Create Sales (requires 'create sales' permission)
Route::middleware(['auth', 'permission:create sales'])
    ->post('/sales', [SaleController::class, 'store'])
    ->name('sales.store');

// Delete Sales (requires 'delete sales' permission - Admin only typically)
Route::middleware(['auth', 'permission:delete sales'])
    ->delete('/sales/{sale}', [SaleController::class, 'destroy'])
    ->name('sales.destroy');

// ============================================
// MIXED PROTECTION EXAMPLES
// ============================================

// Route accessible by both roles but with different views
Route::middleware(['auth', 'role:admin,user'])->group(function () {
    Route::get('/reports', function () {
        $user = auth()->user();
        
        // Admins see all reports
        if ($user->hasRole('admin')) {
            return view('reports.admin');
        }
        
        // Users see limited reports
        return view('reports.user');
    })->name('reports.index');
});

// ============================================
// CONTROLLER EXAMPLES
// ============================================

/**
 * Example: ProductController with role-based logic
 */
class ProductControllerExample
{
    public function index()
    {
        // Both admin and user can view products
        $products = Product::query();
        
        // Admin sees all products including inactive
        if (auth()->user()->hasRole('admin')) {
            // No filter
        } else {
            // Regular users see only active products
            $products->where('status', 'active');
        }
        
        return view('products.index', [
            'products' => $products->paginate(20)
        ]);
    }
    
    public function store(Request $request)
    {
        // Check permission in controller (additional security layer)
        if (!auth()->user()->hasPermissionTo('create products')) {
            abort(403, 'You do not have permission to create products.');
        }
        
        // Create product logic here
    }
}

// ============================================
// BLADE VIEW EXAMPLES
// ============================================

/**
 * Example: products/index.blade.php
 */
/*
@extends('layouts.app')

@section('content')
    <h1>Products</h1>
    
    {{-- Show create button only for users with permission --}}
    @can('create products')
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            Create Product
        </a>
    @endcan
    
    <table>
        @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>
                    {{-- Show edit button only for admins --}}
                    @role('admin')
                        <a href="{{ route('products.edit', $product) }}">Edit</a>
                    @endrole
                    
                    {{-- Show delete button only for users with permission --}}
                    @can('delete products')
                        <form action="{{ route('products.destroy', $product) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    @endcan
                </td>
            </tr>
        @endforeach
    </table>
@endsection
*/

// ============================================
// TESTING ROUTES (Development Only)
// ============================================

if (app()->environment('local')) {
    Route::get('/test-roles', function () {
        $user = auth()->user();
        
        return [
            'user' => $user->name,
            'email' => $user->email,
            'roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'is_admin' => $user->isAdmin(),
            'is_user' => $user->isUser(),
            'primary_role' => $user->primary_role,
        ];
    })->middleware('auth');
}
