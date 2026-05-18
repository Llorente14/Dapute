<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Reports\ExportMonthlyFinancialReportAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FinancialReportExportController extends Controller
{
    public function pdf(Request $request, ExportMonthlyFinancialReportAction $action): Response
    {
        return $action->pdf(
            (int) $request->query('month', now()->month),
            (int) $request->query('year', now()->year),
        );
    }

    public function excel(Request $request, ExportMonthlyFinancialReportAction $action): Response
    {
        return $action->excel(
            (int) $request->query('month', now()->month),
            (int) $request->query('year', now()->year),
        );
    }
}
