<?php

namespace App\Livewire\Admin;

use App\Actions\Logistics\UpdateOrderStatusAction;
use App\Enums\OrderStatus;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class OrderQueue extends Component
{
    public string $statusFilter = 'ALL';
    public ?string $expandedOrderId = null;
    public array $orders = [];
    public array $orderItems = [];
    public int $page = 1;
    public int $perPage = 10;
    public int $totalOrders = 0;

    private const ACTIVE_STATUSES = [
        'PENDING_PAYMENT',
        'IN_PROCESSING',
        'PAID_PROCESSING',
        'PICKUP_REQUESTED',
        'ON_DELIVERY',
        'DELIVERED',
    ];

    public function mount(): void
    {
        $this->loadOrders();
    }

    public function filterBy(string $status): void
    {
        $this->statusFilter = $status;
        $this->expandedOrderId = null;
        $this->page = 1;
        $this->loadOrders();
    }

    public function setPerPage(int $perPage): void
    {
        $perPage = in_array($perPage, [5, 10], true) ? $perPage : 10;

        if ($this->perPage === $perPage) {
            return;
        }

        $this->perPage = $perPage;
        $this->page = 1;
        $this->expandedOrderId = null;
        $this->loadOrders();
    }

    public function nextPage(): void
    {
        if ($this->page >= $this->totalPages()) {
            return;
        }

        $this->page++;
        $this->expandedOrderId = null;
        $this->loadOrders();
    }

    public function previousPage(): void
    {
        if ($this->page <= 1) {
            return;
        }

        $this->page--;
        $this->expandedOrderId = null;
        $this->loadOrders();
    }

    public function goToPage(int $page): void
    {
        $this->page = max(1, min($page, $this->totalPages()));
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

    public function updateStatus(string $orderId, string $nextStatus, UpdateOrderStatusAction $action): void
    {
        $this->transitionOrder($orderId, $nextStatus, $action);
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
            'DELIVERED' => 'bg-[#012d1d] text-white',
            'COMPLETED' => 'bg-[#012d1d] text-white',
            'CANCELLED' => 'bg-[#ffd7d7] text-[#630000]',
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
            'DELIVERED' => 'Delivered',
        ];
    }

    public function actionOptions(string $status): array
    {
        return [
            [
                'label' => 'Batalkan',
                'status' => OrderStatus::CANCELLED->value,
                'icon' => 'cancel',
                'available' => in_array($status, [
                    OrderStatus::PENDING_PAYMENT->value,
                    'IN_PROCESSING',
                    OrderStatus::PAID_PROCESSING->value,
                ], true),
            ],
            [
                'label' => 'Siap Dikirim',
                'status' => OrderStatus::PICKUP_REQUESTED->value,
                'icon' => 'outbox',
                'available' => in_array($status, ['IN_PROCESSING', OrderStatus::PAID_PROCESSING->value], true),
            ],
            [
                'label' => 'Request Pickup',
                'status' => OrderStatus::ON_DELIVERY->value,
                'icon' => 'local_shipping',
                'available' => $status === OrderStatus::PICKUP_REQUESTED->value,
            ],
            [
                'label' => 'Mark Delivered',
                'status' => OrderStatus::DELIVERED->value,
                'icon' => 'inventory',
                'available' => $status === OrderStatus::ON_DELIVERY->value,
            ],
            [
                'label' => 'Complete Order',
                'status' => OrderStatus::COMPLETED->value,
                'icon' => 'task_alt',
                'available' => $status === OrderStatus::DELIVERED->value,
            ],
        ];
    }

    public function itemsForOrder(string $orderId): array
    {
        return $this->orderItems[$orderId] ?? [];
    }

    public function totalPages(): int
    {
        return max(1, (int) ceil($this->totalOrders / $this->perPage));
    }

    public function pageStart(): int
    {
        return $this->totalOrders === 0 ? 0 : (($this->page - 1) * $this->perPage) + 1;
    }

    public function pageEnd(): int
    {
        return min($this->page * $this->perPage, $this->totalOrders);
    }

    public function paginationPages(): array
    {
        $totalPages = $this->totalPages();
        $start = max(1, $this->page - 2);
        $end = min($totalPages, $this->page + 2);

        return range($start, $end);
    }

    private function transitionOrder(string $orderId, string $status, UpdateOrderStatusAction $action): void
    {
        try {
            $result = $action($orderId, $status);
        } catch (AuthorizationException $exception) {
            $this->dispatch('show-toast', title: 'Update Failed', subtitle: $exception->getMessage(), type: 'cart');
            return;
        }

        if (!$result['success']) {
            $this->dispatch('show-toast', title: 'Update Failed', subtitle: $result['message'] ?? 'Order status failed to update.', type: 'cart');
            return;
        }

        $this->expandedOrderId = null;
        $this->loadOrders();
        $this->dispatch('show-toast', title: 'Status diperbarui', subtitle: 'Badge pesanan sudah diperbarui.', type: 'success');
    }

    private function loadOrders(): void
    {
        $statuses = $this->statusFilter === 'ALL'
            ? self::ACTIVE_STATUSES
            : $this->statusesForFilter($this->statusFilter);

        $query = DB::table('orders')
            ->leftJoin('users', 'orders.customer_id', '=', 'users.id')
            ->whereIn('orders.order_status', $statuses);

        $this->totalOrders = (clone $query)->count('orders.id');
        $this->page = max(1, min($this->page, $this->totalPages()));

        $rows = $query
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
            ->offset(($this->page - 1) * $this->perPage)
            ->limit($this->perPage)
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
