<?php

namespace Tests\Feature;

use App\Jobs\ProcessMidtransWebhookJob;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class MidtransWebhookTest extends TestCase
{
    public function test_valid_midtrans_webhook_returns_ok_and_dispatches_job(): void
    {
        config(['services.midtrans.server_key' => 'midtrans-secret']);
        Bus::fake();

        $payload = [
            'order_id' => 'order-123',
            'status_code' => '200',
            'gross_amount' => '10000.00',
            'transaction_status' => 'settlement',
        ];
        $payload['signature_key'] = hash(
            'sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . 'midtrans-secret'
        );

        $this->postJson('/api/webhook/midtrans', $payload)
            ->assertOk()
            ->assertJson(['status' => 'ok']);

        Bus::assertDispatched(ProcessMidtransWebhookJob::class, fn (ProcessMidtransWebhookJob $job) => $job->payload === $payload);
    }

    public function test_invalid_midtrans_signature_returns_forbidden_and_dispatches_no_job(): void
    {
        config(['services.midtrans.server_key' => 'midtrans-secret']);
        Bus::fake();

        $this->postJson('/api/webhook/midtrans', [
            'order_id' => 'order-123',
            'status_code' => '200',
            'gross_amount' => '10000.00',
            'transaction_status' => 'settlement',
            'signature_key' => 'bad-signature',
        ])->assertForbidden();

        Bus::assertNotDispatched(ProcessMidtransWebhookJob::class);
    }
}
