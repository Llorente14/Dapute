<?php

namespace App\Actions\Reports;

use App\Enums\OrderStatus;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FetchMonthlyFinancialReportAction
{
    private const REPORTABLE_STATUSES = [
        'paid',
        'processing',
        'shipped',
        'done',
        'PAID',
        'PROCESSING',
        'SHIPPED',
        'DONE',
        'IN_PROCESSING',
        OrderStatus::PAID_PROCESSING->value,
        OrderStatus::PICKUP_REQUESTED->value,
        OrderStatus::ON_DELIVERY->value,
        OrderStatus::DELIVERED->value,
        OrderStatus::COMPLETED->value,
    ];

    public function execute(int $month, int $year, int $limit = 20): array
    {
        $month = max(1, min(12, $month));
        $year = max(2000, min(2100, $year));
        $limit = max(1, min(20, $limit));

        $orders = DB::table('orders')
            ->whereMonth('orders.order_date', $month)
            ->whereYear('orders.order_date', $year)
            ->whereIn('orders.order_status', self::REPORTABLE_STATUSES)
            ->whereNotIn('orders.order_status', [
                'pending_payment',
                'PENDING_PAYMENT',
                OrderStatus::PENDING_PAYMENT->value,
                'cancelled',
                'CANCELLED',
                OrderStatus::CANCELLED->value,
            ])
            ->select(
                'orders.id',
                'orders.order_date',
                'orders.subtotal_amount',
                'orders.shipping_fee',
                'orders.total_payment',
                'orders.order_status',
            )
            ->orderByDesc('orders.order_date')
            ->limit($limit)
            ->get();

        $itemsByOrder = $this->itemsByOrder($orders->pluck('id'));

        $rows = $orders
            ->map(fn ($order): array => [
                'id' => (string) $order->id,
                'date' => optional($order->order_date ? \Carbon\Carbon::parse($order->order_date) : null)->format('d M Y') ?? '-',
                'order_no' => strtoupper(substr((string) $order->id, 0, 8)),
                'product' => $this->productSummary($itemsByOrder->get($order->id, collect())),
                'subtotal' => (int) ($order->subtotal_amount ?? $itemsByOrder->get($order->id, collect())->sum('subtotal')),
                'shipping' => (int) ($order->shipping_fee ?? 0),
                'total' => (int) ($order->total_payment ?? 0),
                'order_status' => (string) $order->order_status,
            ])
            ->values()
            ->toArray();

        return [
            'rows' => $rows,
            'summary' => [
                'product_revenue' => (int) collect($rows)->sum('subtotal'),
                'shipping_revenue' => (int) collect($rows)->sum('shipping'),
                'grand_total' => (int) collect($rows)->sum('total'),
                'order_count' => count($rows),
            ],
        ];
    }

    private function itemsByOrder(Collection $orderIds): Collection
    {
        if ($orderIds->isEmpty()) {
            return collect();
        }

        return DB::table('order_items')
            ->whereIn('order_id', $orderIds->all())
            ->select('order_id', 'cake_name_snapshot', 'quantity', 'subtotal')
            ->orderBy('cake_name_snapshot')
            ->get()
            ->groupBy('order_id');
    }

    private function productSummary(Collection $items): string
    {
        if ($items->isEmpty()) {
            return 'Order Items';
        }

        $firstItem = $items->first();
        $firstName = $firstItem->cake_name_snapshot ?? 'Order Items';
        $extraCount = $items->count() - 1;

        return $extraCount > 0
            ? "{$firstName} + {$extraCount} more"
            : $firstName;
    }
}
