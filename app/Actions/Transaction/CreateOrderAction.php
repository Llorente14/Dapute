<?php

namespace App\Actions\Transaction;

class CreateOrderAction
{
    public function __invoke(int $userId, array $shippingData): string
    {
        // TODO: build order from cart, call Midtrans Snap API, return snap_token
        return '';
    }
}
