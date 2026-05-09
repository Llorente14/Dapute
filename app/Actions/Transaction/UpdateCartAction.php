<?php

namespace App\Actions\Transaction;

class UpdateCartAction
{
    public function __invoke(int $userId, int $productId, int $qty): void
    {
        // TODO: upsert cart item; qty=0 → remove
    }
}
