<?php

namespace App\Livewire\Catalog;

use App\Actions\Catalog\FetchActiveProductsAction;
use Livewire\Component;

class ProductGrid extends Component
{
    public function render()
    {

        $products = app(FetchActiveProductsAction::class)->execute();

        return view('livewire.catalog.product-grid', [
            'products' => $products,
        ])->layout('layouts.app');
    }
}
