<?php

namespace App\Actions\Catalog;

use Illuminate\Support\Collection;

class FetchActiveProductsAction
{
    public function __invoke(): Collection
    {
        // TODO: return active products from DB
        return collect();
    }
}
