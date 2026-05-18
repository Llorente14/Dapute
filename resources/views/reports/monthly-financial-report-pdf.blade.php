<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>DAPUTE Monthly Sales Report</title>
</head>
<body>
    @include('livewire.dashboard.sales-report-table', [
        'rows' => $rows,
        'summary' => $summary,
        'period' => $period,
        'printMode' => true,
    ])
</body>
</html>
