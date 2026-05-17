<?php

namespace App\Actions\Logistics;

use App\Enums\OrderStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateOrderStatusAction
{
    public const STATUS_SHIPPED = OrderStatus::ON_DELIVERY->value;

    private const ALLOWED_ROLES = ['admin', 'karyawan'];

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
            OrderStatus::DELIVERED->value,
        ],
        OrderStatus::DELIVERED->value => [
            OrderStatus::COMPLETED->value,
        ],
    ];

    public function __invoke(string $orderId, string $nextStatus): array
    {
        $this->authorize();

        if (!OrderStatus::tryFrom($nextStatus)) {
            return ['success' => false, 'message' => 'Invalid order status.'];
        }

        $order = DB::table('orders')
            ->where('id', $orderId)
            ->select('id', 'order_status')
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

        DB::table('orders')
            ->where('id', $orderId)
            ->update([
                'order_status' => $nextStatus,
                'updated_at' => now(),
            ]);

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
        $role = auth()->user()?->role;

        if (!in_array($role, self::ALLOWED_ROLES, true)) {
            throw new AuthorizationException('Only admin or employee can update order status.');
        }
    }

    private function canTransition(string $currentStatus, string $nextStatus): bool
    {
        return in_array($nextStatus, self::VALID_TRANSITIONS[$currentStatus] ?? [], true);
    }
}
