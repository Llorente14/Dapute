<?php

namespace App\Actions\Logistics;

use App\Enums\OrderStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Exception;
use InvalidArgumentException;

class UpdateOrderStatusAction
{
    // Konstanta input
    public const STATUS_PENDING_PAYMENT = 'pending_payment';
    public const STATUS_PAID            = 'paid';
    public const STATUS_PROCESSING      = 'processing';
    public const STATUS_SHIPPED         = 'shipped';
    public const STATUS_DONE            = 'done';
    public const STATUS_CANCELLED       = 'cancelled';

    // State Machine
    private const VALID_TRANSITIONS = [
        self::STATUS_PENDING_PAYMENT => [self::STATUS_PAID, self::STATUS_CANCELLED],
        self::STATUS_PAID            => [self::STATUS_PROCESSING, self::STATUS_CANCELLED],
        self::STATUS_PROCESSING      => [self::STATUS_SHIPPED],
        self::STATUS_SHIPPED         => [self::STATUS_DONE],
        self::STATUS_DONE            => [], 
        self::STATUS_CANCELLED       => [], 
    ];

    private const DB_MAPPING = [
        self::STATUS_PENDING_PAYMENT => 'PENDING_PAYMENT',
        self::STATUS_PAID            => 'PAID_PROCESSING', 
        self::STATUS_PROCESSING      => 'PICKUP_REQUESTED',
        self::STATUS_SHIPPED         => 'ON_DELIVERY',
        self::STATUS_DONE            => 'COMPLETED',
        self::STATUS_CANCELLED       => 'CANCELLED',
    ];

    public function execute(string $orderId, string $newStatus): array
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role, ['admin', 'karyawan'], true)) {
            throw new AuthorizationException('Only admin or employee can update order status.');
        }

        if (!OrderStatus::tryFrom($status)) {
            return ['success' => false, 'message' => 'Invalid order status.'];
        }

        $order = DB::table('orders')
            ->where('id', $orderId)
            ->select('id', 'order_status')
            ->first();

        if (!$order) {
            Log::warning('Order status update skipped: order not found.', [
                'order_id' => $orderId,
                'status' => $status,
            ]);

            return ['success' => false, 'message' => 'Order not found.'];
        }

        if (!$this->canTransition((string) $order->order_status, $status)) {
            Log::warning('Order status update rejected: invalid transition.', [
                'order_id' => $orderId,
                'current_status' => $order->order_status,
                'requested_status' => $status,
            ]);

            return ['success' => false, 'message' => 'Invalid status transition.'];
        }

        $updated = DB::table('orders')
            ->where('id', $orderId)
            ->update([
                'order_status' => $status,
                'updated_at' => now(),
            ]);

        Log::info('Order status updated from admin queue.', [
            'order_id' => $orderId,
            'previous_status' => $order->order_status,
            'status' => $status,
        ]);

        return [
            'success' => true, 
            'message' => "Status pesanan berhasil diperbarui menjadi {$newStatus}."
        ];
    }

    private function canTransition(string $currentStatus, string $nextStatus): bool
    {
        $transitions = [
            OrderStatus::PENDING_PAYMENT->value => [
                OrderStatus::CANCELLED->value,
            ],
            'IN_PROCESSING' => [
                OrderStatus::PICKUP_REQUESTED->value,
                OrderStatus::CANCELLED->value,
            ],
            OrderStatus::PAID_PROCESSING->value => [
                OrderStatus::PICKUP_REQUESTED->value,
                OrderStatus::CANCELLED->value,
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

        return in_array($nextStatus, $transitions[$currentStatus] ?? [], true);
    }
}
