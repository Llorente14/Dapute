<?php

namespace App\Actions\Catalog;

use Illuminate\Support\Collection;

class FetchActiveProductsAction
{
    public function __invoke(): Collection
    {
        return \App\Models\Product::active()->latest()->get();
    }
}
