<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessMidtransWebhookJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $payload)
    {
        //
    }

    public function handle(): void
    {
        Log::info('Midtrans webhook validated and queued.', [
            'order_id' => $this->payload['order_id'] ?? null,
            'transaction_status' => $this->payload['transaction_status'] ?? null,
        ]);
    }
}
