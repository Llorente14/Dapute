<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class FinancialReportPage extends Component
{
    public int $month;
    public int $year;
    public int $page = 1;
    public int $perPage = 20;
    public array $reportRows = [];

    public function mount(): void
    {
        $this->month = (int) now()->month;
        $this->year = (int) now()->year;
    }

    public function updatedMonth(): void
    {
        $this->page = 1;
    }

    public function updatedYear(): void
    {
        $this->page = 1;
    }

    public function getMonthOptionsProperty(): array
    {
        return collect(range(1, 12))
            ->mapWithKeys(fn (int $month) => [
                $month => now()->month($month)->format('F'),
            ])
            ->all();
    }

    public function getYearOptionsProperty(): array
    {
        $currentYear = (int) now()->year;

        return range($currentYear, $currentYear - 4);
    }

    public function getTotalsProperty(): array
    {
        return collect($this->reportRows)->reduce(
            fn (array $totals, array $row) => [
                'subtotal' => $totals['subtotal'] + (int) ($row['subtotal'] ?? 0),
                'shipping' => $totals['shipping'] + (int) ($row['shipping'] ?? 0),
                'total' => $totals['total'] + (int) ($row['total'] ?? 0),
            ],
            ['subtotal' => 0, 'shipping' => 0, 'total' => 0],
        );
    }

    public function getTotalOrdersInRangeProperty(): int
    {
        return count($this->reportRows);
    }

    public function render()
    {
        return view('livewire.admin.financial-report-page')
            ->layout('layouts.admin');
    }
}
