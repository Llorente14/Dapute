<?php

namespace App\Livewire\Transaction;

use Livewire\Component;
use App\Actions\Cart\UpdateCartAction;
use App\Helpers\AddressManager;
use Illuminate\Support\Facades\Auth;

class CheckoutForm extends Component
{
    public $items = [];
    public $subtotal = 0;
    
    public $recipient_name = '';
    public array $selectedAddress = [];

    public $couriers = [];
    public $selectedCourier = null;
    public $shippingCost = 0;
    public $adminFee = 2500;
    public $total = 0;

    public $isProcessing = false;

    public function mount()
    {
        $this->loadCart();
        
        if (Auth::check()) {
            $this->recipient_name = Auth::user()->name;
        }
    }

    public function loadCart()
    {
        if (!Auth::check()) {
            return;
        }

        $action = app(UpdateCartAction::class);
        $userId = Auth::id();
        
        $this->items = $action->getItems($userId);
        
        $this->subtotal = collect($this->items)->sum(function($item) {
            return $item['price_snapshot'] * $item['quantity'];
        });

        $this->calculateTotal();
    }

    public function fetchCouriers()
    {
        // Simulate fetch delay to show skeleton loader
        sleep(1);

        $this->couriers = [
            [
                'id' => 'pickup',
                'name' => 'Self Pickup',
                'estimate' => 'Ready in 1 hour',
                'price' => 0,
                'icon' => 'directions_walk'
            ],
            [
                'id' => 'standard',
                'name' => 'Standard Delivery',
                'estimate' => '3-5 business days',
                'price' => 10000,
                'icon' => 'local_shipping'
            ],
            [
                'id' => 'express',
                'name' => 'Express Delivery',
                'estimate' => 'Next business day',
                'price' => 25000,
                'icon' => 'rocket_launch'
            ]
        ];
    }

    public function updatedSelectedCourier($value)
    {
        $selected = collect($this->couriers)->firstWhere('id', $value);
        if ($selected) {
            $this->shippingCost = $selected['price'];
        } else {
            $this->shippingCost = 0;
        }
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        $this->total = $this->subtotal + $this->shippingCost + $this->adminFee;
    }

    public function processPayment()
    {
        if (!$this->selectedCourier) {
            return;
        }

        $addressValidation = AddressManager::validateAddress($this->selectedAddress);
        if (!$addressValidation['valid']) {
            $this->resetErrorBag();
            $this->addError('selectedAddress', 'Please complete a valid shipping address.');

            foreach ($addressValidation['errors'] as $field => $messages) {
                $this->addError('selectedAddress.'.$field, $messages[0] ?? 'Invalid value.');
            }

            return;
        }
        
        $this->isProcessing = true;
        
        // mock payment process
        sleep(2);
        
        $this->isProcessing = false;
        
        session()->flash('message', 'Payment successful!');
        return redirect()->to('/orders');
    }
    public function render()
    {
        return view('livewire.transaction.checkout-form')
                ->layout('layouts.checkout');
    }
}
