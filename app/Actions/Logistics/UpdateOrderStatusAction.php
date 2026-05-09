<?php

namespace App\Actions\Logistics;

class UpdateOrderStatusAction
{
    public function __invoke(int $orderId, string $status): void
    {
        // TODO: update orders.status, fire OrderStatusUpdated event
    }
}
