<?php

namespace App\Livewire\Catalog;

use App\Models\Product;
use Livewire\Component;

class ProductIndex extends Component
{
    public function render()
    {
        $products = Product::latest()->get();

        return view('livewire.catalog.product-index', [
            'products' => $products,
        ])->layout('layouts.admin');
    }
}
