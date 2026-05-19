<?php

namespace App\Actions\Transaction;

use App\Enums\OrderStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConfirmOrderReceivedAction
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

                // Customer handshake closes courier delivery after package is received.
                $status = $order->order_status ?? null;
                $shippedStatuses = [OrderStatus::ON_DELIVERY->value, OrderStatus::DELIVERED->value, 'SHIPPED'];
                
                if (!in_array($status, $shippedStatuses, true)) {
                    return ['success' => false, 'message' => 'Only shipped orders can be confirmed as received.'];
                }

                DB::table('orders')
                    ->where('id', $orderId)
                    ->update([
                        'order_status' => OrderStatus::COMPLETED->value, // Using COMPLETED for "done"
                        'updated_at' => now(),
                    ]);

                Log::info('Order confirmed as received by customer.', [
                    'order_id' => $orderId,
                    'customer_id' => $userId,
                ]);

                return ['success' => true];
            });
        } catch (\Throwable $e) {
            Log::error('ConfirmOrderReceivedAction failed: ' . $e->getMessage(), [
                'order_id' => $orderId,
                'customer_id' => $userId,
            ]);

            return ['success' => false, 'message' => 'Failed to confirm order.'];
        }
    }
}
