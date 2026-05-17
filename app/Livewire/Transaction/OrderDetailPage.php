<?php

namespace App\Livewire\Transaction;

use App\Actions\Payment\GetMidtransSnapTokenAction;
use App\Actions\Transaction\CancelPendingOrderAction;
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
            'PAID_PROCESSING', 'PICKUP_REQUESTED', 'ON_DELIVERY', 'SHIPPED', 'DELIVERED', 'COMPLETED' => 'bg-[#D4EF70] text-[#012d1d]',
            'CANCELLED', 'FAILED', 'EXPIRED' => 'bg-[#ba1a1a] text-white',
            default => 'bg-white text-[#012d1d]',
        };
    }

    public function getCanManagePendingPaymentProperty(): bool
    {
        return ($this->order['order_status'] ?? null) === 'PENDING_PAYMENT';
    }

    public function payNow(GetMidtransSnapTokenAction $snapAction): void
    {
        if (!$this->canManagePendingPayment) {
            $this->addError('order_action', 'This order can no longer be paid.');
            return;
        }

        $snapResult = $snapAction->execute($this->orderId);

        if (!$snapResult['success']) {
            $this->addError('order_action', $snapResult['message'] ?? 'Failed to open payment.');
            return;
        }

        $this->dispatch('open-snap', token: $snapResult['snap_token'], order_id: $this->orderId);
    }

    public function cancelOrder(CancelPendingOrderAction $action, FetchOrderDetailAction $detailAction): void
    {
        if (!$this->canManagePendingPayment) {
            $this->addError('order_action', 'This order can no longer be cancelled.');
            return;
        }

        $result = $action->execute((string) Auth::id(), $this->orderId);

        if (!$result['success']) {
            $this->addError('order_action', $result['message'] ?? 'Failed to cancel order.');
            return;
        }

        $this->refreshOrder($detailAction);

        $this->dispatch('show-toast', title: 'Order Cancelled', subtitle: 'Pending payment order has been cancelled.', type: 'cart');
    }

    private function refreshOrder(FetchOrderDetailAction $action): void
    {
        $result = $action->execute((string) Auth::id(), $this->orderId);

        if ($result['success']) {
            $this->order = $result['order'];
            $this->items = $result['items'];
            $this->address = $result['address'];
        }
    }

    public function render()
    {
        return view('livewire.transaction.order-detail-page')
            ->layout('layouts.app');
    }
}
