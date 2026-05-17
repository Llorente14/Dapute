@php
    $rows = $rows ?? [];
    $summary = $summary ?? [
        'product_revenue' => collect($rows)->sum('subtotal'),
        'shipping_revenue' => collect($rows)->sum('shipping'),
        'grand_total' => collect($rows)->sum('total'),
        'order_count' => count($rows),
    ];
    $period = $period ?? now()->format('F Y');
    $printMode = $printMode ?? false;
    $formatCurrency = fn (int $amount) => 'Rp ' . number_format($amount, 0, ',', '.');
    $thClass = $printMode ? '' : 'border-[3px] border-[#012d1d] bg-[#D4EF70] px-4 py-3 font-label text-[10px] font-black uppercase tracking-widest text-[#012d1d]';
    $tdClass = $printMode ? '' : 'border-[3px] border-[#012d1d] px-4 py-3 font-body text-sm font-bold text-[#012d1d]';
    $rightClass = $printMode ? 'right' : 'text-right';
@endphp

@if($printMode)
    <style>
        body { margin: 0; font-family: DejaVu Sans, Arial, sans-serif; color: #012d1d; }
        .report-shell { padding: 24px; }
        .report-header { border: 3px solid #012d1d; padding: 18px; margin-bottom: 18px; }
        .brand { font-size: 28px; font-weight: 900; letter-spacing: -1px; }
        .title { margin-top: 8px; font-size: 18px; font-weight: 800; text-transform: uppercase; }
        .period { margin-top: 4px; font-size: 12px; color: #414844; }
        .summary { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
        .summary td { border: 2px solid #012d1d; padding: 10px; font-size: 11px; }
        .summary strong { display: block; font-size: 16px; margin-top: 4px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { background: #D4EF70; border: 2px solid #012d1d; padding: 9px; font-size: 10px; text-align: left; text-transform: uppercase; }
        .table td { border: 2px solid #012d1d; padding: 9px; font-size: 10px; vertical-align: top; }
        .right { text-align: right; }
        .total-row td { background: #eef5f1; font-weight: 800; }
        .empty { border: 2px solid #012d1d; padding: 20px; text-align: center; font-weight: 800; }
    </style>
@endif

<section class="{{ $printMode ? 'report-shell' : 'border-[3px] border-[#012d1d] bg-white p-5 shadow-[4px_4px_0_0_#012d1d]' }}">
    <header class="{{ $printMode ? 'report-header' : 'mb-5 border-b-[3px] border-[#012d1d] pb-4' }}">
        <div class="{{ $printMode ? 'brand' : 'font-headline text-3xl font-black uppercase tracking-tighter text-[#012d1d]' }}">DAPUTE</div>
        <div class="{{ $printMode ? 'title' : 'mt-2 font-label text-xs font-black uppercase tracking-[0.24em] text-[#012d1d]' }}">Monthly Sales Report</div>
        <div class="{{ $printMode ? 'period' : 'mt-1 font-body text-sm font-bold text-[#414844]' }}">Period: {{ $period }}</div>
    </header>

    <table class="{{ $printMode ? 'summary' : 'mb-5 w-full border-collapse' }}">
        <tr>
            <td class="{{ $printMode ? '' : 'border-[3px] border-[#012d1d] p-3 font-label text-[10px] font-black uppercase tracking-widest text-[#414844]' }}">
                Product Revenue
                <strong>{{ $formatCurrency((int) ($summary['product_revenue'] ?? 0)) }}</strong>
            </td>
            <td class="{{ $printMode ? '' : 'border-[3px] border-[#012d1d] p-3 font-label text-[10px] font-black uppercase tracking-widest text-[#414844]' }}">
                Shipping Revenue
                <strong>{{ $formatCurrency((int) ($summary['shipping_revenue'] ?? 0)) }}</strong>
            </td>
            <td class="{{ $printMode ? '' : 'border-[3px] border-[#012d1d] p-3 font-label text-[10px] font-black uppercase tracking-widest text-[#414844]' }}">
                Orders
                <strong>{{ (int) ($summary['order_count'] ?? count($rows)) }}</strong>
            </td>
            <td class="{{ $printMode ? '' : 'border-[3px] border-[#012d1d] bg-[#D4EF70] p-3 font-label text-[10px] font-black uppercase tracking-widest text-[#012d1d]' }}">
                Grand Total
                <strong>{{ $formatCurrency((int) ($summary['grand_total'] ?? 0)) }}</strong>
            </td>
        </tr>
    </table>

    @if(count($rows) > 0)
        <table class="{{ $printMode ? 'table' : 'w-full min-w-[860px] border-collapse text-left' }}">
            <thead>
                <tr>
                    <th class="{{ $thClass }}">Date</th>
                    <th class="{{ $thClass }}">Order No.</th>
                    <th class="{{ $thClass }}">Product</th>
                    <th class="{{ $thClass }} {{ $rightClass }}">Subtotal</th>
                    <th class="{{ $thClass }} {{ $rightClass }}">Shipping</th>
                    <th class="{{ $thClass }} {{ $rightClass }}">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        <td class="{{ $tdClass }}">{{ $row['date'] ?? '-' }}</td>
                        <td class="{{ $tdClass }}">#{{ $row['order_no'] ?? '-' }}</td>
                        <td class="{{ $tdClass }}">{{ $row['product'] ?? '-' }}</td>
                        <td class="{{ $tdClass }} {{ $rightClass }}">{{ $formatCurrency((int) ($row['subtotal'] ?? 0)) }}</td>
                        <td class="{{ $tdClass }} {{ $rightClass }}">{{ $formatCurrency((int) ($row['shipping'] ?? 0)) }}</td>
                        <td class="{{ $tdClass }} {{ $rightClass }}">{{ $formatCurrency((int) ($row['total'] ?? 0)) }}</td>
                    </tr>
                @endforeach
                <tr class="{{ $printMode ? 'total-row' : 'bg-[#eef5f1] font-black' }}">
                    <td colspan="3" class="{{ $tdClass }}">Grand Total</td>
                    <td class="{{ $tdClass }} {{ $rightClass }}">{{ $formatCurrency((int) ($summary['product_revenue'] ?? 0)) }}</td>
                    <td class="{{ $tdClass }} {{ $rightClass }}">{{ $formatCurrency((int) ($summary['shipping_revenue'] ?? 0)) }}</td>
                    <td class="{{ $tdClass }} {{ $rightClass }}">{{ $formatCurrency((int) ($summary['grand_total'] ?? 0)) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="{{ $printMode ? 'empty' : 'border-[3px] border-[#012d1d] bg-[#eef5f1] p-6 text-center font-label text-xs font-black uppercase tracking-widest text-[#414844]' }}">
            No completed sales data available for this period.
        </div>
    @endif
</section>
