<?php

namespace App\Actions\Logistics;

use App\Enums\OrderStatus;
use App\Enums\ShippingStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ProcessBiteshipWebhookAction
{
    public function __invoke(array $payload): array
    {
        $orderIdentifier = $this->orderIdentifier($payload);
        $shipmentStatus = $this->shipmentStatus($payload);

        if (!$orderIdentifier || !$shipmentStatus) {
            Log::warning('Biteship webhook ignored: missing order identifier or status.', $payload);

            return ['success' => false, 'message' => 'Missing order identifier or status.'];
        }

        return DB::transaction(function () use ($orderIdentifier, $shipmentStatus, $payload): array {
            $order = $this->findOrder($orderIdentifier);

            if (!$order) {
                Log::warning('Biteship webhook ignored: order not found.', [
                    'identifier' => $orderIdentifier,
                    'shipment_status' => $shipmentStatus,
                ]);

                return ['success' => false, 'message' => 'Order not found.'];
            }

            if (in_array($order->order_status, [OrderStatus::COMPLETED->value, OrderStatus::CANCELLED->value], true)) {
                return ['success' => true, 'message' => 'Order already final.', 'idempotent' => true];
            }

            $updateData = ['updated_at' => now()];
            $normalizedStatus = $this->normalizeStatus($shipmentStatus);

            if ($normalizedStatus === 'failed') {
                $updateData['order_status'] = OrderStatus::CANCELLED->value;
                if (Schema::hasColumn('orders', 'notes')) {
                    $updateData['notes'] = $this->appendNote($order->notes ?? null, 'NOTED: Driver gagal kirim');
                }
            } elseif ($normalizedStatus === 'delivered') {
                $updateData['order_status'] = OrderStatus::COMPLETED->value;
            } elseif ($normalizedStatus === 'moving') {
                $updateData['order_status'] = OrderStatus::ON_DELIVERY->value;
            }

            if (Schema::hasColumn('orders', 'shipping_status')) {
                $shippingStatus = match ($normalizedStatus) {
                    'delivered' => ShippingStatus::DELIVERED->value,
                    'moving' => ShippingStatus::ON_DELIVERY->value,
                    default => null,
                };

                if ($shippingStatus) {
                    $updateData['shipping_status'] = $shippingStatus;
                }
            }

            if (!array_key_exists('order_status', $updateData)) {
                Log::info('Biteship webhook ignored: status does not move order.', [
                    'order_id' => $order->id,
                    'shipment_status' => $shipmentStatus,
                ]);

                return ['success' => true, 'message' => 'Status ignored.'];
            }

            DB::table('orders')
                ->where('id', $order->id)
                ->update($updateData);

            Log::info('Biteship webhook processed.', [
                'order_id' => $order->id,
                'shipment_status' => $shipmentStatus,
                'order_status' => $updateData['order_status'],
            ]);

            return ['success' => true, 'order_status' => $updateData['order_status']];
        });
    }

    private function findOrder(string $identifier): ?object
    {
        $selectColumns = ['id', 'order_status'];

        $identifierColumns = ['id'];

        if (Schema::hasColumn('orders', 'biteship_order_id')) {
            $selectColumns[] = 'biteship_order_id';
            $identifierColumns[] = 'biteship_order_id';
        }

        if (Schema::hasColumn('orders', 'tracking_id')) {
            $selectColumns[] = 'tracking_id';
            $identifierColumns[] = 'tracking_id';
        }

        if (Schema::hasColumn('orders', 'notes')) {
            $selectColumns[] = 'notes';
        }

        $query = DB::table('orders');

        $query->where(function ($query) use ($identifierColumns, $identifier): void {
            foreach ($identifierColumns as $index => $column) {
                $index === 0
                    ? $query->where($column, $identifier)
                    : $query->orWhere($column, $identifier);
            }
        });

        return $query
            ->lockForUpdate()
            ->select($selectColumns)
            ->first();
    }

    private function orderIdentifier(array $payload): ?string
    {
        $identifier = data_get($payload, 'order_id')
            ?? data_get($payload, 'id')
            ?? data_get($payload, 'order.id')
            ?? data_get($payload, 'courier.waybill_id')
            ?? data_get($payload, 'waybill_id')
            ?? data_get($payload, 'tracking_id');

        return $identifier ? (string) $identifier : null;
    }

    private function shipmentStatus(array $payload): ?string
    {
        $status = data_get($payload, 'status')
            ?? data_get($payload, 'event')
            ?? data_get($payload, 'order_status')
            ?? data_get($payload, 'courier.status')
            ?? data_get($payload, 'shipment.status');

        return $status ? strtolower((string) $status) : null;
    }

    private function normalizeStatus(string $status): ?string
    {
        return match (true) {
            str_contains($status, 'cancel'),
            str_contains($status, 'fail'),
            str_contains($status, 'reject'),
            str_contains($status, 'return') => 'failed',

            str_contains($status, 'deliver') && !str_contains($status, 'delivery') => 'delivered',
            str_contains($status, 'completed') => 'delivered',

            str_contains($status, 'pickup'),
            str_contains($status, 'picked'),
            str_contains($status, 'delivery'),
            str_contains($status, 'transit'),
            str_contains($status, 'drop') => 'moving',

            default => null,
        };
    }

    private function appendNote(?string $notes, string $note): string
    {
        $notes = trim((string) $notes);

        if (str_contains($notes, $note)) {
            return $notes;
        }

        return $notes === '' ? $note : "{$notes}\n{$note}";
    }
}
