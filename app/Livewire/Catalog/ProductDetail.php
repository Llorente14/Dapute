<?php

namespace App\Livewire\Catalog;

use App\Actions\Catalog\FetchActiveProductsAction;
use Livewire\Component;

class ProductDetail extends Component
{
    public $product;

    public $quantity = 1;

    public function mount($id)
    {
        $this->product = \Illuminate\Support\Facades\DB::table('products')
            ->where('id', $id)
            ->whereRaw('is_active = true')
            ->first();

        if (! $this->product) {
            abort(404);
        }
    }

    public function updatedQuantity($value)
    {
        $val = (int) $value;
        if ($val > 99) {
            $this->quantity = 99;
        } elseif ($val < 1) {
            $this->quantity = 1;
        } else {
            $this->quantity = $val;
        }
    }

    public function incrementQty()
    {
        if ($this->quantity < 99) {
            $this->quantity++;
        }
    }

    public function decrementQty()
    {
        if ($this->quantity > 1) {
            $this->quantity--;
        }
    }

    public function addToCart(\App\Actions\Cart\UpdateCartAction $action)
    {
        if (!\Illuminate\Support\Facades\Auth::check()) {
            return redirect()->route('login');
        }

        $result = $action->add(
            \Illuminate\Support\Facades\Auth::id(), 
            $this->product->id, 
            $this->quantity
        );

        if ($result['success']) {
            $this->dispatch('cart-updated');
            $this->dispatch('open-cart');
            $this->quantity = 1; // reset after add
        }
    }

    public function render()
    {
        return view('livewire.catalog.product-detail', [
            'product' => $this->product,
        ])->layout('layouts.app');
    }
}
