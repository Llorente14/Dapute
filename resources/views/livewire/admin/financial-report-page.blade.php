@php
    $formatCurrency = fn (int $amount) => 'Rp ' . number_format($amount, 0, ',', '.');
    $canExport = auth()->user()?->role === 'owner';
    $topProducts = $reportCharts['top_products'] ?? [];
    $revenueSplit = $reportCharts['revenue_split'] ?? [];
    $maxProductQuantity = max(1, collect($topProducts)->max('quantity') ?? 0);
    $productSplit = $revenueSplit[0] ?? ['percentage' => 0, 'value' => 0, 'label' => 'Product Sales'];
    $shippingSplit = $revenueSplit[1] ?? ['percentage' => 0, 'value' => 0, 'label' => 'Shipping Fees'];
    $donutCircumference = 251.2;
    $productDash = round((((int) ($productSplit['percentage'] ?? 0)) / 100) * $donutCircumference, 2);
    $shippingDash = round((((int) ($shippingSplit['percentage'] ?? 0)) / 100) * $donutCircumference, 2);
@endphp

<section class="min-h-screen px-4 py-6 text-[#012d1d] md:px-8 md:py-10">
    <header class="mb-8 border-b-[4px] border-[#012d1d] pb-6">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="mb-3 font-label text-xs font-black uppercase tracking-[0.28em] text-[#414844]">
                    Owner Financial Ledger
                </p>
                <h1 class="font-headline text-4xl font-black uppercase leading-[0.9] tracking-tighter md:text-6xl">
                    Monthly Sales Report
                </h1>
                <p class="mt-4 max-w-[720px] font-body text-sm font-semibold leading-relaxed text-[#414844] md:text-base">
                    Monthly order revenue table for subtotal, shipping cost, and collected total review.
                </p>
            </div>

            <div
                x-data="{ exporting: null }"
                class="grid grid-cols-2 gap-3 sm:flex"
            >
                @if($canExport)
                    <a
                        href="{{ route('admin.reports.export.pdf', ['month' => $month, 'year' => $year]) }}"
                        @click="if (exporting) { $event.preventDefault(); return; } exporting = 'pdf'; setTimeout(() => exporting = null, 5000)"
                        class="inline-flex items-center justify-center gap-2 border-[3px] border-[#012d1d] px-4 py-3 font-label text-[11px] font-black uppercase tracking-widest shadow-[3px_3px_0_0_#012d1d] transition-all hover:-translate-y-0.5"
                        :class="exporting ? 'pointer-events-none cursor-not-allowed bg-[#dde4e0] text-[#717973] shadow-none' : 'bg-white text-[#012d1d] hover:bg-[#D4EF70]'"
                        :aria-disabled="exporting ? 'true' : 'false'"
                    >
                        <span class="material-symbols-outlined text-[18px]" :class="exporting === 'pdf' ? 'animate-spin' : ''" x-text="exporting === 'pdf' ? 'sync' : 'picture_as_pdf'">picture_as_pdf</span>
                        <span x-text="exporting === 'pdf' ? 'Preparing PDF' : 'Export PDF'">Export PDF</span>
                    </a>
                    <a
                        href="{{ route('admin.reports.export.excel', ['month' => $month, 'year' => $year]) }}"
                        @click="if (exporting) { $event.preventDefault(); return; } exporting = 'excel'; setTimeout(() => exporting = null, 5000)"
                        class="inline-flex items-center justify-center gap-2 border-[3px] border-[#012d1d] px-4 py-3 font-label text-[11px] font-black uppercase tracking-widest shadow-[3px_3px_0_0_#D4EF70] transition-all hover:-translate-y-0.5"
                        :class="exporting ? 'pointer-events-none cursor-not-allowed bg-[#dde4e0] text-[#717973] shadow-none' : 'bg-[#012d1d] text-white hover:bg-[#D4EF70] hover:text-[#012d1d]'"
                        :aria-disabled="exporting ? 'true' : 'false'"
                    >
                        <span class="material-symbols-outlined text-[18px]" :class="exporting === 'excel' ? 'animate-spin' : ''" x-text="exporting === 'excel' ? 'sync' : 'table_view'">table_view</span>
                        <span x-text="exporting === 'excel' ? 'Preparing Excel' : 'Export Excel'">Export Excel</span>
                    </a>
                @endif
            </div>
        </div>
    </header>

    <div class="relative z-20 mb-6 grid gap-4 lg:grid-cols-[1fr_auto] lg:items-end">
        <div class="grid gap-4 sm:grid-cols-2">
            <div class="relative" x-data="{ open: false }">
                <p class="mb-2 block font-label text-[11px] font-black uppercase tracking-widest text-[#414844]">Month</p>
                <button
                    type="button"
                    @click="open = !open"
                    @click.outside="open = false"
                    class="flex w-full items-center justify-between gap-6 border-[3px] border-[#012d1d] bg-white px-4 py-3 font-label text-xs font-black uppercase tracking-widest text-[#012d1d] shadow-[4px_4px_0_0_#012d1d] transition-all hover:bg-[#D4EF70] focus:outline-none focus:bg-[#D4EF70]"
                >
                    <span>{{ $this->monthOptions[$month] ?? 'Select Month' }}</span>
                    <span class="material-symbols-outlined text-[20px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>

                <div
                    x-show="open"
                    x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="absolute left-0 top-full z-40 mt-1 max-h-72 w-full overflow-y-auto border-[3px] border-[#012d1d] bg-white shadow-[4px_4px_0_0_#012d1d]"
                    style="display: none;"
                >
                    @foreach($this->monthOptions as $value => $label)
                        <button
                            type="button"
                            wire:click="$set('month', {{ $value }})"
                            @click="open = false"
                            class="flex w-full items-center justify-between px-4 py-3 text-left font-label text-xs font-black uppercase tracking-widest transition-colors {{ (int) $month === (int) $value ? 'bg-[#012d1d] text-white' : 'text-[#012d1d] hover:bg-[#012d1d] hover:text-white' }}"
                        >
                            <span>{{ $label }}</span>
                            @if((int) $month === (int) $value)
                                <span class="material-symbols-outlined text-[16px]">check</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="relative" x-data="{ open: false }">
                <p class="mb-2 block font-label text-[11px] font-black uppercase tracking-widest text-[#414844]">Year</p>
                <button
                    type="button"
                    @click="open = !open"
                    @click.outside="open = false"
                    class="flex w-full items-center justify-between gap-6 border-[3px] border-[#012d1d] bg-white px-4 py-3 font-label text-xs font-black uppercase tracking-widest text-[#012d1d] shadow-[4px_4px_0_0_#012d1d] transition-all hover:bg-[#D4EF70] focus:outline-none focus:bg-[#D4EF70]"
                >
                    <span>{{ $year }}</span>
                    <span class="material-symbols-outlined text-[20px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>

                <div
                    x-show="open"
                    x-cloak
                    x-transition:enter="transition ease-out duration-150"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-100"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2"
                    class="absolute left-0 top-full z-40 mt-1 w-full border-[3px] border-[#012d1d] bg-white shadow-[4px_4px_0_0_#012d1d]"
                    style="display: none;"
                >
                    @foreach($this->yearOptions as $yearOption)
                        <button
                            type="button"
                            wire:click="$set('year', {{ $yearOption }})"
                            @click="open = false"
                            class="flex w-full items-center justify-between px-4 py-3 text-left font-label text-xs font-black uppercase tracking-widest transition-colors {{ (int) $year === (int) $yearOption ? 'bg-[#012d1d] text-white' : 'text-[#012d1d] hover:bg-[#012d1d] hover:text-white' }}"
                        >
                            <span>{{ $yearOption }}</span>
                            @if((int) $year === (int) $yearOption)
                                <span class="material-symbols-outlined text-[16px]">check</span>
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="border-[3px] border-[#012d1d] bg-[#D4EF70] px-5 py-4 shadow-[4px_4px_0_0_#012d1d]">
            <p class="font-label text-[10px] font-black uppercase tracking-widest text-[#012d1d]">Orders This Period</p>
            <p class="font-headline text-3xl font-black leading-none tracking-tighter">{{ $this->totalOrdersInRange }}</p>
            <p class="mt-1 font-body text-[11px] font-black text-[#414844]">
                {{ $this->monthOptions[$month] ?? 'Selected Month' }} {{ $year }}
            </p>
        </div>
    </div>

    <section class="mb-6 grid gap-4 xl:grid-cols-[1.1fr_0.9fr]">
        <article class="border-[3px] border-[#012d1d] bg-white p-5 shadow-[4px_4px_0_0_#012d1d]">
            <div class="mb-4 flex items-start justify-between gap-4 border-b-[3px] border-[#012d1d] pb-3">
                <div>
                    <p class="font-label text-[10px] font-black uppercase tracking-[0.24em] text-[#414844]">Product Demand</p>
                    <h2 class="font-headline text-2xl font-black uppercase tracking-tighter text-[#012d1d]">Top Products Bought</h2>
                </div>
                <span class="material-symbols-outlined border-[2px] border-[#012d1d] bg-[#D4EF70] p-2 text-[20px]">leaderboard</span>
            </div>

            <div class="grid gap-3">
                @forelse($topProducts as $product)
                    @php
                        $barWidth = max(8, ((int) $product['quantity'] / $maxProductQuantity) * 100);
                    @endphp
                    <div class="border-[2px] border-[#012d1d] bg-[#eef5f1] p-3">
                        <div class="mb-2 flex items-start justify-between gap-3">
                            <p class="font-headline text-sm font-black uppercase tracking-tight text-[#012d1d]">{{ $product['label'] }}</p>
                            <p class="shrink-0 font-label text-xs font-black uppercase tracking-widest text-[#012d1d]">{{ $product['quantity'] }} sold</p>
                        </div>
                        <div class="h-4 border-[2px] border-[#012d1d] bg-white">
                            <div class="h-full bg-[#D4EF70]" style="width: {{ $barWidth }}%"></div>
                        </div>
                        <p class="mt-2 font-body text-xs font-bold text-[#414844]">{{ $formatCurrency((int) $product['revenue']) }} product revenue</p>
                    </div>
                @empty
                    <div class="border-[3px] border-[#012d1d] bg-[#eef5f1] p-6 text-center font-label text-xs font-black uppercase tracking-widest text-[#414844]">
                        No product demand available.
                    </div>
                @endforelse
            </div>
        </article>

        <article class="border-[3px] border-[#012d1d] bg-white p-5 shadow-[4px_4px_0_0_#012d1d]">
            <div class="mb-4 flex items-start justify-between gap-4 border-b-[3px] border-[#012d1d] pb-3">
                <div>
                    <p class="font-label text-[10px] font-black uppercase tracking-[0.24em] text-[#414844]">Income Mix</p>
                    <h2 class="font-headline text-2xl font-black uppercase tracking-tighter text-[#012d1d]">Revenue Split</h2>
                </div>
                <span class="material-symbols-outlined border-[2px] border-[#012d1d] bg-[#D4EF70] p-2 text-[20px]">donut_large</span>
            </div>

            @if(count($revenueSplit) > 0)
                <div class="grid gap-4 border-[3px] border-[#012d1d] bg-[#eef5f1] p-4 md:grid-cols-[220px_1fr] md:items-center xl:grid-cols-1 2xl:grid-cols-[220px_1fr]">
                    <div class="relative mx-auto h-[220px] w-[220px]">
                        <svg viewBox="0 0 100 100" class="h-full w-full -rotate-90" role="img" aria-label="Revenue split donut chart">
                            <circle cx="50" cy="50" r="40" fill="none" stroke="#ffffff" stroke-width="18" />
                            <circle
                                cx="50"
                                cy="50"
                                r="40"
                                fill="none"
                                stroke="#012d1d"
                                stroke-width="18"
                                stroke-dasharray="{{ $productDash }} {{ $donutCircumference - $productDash }}"
                                stroke-dashoffset="0"
                            />
                            <circle
                                cx="50"
                                cy="50"
                                r="40"
                                fill="none"
                                stroke="#D4EF70"
                                stroke-width="18"
                                stroke-dasharray="{{ $shippingDash }} {{ $donutCircumference - $shippingDash }}"
                                stroke-dashoffset="-{{ $productDash }}"
                            />
                            <circle cx="50" cy="50" r="28" fill="#eef5f1" stroke="#012d1d" stroke-width="3" />
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center">
                            <p class="font-label text-[10px] font-black uppercase tracking-widest text-[#414844]">Total Mix</p>
                            <p class="font-headline text-3xl font-black tracking-tighter text-[#012d1d]">{{ $productSplit['percentage'] ?? 0 }}%</p>
                            <p class="font-body text-[11px] font-black text-[#414844]">Product</p>
                        </div>
                    </div>

                    <div class="grid gap-3">
                        @foreach($revenueSplit as $index => $segment)
                            <div class="flex items-center justify-between gap-3 border-[2px] border-[#012d1d] bg-white p-3">
                                <div class="flex items-center gap-2">
                                    <span class="h-4 w-4 border-[2px] border-[#012d1d] {{ $index === 0 ? 'bg-[#012d1d]' : 'bg-[#D4EF70]' }}"></span>
                                    <p class="font-label text-[10px] font-black uppercase tracking-widest text-[#414844]">{{ $segment['label'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="font-headline text-base font-black tracking-tight text-[#012d1d]">{{ $segment['percentage'] }}%</p>
                                    <p class="font-body text-[11px] font-bold text-[#414844]">{{ $formatCurrency((int) $segment['value']) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="border-[3px] border-[#012d1d] bg-[#eef5f1] p-6 text-center font-label text-xs font-black uppercase tracking-widest text-[#414844]">
                    No revenue split available.
                </div>
            @endif
        </article>
    </section>

    <div class="overflow-hidden border-[3px] border-[#012d1d] bg-white shadow-[6px_6px_0_0_#012d1d]">
        <div class="border-b-[3px] border-[#012d1d] bg-[#012d1d] px-4 py-4 text-white md:px-6">
            <div class="flex flex-col gap-1 md:flex-row md:items-center md:justify-between">
                <h2 class="font-label text-xs font-black uppercase tracking-[0.24em]">Financial Table</h2>
                <p class="font-body text-xs font-bold text-white/80">
                    {{ $this->monthOptions[$month] ?? 'Selected Month' }} {{ $year }}
                </p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[960px] border-collapse text-left">
                <thead class="border-b-[3px] border-[#012d1d] bg-[#eef5f1]">
                    <tr>
                        <th class="px-4 py-4 font-label text-[11px] font-black uppercase tracking-widest">Date</th>
                        <th class="px-4 py-4 font-label text-[11px] font-black uppercase tracking-widest">Order No.</th>
                        <th class="px-4 py-4 font-label text-[11px] font-black uppercase tracking-widest">Product</th>
                        <th class="px-4 py-4 text-right font-label text-[11px] font-black uppercase tracking-widest">Subtotal</th>
                        <th class="px-4 py-4 text-right font-label text-[11px] font-black uppercase tracking-widest">Shipping</th>
                        <th class="px-4 py-4 text-right font-label text-[11px] font-black uppercase tracking-widest">Total</th>
                    </tr>
                </thead>

                <tbody class="divide-y-[3px] divide-[#012d1d]">
                    @forelse($reportRows as $row)
                        <tr class="bg-white">
                            <td class="px-4 py-4 font-body text-sm font-bold">{{ $row['date'] }}</td>
                            <td class="px-4 py-4 font-label text-xs font-black uppercase tracking-widest">#{{ $row['order_no'] }}</td>
                            <td class="px-4 py-4 font-headline text-sm font-black uppercase tracking-tight">{{ $row['product'] }}</td>
                            <td class="px-4 py-4 text-right font-label text-sm font-black">{{ $formatCurrency((int) $row['subtotal']) }}</td>
                            <td class="px-4 py-4 text-right font-label text-sm font-black">{{ $formatCurrency((int) $row['shipping']) }}</td>
                            <td class="px-4 py-4 text-right font-label text-sm font-black">{{ $formatCurrency((int) $row['total']) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="mx-auto max-w-[520px] border-[3px] border-[#012d1d] bg-[#eef5f1] p-6 shadow-[4px_4px_0_0_#012d1d]">
                                    <span class="material-symbols-outlined mb-3 text-4xl text-[#012d1d]">receipt_long</span>
                                    <h3 class="font-headline text-2xl font-black uppercase tracking-tighter">No Transactions Yet</h3>
                                    <p class="mt-2 font-body text-sm font-bold leading-relaxed text-[#414844]">
                                        No completed sales data available for selected month. Data source will be connected in SCRUM-60.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot class="border-t-[3px] border-[#012d1d] bg-[#D4EF70]">
                    <tr>
                        <td colspan="3" class="px-4 py-4 font-label text-xs font-black uppercase tracking-[0.2em]">Grand Total</td>
                        <td class="px-4 py-4 text-right font-label text-sm font-black">{{ $formatCurrency($this->totals['subtotal']) }}</td>
                        <td class="px-4 py-4 text-right font-label text-sm font-black">{{ $formatCurrency($this->totals['shipping']) }}</td>
                        <td class="px-4 py-4 text-right font-label text-sm font-black">{{ $formatCurrency($this->totals['total']) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="flex flex-col gap-3 border-t-[3px] border-[#012d1d] bg-[#eef5f1] p-4 md:flex-row md:items-center md:justify-between">
            <p class="font-label text-[11px] font-black uppercase tracking-widest text-[#414844]">
                Showing {{ count($reportRows) }} of {{ count($reportRows) }} rows
            </p>
            <div class="flex items-center gap-2">
                <button type="button" disabled class="inline-flex h-10 min-w-10 cursor-not-allowed items-center justify-center border-[3px] border-[#717973] bg-[#eef5f1] px-3 font-label text-[11px] font-black uppercase tracking-widest text-[#717973]">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </button>
                <span class="inline-flex h-10 min-w-10 items-center justify-center border-[3px] border-[#012d1d] bg-[#012d1d] px-3 font-label text-[11px] font-black uppercase tracking-widest text-white shadow-[2px_2px_0_0_#012d1d]">
                    {{ $page }}
                </span>
                <button type="button" disabled class="inline-flex h-10 min-w-10 cursor-not-allowed items-center justify-center border-[3px] border-[#717973] bg-[#eef5f1] px-3 font-label text-[11px] font-black uppercase tracking-widest text-[#717973]">
                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </button>
            </div>
        </div>
    </div>
</section>
