<?php

namespace App\Actions\Catalog;

class StoreProductAction
{
    /**
     * Create or update a product.
     *
     * @param  array{name: string, description: string, price: int, image: string|null}  $data
     */
    public function __invoke(array $data): void
    {
        // TODO: validate $data, store image to storage, persist to DB
    }
}
