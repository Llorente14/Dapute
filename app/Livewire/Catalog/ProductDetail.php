<?php

namespace App\Livewire\Catalog;

use App\Actions\Catalog\FetchActiveProductsAction;
use Livewire\Component;

class ProductDetail extends Component
{
    public $product;

    public function mount($id)
    {
        // Get product from dummy data — no DB query
        $products = app(FetchActiveProductsAction::class)();
        $this->product = $products->firstWhere('id', (int) $id);

        if (! $this->product) {
            abort(404);
        }
    }

    public function render()
    {
        return view('livewire.catalog.product-detail', [
            'product' => $this->product,
        ])->layout('layouts.app');
    }
}
