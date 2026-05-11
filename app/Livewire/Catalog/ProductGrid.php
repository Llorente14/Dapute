<?php

namespace App\Livewire\Catalog;

use App\Actions\Catalog\FetchActiveProductsAction;
use Livewire\Component;

class ProductGrid extends Component
{
    public function render()
    {
        // Dummy data — no DB query
        $products = app(FetchActiveProductsAction::class)();

        return view('livewire.catalog.product-grid', [
            'products' => $products,
        ])->layout('layouts.app');
    }
}
