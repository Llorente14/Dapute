<?php

namespace App\Jobs;

use App\Actions\Transaction\ProcessMidtransWebhookAction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessMidtransWebhookJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public array $payload)
    {
        //
    }

    public function handle(ProcessMidtransWebhookAction $action): void
    {
        $action->execute($this->payload);
    }
}
