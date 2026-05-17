<?php

namespace App\Actions\Transaction;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessMidtransWebhookAction
{
    public function __invoke(array $payload): void
    {
        $this->execute($payload);
    }

    public function execute(array $payload): void
    {
        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;

        if (!$orderId || !$transactionStatus) {
            Log::warning('Midtrans webhook ignored: missing order_id or transaction_status.', $payload);
            return;
        }

        $mappedStatus = $this->mapStatus($transactionStatus);

        if (!$mappedStatus) {
            Log::info('Midtrans webhook ignored: unmapped transaction status.', $payload);
            return;
        }

        [$paymentStatus, $orderStatus] = $mappedStatus;

        DB::transaction(function () use ($orderId, $paymentStatus, $orderStatus, $payload, $transactionStatus): void {
            $payment = DB::table('payments')
                ->where('order_id', $orderId)
                ->lockForUpdate()
                ->first();

            if (!$payment) {
                Log::warning('Midtrans webhook ignored: payment row not found.', [
                    'order_id' => $orderId,
                    'transaction_status' => $transactionStatus,
                ]);

                return;
            }

            if (($payment->payment_status ?? null) === PaymentStatus::PAID->value) {
                Log::info('Midtrans webhook skipped: payment already paid.', [
                    'order_id' => $orderId,
                    'transaction_status' => $transactionStatus,
                ]);

                return;
            }

            $now = now();
            $paymentData = [
                'payment_status' => $paymentStatus->value,
                'transaction_id' => $payload['transaction_id'] ?? $payment->transaction_id ?? null,
            ];

            if ($paymentStatus === PaymentStatus::PAID) {
                $paymentData['paid_at'] = $now;
            }

            if ($paymentStatus === PaymentStatus::EXPIRED) {
                $paymentData['expired_at'] = $now;
            }

            DB::table('payments')
                ->where('id', $payment->id)
                ->update($paymentData);

            DB::table('orders')
                ->where('id', $orderId)
                ->update([
                    'order_status' => $orderStatus->value,
                    'updated_at' => $now,
                ]);

            Log::info('Midtrans webhook processed.', [
                'order_id' => $orderId,
                'transaction_status' => $transactionStatus,
                'payment_status' => $paymentStatus->value,
                'order_status' => $orderStatus->value,
            ]);
        });
    }

    /**
     * @return array{PaymentStatus, OrderStatus}|null
     */
    private function mapStatus(string $transactionStatus): ?array
    {
        return match ($transactionStatus) {
            'settlement', 'capture' => [PaymentStatus::PAID, OrderStatus::PAID_PROCESSING],
            'expire' => [PaymentStatus::EXPIRED, OrderStatus::CANCELLED],
            'cancel', 'deny', 'failure' => [PaymentStatus::FAILED, OrderStatus::CANCELLED],
            'pending' => [PaymentStatus::PENDING, OrderStatus::PENDING_PAYMENT],
            default => null,
        };
    }
}
