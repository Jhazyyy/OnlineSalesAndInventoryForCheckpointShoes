<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory items (aggregated per product).
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $sort = $request->get('sort', 'product_name');
        $order = $request->get('order', 'asc');

        $query = Inventory::query()
            ->join('products', 'products.product_id', '=', 'inventories.product_id')
            ->selectRaw('inventories.product_id')
            ->selectRaw('MIN(inventories.sku) as sku')
            ->selectRaw('products.product_name, products.product_category, products.reorder_level')
            ->selectRaw('SUM(inventories.quantity_on_hand) as total_stock')
            ->selectRaw('SUM(inventories.quantity_reserved) as reserved')
            ->groupBy('inventories.product_id', 'products.product_name', 'products.product_category', 'products.reorder_level');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('products.product_name', 'like', "%{$search}%")
                  ->orWhere('products.product_category', 'like', "%{$search}%")
                  ->orWhere('inventories.sku', 'like', "%{$search}%");
            });
        }

        // Sorting
        $query->orderBy(match($sort) {
            'total_stock' => 'total_stock',
            'reserved' => 'reserved',
            'category' => 'products.product_category',
            'sku' => 'sku',
            default => 'products.product_name',
        }, $order);

        $inventories = $query->paginate(20)->withQueryString();

        return view('inventory.index', [
            'inventories' => $inventories,
        ]);
    }
}
