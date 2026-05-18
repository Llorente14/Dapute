<?php

namespace App\Livewire;

use Livewire\Component;

class Navbar extends Component
{
    public $links;

    public function mount($links = null)
    {
        // Default links if none are provided
        $this->links = $links ?? [
            ['name' => 'Home', 'url' => '/', 'icon' => 'home'],
            ['name' => 'Shop', 'url' => '/catalog', 'icon' => 'bakery_dining'],
            ['name' => 'Track', 'url' => '/tracking', 'icon' => 'local_shipping'],
            ['name' => 'Order', 'url' => '/order', 'icon' => 'receipt_long'],
        ];
    }

    public function render()
    {
        return view('livewire.navbar');
    }
}
