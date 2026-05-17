<?php

namespace App\Actions\Transaction;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessMidtransWebhookAction
{
    public function __invoke(array $payload): void
    {
        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (!$orderId || !$transactionStatus) {
            Log::warning('Midtrans webhook ignored: missing order_id or transaction_status.', $payload);
            return;
        }

        $orderStatus = match ($transactionStatus) {
            'settlement' => 'PAID',
            'capture' => $fraudStatus === 'challenge' ? 'PENDING_PAYMENT' : 'PAID',
            'pending' => 'PENDING_PAYMENT',
            'expire' => 'EXPIRED',
            'cancel', 'deny', 'failure' => 'FAILED',
            default => null,
        };

        if (!$orderStatus) {
            Log::info('Midtrans webhook ignored: unmapped transaction status.', $payload);
            return;
        }

        DB::table('orders')
            ->where('id', $orderId)
            ->update([
                'order_status' => $orderStatus,
                'updated_at' => now(),
            ]);
    }
}
