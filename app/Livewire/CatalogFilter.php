<?php

namespace App\Livewire;

use App\Actions\Catalog\FetchActiveProductsAction;
use Livewire\Component;
use Livewire\Attributes\Url;

class CatalogFilter extends Component
{
    #[Url(except: '')]
    public $search = '';

    public function render(FetchActiveProductsAction $fetchActiveProducts)
    {
        $products = $fetchActiveProducts->execute($this->search);

        return view('livewire.catalog-filter', [
            'products' => $products,
        ]);
    }
}
