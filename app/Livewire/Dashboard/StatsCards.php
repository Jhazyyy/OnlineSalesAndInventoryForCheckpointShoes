<?php

namespace App\Livewire\Dashboard;

use App\Models\Product;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Returns;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class StatsCards extends Component
{
    public $autoRefresh = false;

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = !$this->autoRefresh;
    }

    public function getInventoryStatsProperty()
    {
        return [
            'total_products' => Product::count(),
            // Standard formula: quantity > 0 AND quantity <= 10
            'low_stock_products' => Product::where('quantity', '>', 0)
                                          ->where('quantity', '<=', 10)
                                          ->count(),
            'out_of_stock' => Product::where('quantity', '<=', 0)->count(),
            'total_value' => (float) Product::select(DB::raw('SUM(quantity * price) as total'))
                ->value('total') ?? 0,
        ];
    }

    public function getSalesStatsProperty()
    {
        return [
            'total_sales' => Sale::count(),
            'total_sales_value' => (float) (Sale::sum('total_amount') ?? 0),
            'today_sales' => Sale::whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->count(),
            'today_sales_value' => (float) (Sale::whereBetween('created_at', [now()->startOfDay(), now()->endOfDay()])->sum('total_amount') ?? 0),
        ];
    }

    public function getCustomerStatsProperty()
    {
        return [
            'total_customers' => Customer::count(),
            // Count distinct customers with sales in the last 30 days to avoid relying on relation naming
            'active_customers' => Sale::where('created_at', '>=', now()->subDays(30))
                ->distinct('customer_id')
                ->count('customer_id'),
            'new_this_month' => Customer::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    public function getReturnStatsProperty()
    {
        return [
            'total_returns' => Returns::count(),
            'pending_returns' => Returns::pending()->count(),
            'this_month' => Returns::thisMonth()->count(),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard.stats-cards', [
            'inventoryStats' => $this->inventoryStats,
            'salesStats' => $this->salesStats,
            'customerStats' => $this->customerStats,
            'returnStats' => $this->returnStats,
        ]);
    }
}
