<?php

namespace App\Actions\Logistics;

class ManualShipmentAction
{
    public function __invoke(int $orderId, string $courier, string $trackingNumber): void
    {
        // TODO: store manual shipment info when not using Biteship
    }
}
