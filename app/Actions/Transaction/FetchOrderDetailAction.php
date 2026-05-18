<?php

namespace App\Actions\Transaction;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        $trackings = $this->fetchTrackingEvents($orderId);

        return [
            'success' => true,
            'order' => (array) $order,
            'items' => $items,
            'address' => $address ? (array) $address : null,
            'trackings' => $trackings,
        ];
    }

    private function fetchTrackingEvents(string $orderId): array
    {
        if (!Schema::hasTable('order_trackings')) {
            return [];
        }

        $timestampColumn = $this->resolveTrackingTimestampColumn();
        $query = DB::table('order_trackings')->where('order_id', $orderId);

        if ($timestampColumn) {
            $query->orderByDesc($timestampColumn);
        }

        return $query
            ->get()
            ->map(fn ($tracking) => $this->normalizeTrackingEvent((array) $tracking))
            ->toArray();
    }

    private function normalizeTrackingEvent(array $tracking): array
    {
        $status = $tracking['status']
            ?? $tracking['tracking_status']
            ?? $tracking['shipment_status']
            ?? $tracking['order_status']
            ?? 'TRACKING_UPDATE';

        return [
            'id' => $tracking['id'] ?? null,
            'status' => (string) $status,
            'label' => str((string) $status)->replace('_', ' ')->title()->toString(),
            'description' => $tracking['description']
                ?? $tracking['message']
                ?? $tracking['note']
                ?? $tracking['checkpoint']
                ?? 'Shipment status updated.',
            'timestamp' => $tracking['event_at']
                ?? $tracking['tracked_at']
                ?? $tracking['timestamp']
                ?? $tracking['created_at']
                ?? $tracking['updated_at']
                ?? null,
        ];
    }

    private function resolveTrackingTimestampColumn(): ?string
    {
        $columns = Schema::getColumnListing('order_trackings');

        foreach (['event_at', 'tracked_at', 'timestamp', 'created_at', 'updated_at'] as $column) {
            if (in_array($column, $columns, true)) {
                return $column;
            }
        }

        return null;
    }
}
