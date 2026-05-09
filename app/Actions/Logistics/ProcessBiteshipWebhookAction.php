<?php

namespace App\Actions\Logistics;

class ProcessBiteshipWebhookAction
{
    public function __invoke(array $payload): void
    {
        // TODO: verify, update shipment status from Biteship event
    }
}
