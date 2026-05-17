<?php

namespace App\Actions\Logistics;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateOrderStatusAction
{
    public function __invoke(string $orderId, string $status): array
    {
        $updated = DB::table('orders')
            ->where('id', $orderId)
            ->update([
                'order_status' => $status,
                'updated_at' => now(),
            ]);

        if ($updated < 1) {
            Log::warning('Order status update skipped: order not found.', [
                'order_id' => $orderId,
                'status' => $status,
            ]);

            return ['success' => false, 'message' => 'Order not found.'];
        }

        Log::info('Order status updated from admin queue.', [
            'order_id' => $orderId,
            'status' => $status,
        ]);

        return ['success' => true];
    }
}
