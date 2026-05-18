<?php

namespace App\Livewire\Transaction;

use App\Actions\Transaction\FetchOrderHistoryAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderHistoryPage extends Component
{
    public array $orders = [];
    public int $limit = 5;

    public function mount(FetchOrderHistoryAction $action): void
    {
        $this->loadOrders($action);
    }

    public function loadMore(FetchOrderHistoryAction $action): void
    {
        $this->limit += 5;
        $this->loadOrders($action);
    }

    public function getActiveOrdersProperty(): array
    {
        return collect($this->orders)
            ->reject(fn (array $order) => $this->isHistoricalStatus($order['order_status'] ?? ''))
            ->values()
            ->toArray();
    }

    public function getHistoricalOrdersProperty(): array
    {
        return collect($this->orders)
            ->filter(fn (array $order) => $this->isHistoricalStatus($order['order_status'] ?? ''))
            ->values()
            ->toArray();
    }

    public function statusLabel(string $status): string
    {
        return str($status)->replace('_', ' ')->title()->toString();
    }

    public function statusTone(string $status): string
    {
        return match ($status) {
            'PENDING_PAYMENT' => 'bg-[#ffdad6] text-[#93000a]',
            'PAID_PROCESSING', 'PICKUP_REQUESTED', 'ON_DELIVERY' => 'bg-[#D4EF70] text-[#012d1d]',
            'DELIVERED', 'COMPLETED' => 'bg-[#012d1d] text-white',
            'CANCELLED', 'FAILED', 'EXPIRED' => 'bg-[#ba1a1a] text-white',
            default => 'bg-white text-[#012d1d]',
        };
    }

    private function loadOrders(FetchOrderHistoryAction $action): void
    {
        $this->orders = $action->execute((string) Auth::id(), $this->limit);
    }

    private function isHistoricalStatus(string $status): bool
    {
        return in_array($status, ['COMPLETED', 'CANCELLED', 'FAILED', 'EXPIRED'], true);
    }

    public function render()
    {
        return view('livewire.transaction.order-history-page')
            ->layout('layouts.app');
    }
}
