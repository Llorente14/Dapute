<?php

namespace App\Actions\Transaction;

use Illuminate\Support\Facades\DB;

class FetchOrderDetailAction
{
    public function execute(string $userId, string $orderId): array
    {
        $order = DB::table('orders')
            ->where('id', $orderId)
            ->where('customer_id', $userId)
            ->first();

        if (!$order) {
            return ['success' => false, 'message' => 'Order not found.'];
        }

        $items = DB::table('order_items')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->where('order_items.order_id', $orderId)
            ->select(
                'order_items.id',
                'order_items.product_id',
                'order_items.cake_name_snapshot',
                'order_items.price_snapshot',
                'order_items.quantity',
                'order_items.subtotal',
                'products.image_url'
            )
            ->orderBy('order_items.cake_name_snapshot')
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'product_id' => $item->product_id,
                'cake_name_snapshot' => $item->cake_name_snapshot,
                'price_snapshot' => (int) $item->price_snapshot,
                'quantity' => (int) $item->quantity,
                'subtotal' => (int) $item->subtotal,
                'image_url' => $item->image_url,
            ])
            ->toArray();

        $address = DB::table('order_addresses')
            ->where('order_id', $orderId)
            ->first();

        return [
            'success' => true,
            'order' => (array) $order,
            'items' => $items,
            'address' => $address ? (array) $address : null,
        ];
    }
}
