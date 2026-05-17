<?php

namespace App\Actions\Transaction;

use Illuminate\Support\Facades\Log;

class ValidateMidtransWebhookSignatureAction
{
    public function execute(array $payload): bool
    {
        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $serverKey = config('services.midtrans.server_key');

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey || !$serverKey) {
            Log::warning('Midtrans webhook rejected: missing signature validation data.', [
                'order_id' => $orderId,
                'has_status_code' => filled($statusCode),
                'has_gross_amount' => filled($grossAmount),
                'has_signature_key' => filled($signatureKey),
                'has_server_key' => filled($serverKey),
            ]);

            return false;
        }

        $expectedSignature = hash('sha512', (string) $orderId . (string) $statusCode . (string) $grossAmount . $serverKey);

        if (!hash_equals($expectedSignature, (string) $signatureKey)) {
            Log::warning('Midtrans webhook rejected: invalid signature key.', [
                'order_id' => $orderId,
                'status_code' => $statusCode,
            ]);

            return false;
        }

        return true;
    }
}
