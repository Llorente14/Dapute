<?php

namespace App\Actions\Logistics;

use App\Enums\OrderStatus;
use App\Enums\ShippingStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class UpdateOrderStatusAction
{


    private const ALLOWED_ROLES = ['owner', 'staff'];

    private const VALID_TRANSITIONS = [
        OrderStatus::PENDING_PAYMENT->value => [
            OrderStatus::CANCELLED->value,
        ],
        OrderStatus::PAID_PROCESSING->value => [
            OrderStatus::PICKUP_REQUESTED->value,
            OrderStatus::CANCELLED->value,
        ],
        'IN_PROCESSING' => [
            OrderStatus::PICKUP_REQUESTED->value,
        ],
        OrderStatus::PICKUP_REQUESTED->value => [
            OrderStatus::ON_DELIVERY->value,
        ],
        OrderStatus::ON_DELIVERY->value => [
            OrderStatus::COMPLETED->value,
            OrderStatus::CANCELLED->value,
        ],
        OrderStatus::DELIVERED->value => [
            OrderStatus::COMPLETED->value,
            OrderStatus::CANCELLED->value,
        ],
    ];

    public function __invoke(string $orderId, string $nextStatus): array
    {
        $this->authorize();

        if (!OrderStatus::tryFrom($nextStatus)) {
            return ['success' => false, 'message' => 'Invalid order status.'];
        }

        $selectColumns = ['id', 'order_status'];

        if (Schema::hasColumn('orders', 'notes')) {
            $selectColumns[] = 'notes';
        }

        $order = DB::table('orders')
            ->where('id', $orderId)
            ->select($selectColumns)
            ->first();

        if (!$order) {
            Log::warning('Order status update skipped: order not found.', [
                'order_id' => $orderId,
                'requested_status' => $nextStatus,
            ]);

            return ['success' => false, 'message' => 'Order not found.'];
        }

        $currentStatus = (string) $order->order_status;

        if (!$this->canTransition($currentStatus, $nextStatus)) {
            Log::warning('Order status update rejected: invalid transition.', [
                'order_id' => $orderId,
                'current_status' => $currentStatus,
                'requested_status' => $nextStatus,
            ]);

            return ['success' => false, 'message' => 'Invalid status transition.'];
        }

        $updateData = [
            'order_status' => $nextStatus,
            'updated_at' => now(),
        ];

        if (Schema::hasColumn('orders', 'shipping_status')) {
            $shippingStatus = $this->shippingStatusForOrderStatus($nextStatus);

            if ($shippingStatus) {
                $updateData['shipping_status'] = $shippingStatus;
            }
        }

        if (
            $nextStatus === OrderStatus::CANCELLED->value
            && in_array($currentStatus, [OrderStatus::ON_DELIVERY->value, OrderStatus::DELIVERED->value], true)
            && Schema::hasColumn('orders', 'notes')
        ) {
            $updateData['notes'] = $this->appendNote($order->notes ?? null, 'NOTED: Driver gagal kirim');
        }

        DB::table('orders')
            ->where('id', $orderId)
            ->update($updateData);

        $this->syncShipment($orderId, $nextStatus, $updateData['notes'] ?? null);

        Log::info('Order status updated from admin queue.', [
            'order_id' => $orderId,
            'previous_status' => $currentStatus,
            'status' => $nextStatus,
        ]);

        return ['success' => true, 'message' => 'Status updated'];
    }

    public function execute(string $orderId, string $nextStatus): array
    {
        return $this($orderId, $nextStatus);
    }

    private function authorize(): void
    {
        $role = DB::table('users')->where('id', auth()->id())->value('role');

        if (!in_array($role, self::ALLOWED_ROLES, true)) {
            throw new AuthorizationException('Only owner or staff can update order status.');
        }
    }

    private function canTransition(string $currentStatus, string $nextStatus): bool
    {
        return in_array($nextStatus, self::VALID_TRANSITIONS[$currentStatus] ?? [], true);
    }

    private function shippingStatusForOrderStatus(string $orderStatus): ?string
    {
        return match ($orderStatus) {
            OrderStatus::PICKUP_REQUESTED->value => ShippingStatus::AWAITING_PICKUP->value,
            OrderStatus::ON_DELIVERY->value => ShippingStatus::ON_DELIVERY->value,
            OrderStatus::DELIVERED->value, OrderStatus::COMPLETED->value => ShippingStatus::DELIVERED->value,
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

    private function syncShipment(string $orderId, string $orderStatus, ?string $notes): void
    {
        if (!Schema::hasTable('shipments')) {
            return;
        }

        $updateData = ['updated_at' => now()];
        $shippingStatus = $this->shippingStatusForOrderStatus($orderStatus);

        if ($shippingStatus) {
            $updateData['shipping_status'] = $shippingStatus;
        }

        if ($orderStatus === OrderStatus::COMPLETED->value) {
            $updateData['delivered_at'] = now();
        }

        if ($notes !== null) {
            $updateData['notes'] = $notes;
        }

        DB::table('shipments')
            ->where('order_id', $orderId)
            ->update($updateData);
    }
}
