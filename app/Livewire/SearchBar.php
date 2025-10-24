<?php

namespace App\Livewire;

use App\Models\Product;
use App\Models\Customer;
use App\Models\SalesOrder;
use Livewire\Component;

class SearchBar extends Component
{
    public $query = '';
    public $results = [];
    public $showResults = false;

    public function updatedQuery()
    {
        if (strlen($this->query) < 2) {
            $this->results = [];
            $this->showResults = false;
            return;
        }

        $this->results = [
            'products' => Product::where('name', 'like', '%' . $this->query . '%')
                ->orWhere('sku', 'like', '%' . $this->query . '%')
                ->limit(5)
                ->get(),
            'customers' => Customer::where('name', 'like', '%' . $this->query . '%')
                ->orWhere('email', 'like', '%' . $this->query . '%')
                ->limit(5)
                ->get(),
            'orders' => SalesOrder::where('order_number', 'like', '%' . $this->query . '%')
                ->limit(5)
                ->get(),
        ];

        $this->showResults = true;
    }

    public function clearSearch()
    {
        $this->query = '';
        $this->results = [];
        $this->showResults = false;
    }

    public function render()
    {
        return view('livewire.search-bar');
    }
}
