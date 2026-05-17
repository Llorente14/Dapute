<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlyFinancialReportExport implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    public function __construct(
        private readonly array $rows,
        private readonly array $summary,
        private readonly string $period,
    ) {}

    public function headings(): array
    {
        return [
            ['DAPUTE Monthly Sales Report'],
            ['Period', $this->period],
            [],
            ['Date', 'Order No.', 'Product', 'Subtotal', 'Shipping', 'Total'],
        ];
    }

    public function array(): array
    {
        $rows = collect($this->rows)
            ->map(fn (array $row): array => [
                $row['date'] ?? '-',
                $row['order_no'] ?? '-',
                $row['product'] ?? '-',
                (int) ($row['subtotal'] ?? 0),
                (int) ($row['shipping'] ?? 0),
                (int) ($row['total'] ?? 0),
            ])
            ->values()
            ->toArray();

        $rows[] = [];
        $rows[] = [
            'Grand Total',
            '',
            'Orders: ' . (int) ($this->summary['order_count'] ?? count($this->rows)),
            (int) ($this->summary['product_revenue'] ?? 0),
            (int) ($this->summary['shipping_revenue'] ?? 0),
            (int) ($this->summary['grand_total'] ?? 0),
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->mergeCells('A1:F1');
        $sheet->getStyle('A1:F4')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(16);
        $sheet->getStyle('A4:F4')->getFill()
            ->setFillType('solid')
            ->getStartColor()
            ->setARGB('FFD4EF70');

        $lastRow = count($this->rows) + 6;
        $sheet->getStyle("A{$lastRow}:F{$lastRow}")->getFont()->setBold(true);

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
