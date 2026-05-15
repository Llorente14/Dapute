<?php

namespace App\Livewire\Transaction;

use Livewire\Component;
use App\Actions\Cart\UpdateCartAction;
use Illuminate\Support\Facades\Auth;

class CheckoutForm extends Component
{
    public $items = [];
    public $subtotal = 0;
    
    // Address fields
    public $recipient_name = '';
    public $recipient_phone = '';
    public $address = '';
    public $city = '';
    public $state = '';
    public $postal_code = '';

    public $provinces = [
        'Aceh', 'Sumatera Utara', 'Sumatera Barat', 'Riau', 'Kepulauan Riau', 'Jambi', 
        'Sumatera Selatan', 'Kepulauan Bangka Belitung', 'Bengkulu', 'Lampung', 
        'DKI Jakarta', 'Banten', 'Jawa Barat', 'Jawa Tengah', 'DI Yogyakarta', 
        'Jawa Timur', 'Bali', 'Nusa Tenggara Barat', 'Nusa Tenggara Timur', 
        'Kalimantan Barat', 'Kalimantan Tengah', 'Kalimantan Selatan', 'Kalimantan Timur', 
        'Kalimantan Utara', 'Sulawesi Utara', 'Gorontalo', 'Sulawesi Tengah', 
        'Sulawesi Barat', 'Sulawesi Selatan', 'Sulawesi Tenggara', 'Maluku', 
        'Maluku Utara', 'Papua Barat', 'Papua Barat Daya', 'Papua', 
        'Papua Selatan', 'Papua Tengah', 'Papua Pegunungan'
    ];

    // Dropdown address properties
    public $showAddressDropdown = false;
    public $suggestedAddresses = [
        '123 Forest Brutalist St, Jakarta',
        'Jl. Jend. Sudirman Kav 45, Jakarta Selatan',
        'Jl. Gatot Subroto No. 12, Jakarta Pusat',
        'Komp. Neo-Brutalist Blok C/9, Bandung',
        'Jl. Pahlawan No. 1, Surabaya'
    ];
    public $filteredAddresses = [];

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
            // Just populate mock address fields to show they are editable
            $this->address = '123 Forest Brutalist St';
            $this->city = 'Jakarta';
            $this->state = 'DKI Jakarta';
            $this->postal_code = '12345';
        }
    }

    #[\Livewire\Attributes\Computed]
    public function filteredProvinces()
    {
        if (empty($this->state)) {
            return $this->provinces;
        }
        return collect($this->provinces)
            ->filter(fn($prov) => str_contains(strtolower($prov), strtolower($this->state)))
            ->values()
            ->toArray();
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

    public function updatedAddress($value)
    {
        if (strlen($value) > 0) {
            $this->filteredAddresses = collect($this->suggestedAddresses)
                ->filter(fn($addr) => str_contains(strtolower($addr), strtolower($value)))
                ->values()
                ->toArray();
            $this->showAddressDropdown = count($this->filteredAddresses) > 0;
        } else {
            $this->showAddressDropdown = false;
        }
    }

    public function selectAddress($addr)
    {
        $this->address = $addr;
        $this->showAddressDropdown = false;
        
        // Auto fill city/state based on selection (mock)
        if (str_contains(strtolower($addr), 'jakarta')) {
            $this->city = 'Jakarta';
            $this->state = 'DKI Jakarta';
            $this->postal_code = '10000';
        } elseif (str_contains(strtolower($addr), 'bandung')) {
            $this->city = 'Bandung';
            $this->state = 'Jawa Barat'; 
            $this->postal_code = '40111';
        } elseif (str_contains(strtolower($addr), 'surabaya')) {
            $this->city = 'Surabaya';
            $this->state = 'Jawa Timur'; 
            $this->postal_code = '60111';
        }
    }

    public function fetchCouriers()
    {
        // Simulate fetch delay to show skeleton loader
        sleep(1);

        $this->couriers = [
            [
                'id' => 'standard',
                'name' => 'Standard',
                'estimate' => '3-5 business days',
                'price' => 0,
                'icon' => 'local_shipping'
            ],
            [
                'id' => 'express',
                'name' => 'Express',
                'estimate' => 'Next business day',
                'price' => 15000,
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
