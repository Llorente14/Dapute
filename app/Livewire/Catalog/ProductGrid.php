<?php

namespace App\Livewire\Catalog;

use App\Actions\Catalog\FetchActiveProductsAction;
use Livewire\Component;

class ProductGrid extends Component
{
    public function render(FetchActiveProductsAction $fetchActiveProducts)
    {
        $products = $fetchActiveProducts->execute();

        return view('livewire.catalog.product-grid', [
            'totalCount' => count($products),
        ])->layout('layouts.app');
    }
}
