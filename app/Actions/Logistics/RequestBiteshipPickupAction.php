<?php

namespace App\Actions\Logistics;

class RequestBiteshipPickupAction
{
    public function __invoke(int $orderId): string
    {
        // TODO: call Biteship create-order API, return tracking_id
        return '';
    }
}
