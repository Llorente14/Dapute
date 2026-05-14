<?php

namespace App\Livewire;

use Livewire\Component;

class LandingPage extends Component
{
    public string $activeFilter = 'all';

    public array $filterOptions = [
        'all'       => 'Semua Produk',
        'new'       => 'Terbaru',
    ];


    public function setFilter(string $filter): void
    {
        $this->activeFilter = $filter;
    }

    public function getFilteredProductsProperty(): array
    {
        return app(\App\Actions\Catalog\FetchActiveProductsAction::class)->execute(null, $this->activeFilter);
    }

    public function addToCart(\App\Actions\Cart\UpdateCartAction $action, string $productId)
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('login');
        }

        $result = $action->add(\Illuminate\Support\Facades\Auth::id(), $productId, 1);

        if ($result['success']) {
            $this->dispatch('cart-updated');
            $this->dispatch('open-cart');
        }
    }

    public function render()
    {
        return view('livewire.landing-page');
    }
}
