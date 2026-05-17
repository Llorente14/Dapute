<?php

namespace App\Livewire\Catalog;

use App\Models\Product;
use Livewire\Component;

class ProductIndex extends Component
{
    public function delete($productId)
    {
        \Illuminate\Support\Facades\DB::table('products')->where('id', $productId)->delete();
        session()->flash('success', 'Produk berhasil dihapus.');
    }

    public function render()
    {
        $products = Product::latest()->get();

        return view('livewire.catalog.product-index', [
            'products' => $products,
        ])->layout('layouts.admin');
    }
}
