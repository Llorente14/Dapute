<?php

namespace App\Actions\Reports;

use App\Exports\MonthlyFinancialReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ExportMonthlyFinancialReportAction
{
    public function __construct(
        private readonly FetchMonthlyFinancialReportAction $fetchReport,
    ) {}

    public function pdf(int $month, int $year, int $limit = 20): Response
    {
        $this->authorizeOwner();

        $report = $this->fetchReport->execute($month, $year, $limit);
        $period = $this->periodLabel($month, $year);

        return Pdf::loadView('reports.monthly-financial-report-pdf', [
            'rows' => $report['rows'],
            'summary' => $report['summary'],
            'period' => $period,
            'month' => $month,
            'year' => $year,
        ])
            ->setPaper('a4', 'landscape')
            ->download($this->filename($year, $month, 'pdf'));
    }

    public function excel(int $month, int $year, int $limit = 20): BinaryFileResponse
    {
        $this->authorizeOwner();

        $report = $this->fetchReport->execute($month, $year, $limit);

        return ExcelFacade::download(
            new MonthlyFinancialReportExport(
                $report['rows'],
                $report['summary'],
                $this->periodLabel($month, $year),
            ),
            $this->filename($year, $month, 'xlsx'),
            ExcelFormat::XLSX,
        );
    }

    private function authorizeOwner(): void
    {
        if (!Auth::check()) {
            throw new AuthorizationException('Only Owner can export financial reports.');
        }

        $role = DB::table('users')->where('id', Auth::id())->value('role');

        if ($role !== 'owner') {
            throw new AuthorizationException('Only Owner can export financial reports.');
        }
    }

    private function filename(int $year, int $month, string $extension): string
    {
        return sprintf('laporan-dapute-%04d-%02d.%s', $year, $month, $extension);
    }

    private function periodLabel(int $month, int $year): string
    {
        return now()->month(max(1, min(12, $month)))->format('F') . ' ' . $year;
    }
}
