<?php

namespace App\Livewire;

use App\Actions\Cart\UpdateCartAction;
use App\Actions\Catalog\FetchActiveProductsAction;
use Livewire\Component;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

class CatalogFilter extends Component
{
    #[Url(except: '')]
    public $search = '';

    public $sort = 'Terbaru';

    public function addToCart(UpdateCartAction $action, string $productId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $result = $action->add(Auth::id(), $productId, 1);

        if ($result['success']) {
            $this->dispatch('cart-updated');
            $this->dispatch('open-cart');
        }
    }

    public function render(FetchActiveProductsAction $fetchActiveProducts)
    {
        $products = $fetchActiveProducts->execute($this->search, $this->sort);

        return view('livewire.catalog-filter', [
            'products' => $products,
        ]);
    }
}
