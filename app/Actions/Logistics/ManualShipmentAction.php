<?php

namespace App\Actions\Logistics;

use App\Enums\OrderStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ManualShipmentAction
{
    public function __construct(private UpdateOrderStatusAction $updateOrderStatusAction)
    {
    }

    public function __invoke(string $orderId, string $trackingNumber): array
    {
        $this->authorize();

        $trackingNumber = Str::limit(trim($trackingNumber), 120, '');

        if ($trackingNumber === '') {
            return ['success' => false, 'message' => 'Tracking number is required.'];
        }

        $order = DB::table('orders')
            ->where('id', $orderId)
            ->select('id', 'order_status', 'biteship_order_id', 'tracking_id')
            ->first();

        if (!$order) {
            return ['success' => false, 'message' => 'Order not found.'];
        }

        if (!empty($order->biteship_order_id)) {
            return ['success' => false, 'message' => 'This order already has a Biteship pickup.'];
        }

        if (
            $order->order_status === OrderStatus::ON_DELIVERY->value
            && (string) $order->tracking_id === $trackingNumber
        ) {
            return [
                'success' => true,
                'message' => 'Manual shipment already saved.',
                'tracking_id' => $trackingNumber,
                'idempotent' => true,
            ];
        }

        if ($order->order_status !== OrderStatus::PICKUP_REQUESTED->value) {
            return ['success' => false, 'message' => 'Manual shipment only works for pickup requested orders.'];
        }

        DB::beginTransaction();

        try {
            DB::table('orders')
                ->where('id', $orderId)
                ->update([
                    'tracking_id' => $trackingNumber,
                    'updated_at' => now(),
                ]);

            $statusResult = ($this->updateOrderStatusAction)(
                $orderId,
                OrderStatus::ON_DELIVERY->value
            );

            if (!$statusResult['success']) {
                DB::rollBack();
                return $statusResult;
            }

            DB::commit();

            Log::info('Manual shipment created from admin queue.', [
                'order_id' => $orderId,
                'tracking_id' => $trackingNumber,
            ]);

            return [
                'success' => true,
                'message' => 'Manual shipment saved.',
                'tracking_id' => $trackingNumber,
            ];
        } catch (\Throwable $exception) {
            DB::rollBack();

            Log::error('Manual shipment failed.', [
                'order_id' => $orderId,
                'message' => $exception->getMessage(),
            ]);

            return ['success' => false, 'message' => 'Failed to save manual shipment.'];
        }
    }

    public function execute(string $orderId, string $trackingNumber): array
    {
        return $this($orderId, $trackingNumber);
    }

    private function authorize(): void
    {
        $role = DB::table('users')->where('id', auth()->id())->value('role');

        if (!in_array($role, ['owner', 'staff'], true)) {
            throw new AuthorizationException('Only owner or staff can create manual shipment.');
        }
    }
}
