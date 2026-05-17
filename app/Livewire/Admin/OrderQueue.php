<?php

namespace App\Livewire\Admin;

use App\Actions\Logistics\ManualShipmentAction;
use App\Actions\Logistics\RequestBiteshipPickupAction;
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
    public bool $showManualShipmentModal = false;
    public ?string $manualShipmentOrderId = null;
    public string $manualTrackingId = '';
    public ?array $manualShipmentOrder = null;
    public bool $showPickupModal = false;
    public ?string $pickupOrderId = null;
    public ?array $pickupOrder = null;

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

    public function openPickupModal(string $orderId): void
    {
        $order = $this->findManualShipmentOrder($orderId);

        if (!$order) {
            $this->dispatch('show-toast', title: 'Request Pickup Failed', subtitle: 'Order not found.', type: 'cart');
            return;
        }

        if ($order['order_status'] !== OrderStatus::PICKUP_REQUESTED->value) {
            $this->dispatch('show-toast', title: 'Request Pickup Failed', subtitle: 'Only pickup requested orders can be processed.', type: 'cart');
            return;
        }

        $this->pickupOrderId = $orderId;
        $this->pickupOrder = $order;
        $this->loadOrderItems($orderId);
        $this->showPickupModal = true;
    }

    public function closePickupModal(): void
    {
        $this->showPickupModal = false;
        $this->pickupOrderId = null;
        $this->pickupOrder = null;
    }

    public function submitPickup(): void
    {
        if (!$this->pickupOrderId) {
            return;
        }

        \Illuminate\Support\Facades\Log::info('submitPickup called for order: ' . $this->pickupOrderId);
        $action = app(RequestBiteshipPickupAction::class);
        $result = $action->execute($this->pickupOrderId);

        if (!$result['success']) {
            \Illuminate\Support\Facades\Log::error('Pickup failed: ' . ($result['message'] ?? 'Unknown error'));
            $this->dispatch('show-toast', title: 'Pickup Failed', subtitle: $result['message'] ?? 'Biteship pickup failed.', type: 'cart');
            return;
        }

        $this->closePickupModal();
        $this->expandedOrderId = null;
        $this->loadOrders();
        $this->dispatch('show-toast', title: 'Pickup Requested', subtitle: $result['message'] ?? 'Courier pickup requested.', type: 'success');
    }

    public function openManualShipmentModal(string $orderId): void
    {
        $order = $this->findManualShipmentOrder($orderId);

        if (!$order) {
            $this->dispatch('show-toast', title: 'Manual Shipment Failed', subtitle: 'Order not found.', type: 'cart');
            return;
        }

        if ($order['order_status'] !== OrderStatus::PICKUP_REQUESTED->value) {
            $this->dispatch('show-toast', title: 'Manual Shipment Failed', subtitle: 'Only pickup requested orders can be shipped manually.', type: 'cart');
            return;
        }

        $this->resetErrorBag('manualTrackingId');
        $this->manualShipmentOrderId = $orderId;
        $this->manualTrackingId = '';
        $this->manualShipmentOrder = $order;
        $this->showManualShipmentModal = true;
    }

    public function closeManualShipmentModal(): void
    {
        $this->resetErrorBag('manualTrackingId');
        $this->showManualShipmentModal = false;
        $this->manualShipmentOrderId = null;
        $this->manualTrackingId = '';
        $this->manualShipmentOrder = null;
    }

    public function submitManualShipment(ManualShipmentAction $action): void
    {
        if (!$this->manualShipmentOrderId) {
            $this->dispatch('show-toast', title: 'Manual Shipment Failed', subtitle: 'Order not selected.', type: 'cart');
            return;
        }

        $trackingId = trim($this->manualTrackingId);

        if ($trackingId === '') {
            $this->addError('manualTrackingId', 'Tracking number is required.');
            return;
        }

        try {
            $result = $action($this->manualShipmentOrderId, $trackingId);
        } catch (AuthorizationException $exception) {
            $this->dispatch('show-toast', title: 'Manual Shipment Failed', subtitle: $exception->getMessage(), type: 'cart');
            return;
        }

        if (!$result['success']) {
            $this->addError('manualTrackingId', $result['message'] ?? 'Manual shipment failed.');
            $this->dispatch('show-toast', title: 'Manual Shipment Failed', subtitle: $result['message'] ?? 'Manual shipment failed.', type: 'cart');
            return;
        }

        $this->closeManualShipmentModal();
        $this->expandedOrderId = null;
        $this->loadOrders();
        $this->dispatch('show-toast', title: 'Manual Shipment Saved', subtitle: 'Tracking number saved and order moved to delivery.', type: 'success');
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
            'ALL' => 'All',
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
                'label' => 'Cancelled',
                'status' => OrderStatus::CANCELLED->value,
                'action' => 'status',
                'icon' => 'cancel',
                'available' => in_array($status, [
                    OrderStatus::PENDING_PAYMENT->value,
                    OrderStatus::PAID_PROCESSING->value,
                ], true),
            ],
            [
                'label' => 'Ready to Ship',
                'status' => OrderStatus::PICKUP_REQUESTED->value,
                'action' => 'status',
                'icon' => 'outbox',
                'available' => in_array($status, ['IN_PROCESSING', OrderStatus::PAID_PROCESSING->value], true),
            ],
            [
                'label' => 'Request Pickup',
                'status' => null,
                'action' => 'pickup',
                'icon' => 'local_shipping',
                'available' => $status === OrderStatus::PICKUP_REQUESTED->value,
            ],
            [
                'label' => 'Manual Shipment',
                'status' => null,
                'action' => 'manual',
                'icon' => 'receipt_long',
                'available' => $status === OrderStatus::PICKUP_REQUESTED->value,
            ],
            [
                'label' => 'Mark Delivered',
                'status' => OrderStatus::DELIVERED->value,
                'action' => 'status',
                'icon' => 'inventory',
                'available' => $status === OrderStatus::ON_DELIVERY->value,
            ],
            [
                'label' => 'Complete Order',
                'status' => OrderStatus::COMPLETED->value,
                'action' => 'status',
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
        $this->dispatch('show-toast', title: 'Status updated', subtitle: 'Order badge has been updated.', type: 'success');
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

    private function findManualShipmentOrder(string $orderId): ?array
    {
        $order = DB::table('orders')
            ->leftJoin('users', 'orders.customer_id', '=', 'users.id')
            ->leftJoin('order_addresses', 'orders.id', '=', 'order_addresses.order_id')
            ->where('orders.id', $orderId)
            ->select(
                'orders.id',
                'orders.order_status',
                'orders.total_payment',
                'orders.tracking_id',
                'orders.biteship_order_id',
                'users.full_name as customer_name',
                'users.email as customer_email',
                'order_addresses.recipient_name',
                'order_addresses.recipient_phone',
                'order_addresses.shipping_address',
                'order_addresses.city',
                'order_addresses.postal_code'
            )
            ->first();

        if (!$order) {
            return null;
        }

        return [
            'id' => $order->id,
            'short_id' => strtoupper(substr((string) $order->id, 0, 8)),
            'order_status' => $order->order_status,
            'customer_name' => $order->customer_name ?: 'Unknown Customer',
            'customer_email' => $order->customer_email,
            'recipient_name' => $order->recipient_name,
            'recipient_phone' => $order->recipient_phone,
            'shipping_address' => $order->shipping_address,
            'city' => $order->city,
            'postal_code' => $order->postal_code,
            'total_payment' => (int) $order->total_payment,
            'tracking_id' => $order->tracking_id,
            'biteship_order_id' => $order->biteship_order_id,
        ];
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
