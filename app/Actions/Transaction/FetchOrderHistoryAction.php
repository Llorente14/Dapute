<?php

namespace App\Actions\Transaction;

use Illuminate\Support\Facades\DB;

class FetchOrderHistoryAction
{
    public function execute(string $userId, int $limit = 12): array
    {
        $orders = DB::table('orders')
            ->where('customer_id', $userId)
            ->orderByDesc('order_date')
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();

        return $orders
            ->map(function ($order): array {
                $items = DB::table('order_items')
                    ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
                    ->where('order_items.order_id', $order->id)
                    ->select(
                        'order_items.cake_name_snapshot',
                        'order_items.quantity',
                        'order_items.subtotal',
                        'products.image_url'
                    )
                    ->orderBy('order_items.cake_name_snapshot')
                    ->get();

                $payment = DB::table('payments')
                    ->where('order_id', $order->id)
                    ->first();

                $address = DB::table('order_addresses')
                    ->where('order_id', $order->id)
                    ->first();

                $firstItem = $items->first();

                return [
                    'id' => $order->id,
                    'short_id' => strtoupper(substr((string) $order->id, 0, 8)),
                    'order_date' => $order->order_date ?? $order->created_at,
                    'order_status' => $order->order_status,
                    'payment_status' => $payment->payment_status ?? 'PENDING',
                    'total_payment' => (int) $order->total_payment,
                    'shipping_fee' => (int) ($order->shipping_fee ?? 0),
                    'tracking_id' => $order->tracking_id ?? null,
                    'biteship_order_id' => $order->biteship_order_id ?? null,
                    'city' => $address->city ?? null,
                    'first_item_name' => $firstItem->cake_name_snapshot ?? 'Order items',
                    'first_item_image' => $firstItem->image_url ?? null,
                    'item_count' => (int) $items->sum('quantity'),
                    'item_rows' => $items->count(),
                ];
            })
            ->values()
            ->toArray();
    }
}
