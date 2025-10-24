<?php

namespace App\Livewire;

use App\Models\Inventory;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryList extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'product_id';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $stockFilter = 'all'; // all, low, out

    protected $queryString = [
        'search' => ['except' => ''],
        'stockFilter' => ['except' => 'all'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStockFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

    public function render()
    {
        $inventory = Inventory::query()
            ->with(['product'])
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                        ->orWhere('sku', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->stockFilter === 'low', function ($query) {
                $query->where('quantity', '>', 0)
                    ->where('quantity', '<=', 10);
            })
            ->when($this->stockFilter === 'out', function ($query) {
                $query->where('quantity', '<=', 0);
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.inventory-list', [
            'inventory' => $inventory,
        ]);
    }
}
