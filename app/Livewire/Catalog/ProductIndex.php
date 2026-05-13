<?php

namespace App\Livewire\Catalog;

use Livewire\Component;

class ProductIndex extends Component
{
    public function render()
    {
        return view('livewire.catalog.product-index')
            ->layout('layouts.admin');
    }
}
