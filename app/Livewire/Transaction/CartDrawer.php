<?php

namespace App\Livewire\Transaction;

use Livewire\Component;
use App\Actions\Cart\UpdateCartAction;
use Illuminate\Support\Facades\Auth;

class CartDrawer extends Component
{
    public $items = [];
    public $subtotal = 0;
    public $cartCount = 0;

    protected $listeners = [
        'cart-updated' => 'loadCart'
    ];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        if (!Auth::check()) {
            return;
        }

        $action = app(UpdateCartAction::class);
        $userId = Auth::id();
        
        $this->items = $action->getItems($userId);
        $this->cartCount = $action->getCount($userId);
        
        $this->subtotal = collect($this->items)->sum(function($item) {
            return $item['price_snapshot'] * $item['quantity'];
        });

        // dispatch event to update badge in navbar
        $this->dispatch('cart-count-updated', count: $this->cartCount);
    }

    public function incrementQty(UpdateCartAction $action, string $cartItemId)
    {
        if (!Auth::check()) return;
        $action->increment($cartItemId, Auth::id());
        $this->loadCart();
    }

    public function decrementQty(UpdateCartAction $action, string $cartItemId)
    {
        if (!Auth::check()) return;
        $action->decrement($cartItemId, Auth::id());
        $this->loadCart();
    }

    public function removeItem(UpdateCartAction $action, string $cartItemId)
    {
        if (!Auth::check()) return;
        $action->remove($cartItemId, Auth::id());
        $this->loadCart();
    }

    public function render()
    {
        return view('livewire.transaction.cart-drawer');
    }
}
