<?php

namespace App\Livewire\Transaction;

use App\Actions\Transaction\FetchOrderDetailAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderDetailPage extends Component
{
    public string $orderId;
    public array $order = [];
    public array $items = [];
    public ?array $address = null;

    public function mount(string $id, FetchOrderDetailAction $action): void
    {
        $this->orderId = $id;
        $result = $action->execute((string) Auth::id(), $id);

        if (!$result['success']) {
            abort(404);
        }

        $this->order = $result['order'];
        $this->items = $result['items'];
        $this->address = $result['address'];
    }

    public function getStatusLabelProperty(): string
    {
        return str($this->order['order_status'] ?? 'PENDING_PAYMENT')
            ->replace('_', ' ')
            ->title()
            ->toString();
    }

    public function getStatusToneProperty(): string
    {
        return match ($this->order['order_status'] ?? 'PENDING_PAYMENT') {
            'PAID', 'PROCESSING', 'ON_DELIVERY', 'SHIPPED', 'DELIVERED', 'COMPLETED' => 'bg-[#D4EF70] text-[#012d1d]',
            'CANCELLED', 'FAILED', 'EXPIRED' => 'bg-[#ba1a1a] text-white',
            default => 'bg-white text-[#012d1d]',
        };
    }

    public function render()
    {
        return view('livewire.transaction.order-detail-page')
            ->layout('layouts.app');
    }
}
