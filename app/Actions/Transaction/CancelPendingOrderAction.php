<?php

namespace App\Actions\Transaction;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelPendingOrderAction
{
    public function execute(string $userId, string $orderId): array
    {
        try {
            return DB::transaction(function () use ($userId, $orderId): array {
                $order = DB::table('orders')
                    ->where('id', $orderId)
                    ->where('customer_id', $userId)
                    ->lockForUpdate()
                    ->first();

                if (!$order) {
                    return ['success' => false, 'message' => 'Order not found.'];
                }

                if (($order->order_status ?? null) !== OrderStatus::PENDING_PAYMENT->value) {
                    return ['success' => false, 'message' => 'Only pending payment orders can be cancelled.'];
                }

                DB::table('orders')
                    ->where('id', $orderId)
                    ->update([
                        'order_status' => OrderStatus::CANCELLED->value,
                        'updated_at' => now(),
                    ]);

                DB::table('payments')
                    ->where('order_id', $orderId)
                    ->where('payment_status', PaymentStatus::PENDING->value)
                    ->update([
                        'payment_status' => PaymentStatus::FAILED->value,
                    ]);

                Log::info('Pending order cancelled by customer.', [
                    'order_id' => $orderId,
                    'customer_id' => $userId,
                ]);

                return ['success' => true];
            });
        } catch (\Throwable $e) {
            Log::error('CancelPendingOrderAction failed: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'customer_id' => $userId,
            ]);

            return ['success' => false, 'message' => 'Failed to cancel order.'];
        }
    }
}
