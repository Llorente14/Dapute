<?php

namespace App\Livewire\Admin;

use App\Actions\Logistics\UpdateOrderStatusAction;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OrderQueue extends Component
{
    public string $statusFilter = 'ALL';
    public ?string $expandedOrderId = null;
    public array $orders = [];
    public array $orderItems = [];

    private const ACTIVE_STATUSES = [
        'PENDING_PAYMENT',
        'IN_PROCESSING',
        'PAID_PROCESSING',
        'PICKUP_REQUESTED',
        'ON_DELIVERY',
    ];

    public function mount(): void
    {
        $this->loadOrders();
    }

    public function filterBy(string $status): void
    {
        $this->statusFilter = $status;
        $this->expandedOrderId = null;
        $this->loadOrders();
    }

    public function toggleDetails(string $orderId): void
    {
        if ($this->expandedOrderId === $orderId) {
            $this->expandedOrderId = null;
            return;
        }

        $this->expandedOrderId = $orderId;
        $this->loadOrderItems($orderId);
    }

    public function cancelPending(string $orderId, UpdateOrderStatusAction $action): void
    {
        $this->transitionOrder($orderId, 'CANCELLED', $action);
    }

    public function markReadyToShip(string $orderId, UpdateOrderStatusAction $action): void
    {
        $this->transitionOrder($orderId, 'PICKUP_REQUESTED', $action);
    }

    public function requestPickup(string $orderId): void
    {
        $this->dispatch('show-toast', title: 'Pickup Pending', subtitle: 'Biteship pickup flow is handled in SCRUM-51.', type: 'cart');
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'PAID_PROCESSING' => 'Paid Processing',
            'IN_PROCESSING' => 'Paid Processing',
            default => str($status)->replace('_', ' ')->title()->toString(),
        };
    }

    public function statusBadgeClass(string $status): string
    {
        return match ($status) {
            'PENDING_PAYMENT' => 'bg-[#D4EF70] text-[#012d1d]',
            'IN_PROCESSING', 'PAID_PROCESSING' => 'bg-[#d7e3ff] text-[#001b3f]',
            'PICKUP_REQUESTED' => 'bg-[#eadcff] text-[#2a0054]',
            'ON_DELIVERY' => 'bg-[#c1ecd4] text-[#012d1d]',
            default => 'bg-white text-[#012d1d]',
        };
    }

    public function getFilterTabsProperty(): array
    {
        return [
            'ALL' => 'Semua',
            'PENDING_PAYMENT' => 'Pending',
            'PAID_PROCESSING' => 'Paid Processing',
            'PICKUP_REQUESTED' => 'Pickup Requested',
            'ON_DELIVERY' => 'On Delivery',
        ];
    }

    public function actionOptions(string $status): array
    {
        $options = [
            [
                'label' => 'Batalkan',
                'method' => 'cancelPending',
                'icon' => 'cancel',
                'available' => $status === 'PENDING_PAYMENT',
            ],
            [
                'label' => 'Siap Dikirim',
                'method' => 'markReadyToShip',
                'icon' => 'outbox',
                'available' => in_array($status, ['IN_PROCESSING', 'PAID_PROCESSING'], true),
            ],
            [
                'label' => 'Request Pickup',
                'method' => 'requestPickup',
                'icon' => 'local_shipping',
                'available' => $status === 'PICKUP_REQUESTED',
            ],
        ];

        return $options;
    }

    public function itemsForOrder(string $orderId): array
    {
        return $this->orderItems[$orderId] ?? [];
    }

    private function transitionOrder(string $orderId, string $status, UpdateOrderStatusAction $action): void
    {
        $result = $action($orderId, $status);

        if (!$result['success']) {
            $this->dispatch('show-toast', title: 'Update Failed', subtitle: $result['message'] ?? 'Order status failed to update.', type: 'cart');
            return;
        }

        $this->expandedOrderId = null;
        $this->loadOrders();
        $this->dispatch('show-toast', title: 'Order Updated', subtitle: 'Order queue refreshed.', type: 'success');
    }

    private function loadOrders(): void
    {
        $statuses = $this->statusFilter === 'ALL'
            ? self::ACTIVE_STATUSES
            : $this->statusesForFilter($this->statusFilter);

        $rows = DB::table('orders')
            ->leftJoin('users', 'orders.customer_id', '=', 'users.id')
            ->whereIn('orders.order_status', $statuses)
            ->select(
                'orders.id',
                'orders.customer_id',
                'orders.total_payment',
                'orders.order_status',
                'orders.order_date',
                'orders.created_at',
                'users.full_name as customer_name',
                'users.email as customer_email'
            )
            ->orderByDesc('orders.order_date')
            ->orderByDesc('orders.created_at')
            ->get();

        $this->orders = $rows
            ->map(fn ($order) => [
                'id' => $order->id,
                'short_id' => strtoupper(substr((string) $order->id, 0, 8)),
                'customer_name' => $order->customer_name ?: 'Unknown Customer',
                'customer_email' => $order->customer_email,
                'total_payment' => (int) $order->total_payment,
                'order_status' => $order->order_status,
                'order_date' => $order->order_date ?? $order->created_at,
            ])
            ->values()
            ->toArray();
    }

    private function loadOrderItems(string $orderId): void
    {
        if (array_key_exists($orderId, $this->orderItems)) {
            return;
        }

        $this->orderItems[$orderId] = DB::table('order_items')
            ->where('order_id', $orderId)
            ->select('cake_name_snapshot', 'quantity', 'subtotal')
            ->orderBy('cake_name_snapshot')
            ->get()
            ->map(fn ($item) => [
                'cake_name_snapshot' => $item->cake_name_snapshot,
                'quantity' => (int) $item->quantity,
                'subtotal' => (int) $item->subtotal,
            ])
            ->values()
            ->toArray();
    }

    private function statusesForFilter(string $status): array
    {
        return match ($status) {
            'PAID_PROCESSING', 'IN_PROCESSING' => ['PAID_PROCESSING', 'IN_PROCESSING'],
            default => [$status],
        };
    }

    public function render()
    {
        return view('livewire.admin.order-queue')
            ->layout('layouts.admin');
    }
}
