<?php

namespace App\Actions\Transaction;

class ProcessMidtransWebhookAction
{
    public function __invoke(array $payload): void
    {
        // TODO: verify signature, update order payment_status
    }
}
