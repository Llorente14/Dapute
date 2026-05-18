<?php

namespace App\Livewire\Profile;

use App\Actions\Orders\FetchUserOrdersAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileOrderHistory extends Component
{
    public array $orders = [];
    public string $filterStatus = 'all';
    public ?string $expandedOrderId = null;

    public function mount(FetchUserOrdersAction $action): void
    {
        $this->loadOrders($action);
    }

    public function setFilter(string $status, FetchUserOrdersAction $action): void
    {
        $this->filterStatus = $status;
        $this->expandedOrderId = null;
        $this->loadOrders($action);
    }

    public function toggleExpand(string $orderId): void
    {
        $this->expandedOrderId = $this->expandedOrderId === $orderId ? null : $orderId;
    }

    public function filters(): array
    {
        return [
            'all' => 'All',
            'PENDING_PAYMENT' => 'Pending payment',
            'PAID_PROCESSING' => 'Paid Processing',
            'ON_DELIVERY' => 'On Delivery',
            'COMPLETED' => 'Completed',
            'CANCELLED' => 'Cancelled',
        ];
    }

    public function statusLabel(string $status): string
    {
        return match ($status) {
            'PENDING_PAYMENT' => 'Pending payment',
            'PAID_PROCESSING', 'IN_PROCESSING' => 'Paid Processing',
            'ON_DELIVERY', 'SHIPPED' => 'On Delivery',
            'COMPLETED' => 'Completed',
            'CANCELLED' => 'Cancelled',
            default => str($status)->replace('_', ' ')->title()->toString(),
        };
    }

    public function statusBadgeClass(string $status): string
    {
        return match ($status) {
            'PENDING_PAYMENT' => 'bg-[#FFF3CD] text-[#012d1d]',
            'PAID_PROCESSING', 'IN_PROCESSING' => 'bg-[#D4EF70] text-[#012d1d]',
            'ON_DELIVERY', 'SHIPPED' => 'bg-[#CCE5FF] text-[#012d1d]',
            'COMPLETED' => 'bg-[#012d1d] text-white',
            'CANCELLED' => 'bg-[#F8D7DA] text-[#012d1d]',
            default => 'bg-white text-[#012d1d]',
        };
    }

    private function loadOrders(FetchUserOrdersAction $action): void
    {
        $this->orders = $action->execute((string) Auth::id(), $this->filterStatus);
    }

    public function render()
    {
        return view('livewire.profile.profile-order-history');
    }
}
