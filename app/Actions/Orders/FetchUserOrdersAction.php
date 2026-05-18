<?php

namespace App\Actions\Orders;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FetchUserOrdersAction
{
    public function execute(string $userId, string $status = 'all'): array
    {
        try {
            $ordersQuery = DB::table('orders')
                ->where('customer_id', $userId)
                ->orderByDesc('created_at');

            if ($status !== 'all') {
                $ordersQuery->where('order_status', $status);
            }

            $orders = $ordersQuery->get();

            if ($orders->isEmpty()) {
                return [];
            }

            $itemsByOrder = DB::table('order_items')
                ->whereIn('order_id', $orders->pluck('id')->all())
                ->select('order_id', 'cake_name_snapshot', 'price_snapshot', 'quantity', 'subtotal')
                ->orderBy('cake_name_snapshot')
                ->get()
                ->groupBy('order_id');

            return $orders
                ->map(fn ($order): array => [
                    'id' => $order->id,
                    'order_date' => $order->order_date ?? $order->created_at,
                    'order_status' => $order->order_status,
                    'subtotal_amount' => (int) ($order->subtotal_amount ?? 0),
                    'shipping_fee' => (int) ($order->shipping_fee ?? 0),
                    'total_payment' => (int) ($order->total_payment ?? 0),
                    'notes' => $order->notes ?? null,
                    'items' => ($itemsByOrder[$order->id] ?? collect())
                        ->map(fn ($item): array => [
                            'cake_name_snapshot' => $item->cake_name_snapshot,
                            'price_snapshot' => (int) $item->price_snapshot,
                            'quantity' => (int) $item->quantity,
                            'subtotal' => (int) $item->subtotal,
                        ])
                        ->values()
                        ->toArray(),
                ])
                ->values()
                ->toArray();
        } catch (\Throwable $exception) {
            Log::error('FetchUserOrdersAction failed.', [
                'user_id' => $userId,
                'status' => $status,
                'message' => $exception->getMessage(),
            ]);

            return [];
        }
    }
}
