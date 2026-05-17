@php
    $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d M Y') : 'Date unavailable';
    $formatTime = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('H:i') : '--:--';
    $fallbackImage = 'https://placehold.co/240x180/012d1d/D4EF70?text=DAPUTE';
@endphp

<section class="min-h-screen bg-[#f4fbf7] text-[#012d1d]">
    <div class="mx-auto max-w-[1180px] px-3 py-6 md:px-8 md:py-14">
        <header class="border-b-[3px] border-[#012d1d] pb-5 md:pb-9">
            <div class="flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                <div class="max-w-[680px]">
                    <p class="mb-3 font-label text-xs font-black uppercase tracking-[0.28em] text-[#3d6651]">
                        Transaction Ledger
                    </p>
                    <h1 class="font-headline text-4xl font-black uppercase leading-[0.9] tracking-tighter md:text-7xl">
                        Order<br class="hidden sm:block"> History
                    </h1>
                    <p class="mt-4 max-w-[560px] font-body text-sm font-semibold leading-relaxed text-[#3d6651] md:mt-5 md:text-lg">
                        Review every order, payment state, and delivery movement from your Dapute purchases.
                    </p>
                </div>

                <a
                    href="/catalog"
                    class="inline-flex w-fit items-center justify-center gap-2 border-[3px] border-[#012d1d] bg-[#D4EF70] px-5 py-3 font-label text-xs font-black uppercase tracking-widest shadow-[4px_4px_0_0_#012d1d] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_#012d1d]"
                >
                    <span class="material-symbols-outlined text-[18px]">bakery_dining</span>
                    Shop Again
                </a>
            </div>
        </header>

        <div class="mt-7 grid grid-cols-1 gap-7 md:mt-10 lg:grid-cols-[1fr_320px] lg:items-start">
            <main class="flex flex-col gap-8 md:gap-10">
                <section>
                    <div class="mb-4 flex items-center justify-between gap-4 md:mb-5">
                        <h2 class="flex items-center gap-2 font-headline text-xl font-black uppercase tracking-tighter md:gap-3 md:text-3xl">
                            <span class="block h-6 w-2 bg-[#012d1d] md:h-8"></span>
                            Active Orders
                        </h2>
                        <span class="font-label text-xs font-black uppercase tracking-widest text-[#3d6651]">
                            {{ count($this->activeOrders) }} open
                        </span>
                    </div>

                    <div class="grid grid-cols-1 gap-4 md:gap-5">
                        @forelse($this->activeOrders as $order)
                            <article class="grid overflow-hidden border-[3px] border-[#012d1d] bg-white shadow-[3px_3px_0_0_#012d1d] md:grid-cols-[210px_1fr] md:shadow-[4px_4px_0_0_#012d1d]">
                                <div class="relative h-[118px] border-b-[3px] border-[#012d1d] bg-[#dfe8e2] md:h-auto md:min-h-[180px] md:border-b-0 md:border-r-[3px]">
                                    <img
                                        src="{{ $order['first_item_image'] ?: $fallbackImage }}"
                                        alt="{{ $order['first_item_name'] }}"
                                        class="h-full w-full object-cover grayscale md:min-h-[180px]"
                                        onerror="this.src='{{ $fallbackImage }}'"
                                    />
                                    <span class="absolute left-2 top-2 border border-[#012d1d] bg-[#D4EF70] px-2 py-1 font-label text-[9px] font-black uppercase tracking-wider md:left-3 md:top-3 md:text-[10px] md:tracking-widest">
                                        {{ $this->statusLabel($order['order_status']) }}
                                    </span>
                                </div>

                                <div class="flex flex-col justify-between p-3 md:p-6">
                                    <div class="grid gap-3 md:grid-cols-[1fr_auto] md:items-start md:gap-4">
                                        <div>
                                            <div class="mb-2 flex flex-wrap items-center gap-2 font-label text-[10px] font-black uppercase tracking-wider text-[#3d6651] md:mb-3 md:gap-3 md:text-[11px] md:tracking-widest">
                                                <span>Order #{{ $order['short_id'] }}</span>
                                                <span>{{ $formatDate($order['order_date']) }}, {{ $formatTime($order['order_date']) }}</span>
                                            </div>
                                            <h3 class="font-headline text-lg font-black uppercase leading-tight tracking-tighter md:text-2xl">
                                                {{ $order['first_item_name'] }}
                                            </h3>
                                            <p class="mt-1 font-body text-xs font-bold text-[#3d6651] md:mt-2 md:text-sm">
                                                {{ $order['item_count'] }} item{{ $order['item_count'] === 1 ? '' : 's' }}
                                                @if($order['city'])
                                                    shipped to {{ $order['city'] }}
                                                @endif
                                            </p>
                                        </div>

                                        <div class="md:text-right">
                                            <p class="font-label text-[10px] font-black uppercase tracking-widest text-[#3d6651] md:text-xs">Total</p>
                                            <p class="font-headline text-lg font-black tracking-tighter md:text-2xl">
                                                Rp {{ number_format($order['total_payment'], 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="mt-3 grid gap-2 border-[3px] border-[#012d1d] bg-[#f4fbf7] p-3 md:mt-5 md:grid-cols-[1fr_auto] md:items-center md:gap-3 md:p-4">
                                        <div class="flex items-center gap-2 md:gap-3">
                                            <span class="material-symbols-outlined text-[20px] md:text-[24px]">local_shipping</span>
                                            <div>
                                                <p class="font-label text-[10px] font-black uppercase tracking-wider md:text-[11px] md:tracking-widest">Package Status</p>
                                                <p class="font-body text-xs font-bold text-[#3d6651] md:text-sm">
                                                    {{ $order['tracking_id'] ? 'Tracking #' . $order['tracking_id'] : 'Tracking number not assigned yet' }}
                                                </p>
                                            </div>
                                        </div>

                                        <a
                                            href="/order/{{ $order['id'] }}"
                                            class="inline-flex items-center justify-center gap-2 border-[3px] border-[#012d1d] bg-[#012d1d] px-3 py-2.5 font-label text-[11px] font-black uppercase tracking-widest text-white transition-all hover:bg-[#D4EF70] hover:text-[#012d1d] md:px-4 md:py-3 md:text-xs"
                                        >
                                            <span class="material-symbols-outlined text-[18px]">route</span>
                                            Track Package
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @empty
                            <div class="border-[3px] border-[#012d1d] bg-white p-8 text-center shadow-[4px_4px_0_0_#012d1d]">
                                <p class="font-headline text-2xl font-black uppercase tracking-tighter">No Active Orders</p>
                                <p class="mt-2 font-body font-semibold text-[#3d6651]">Paid orders and deliveries will appear here.</p>
                            </div>
                        @endforelse
                    </div>

                    @if(count($orders) >= $limit)
                        <button
                            type="button"
                            wire:click="loadMore"
                            class="mt-5 w-full border-[3px] border-[#012d1d] bg-white px-5 py-3 font-label text-xs font-black uppercase tracking-widest shadow-[3px_3px_0_0_#012d1d] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:bg-[#D4EF70] hover:shadow-[5px_5px_0_0_#012d1d] md:py-4 md:shadow-[4px_4px_0_0_#012d1d]"
                        >
                            Load Older Orders
                        </button>
                    @endif
                </section>

                <section>
                    <div class="mb-4 flex items-center justify-between gap-4 md:mb-5">
                        <h2 class="flex items-center gap-2 font-headline text-xl font-black uppercase tracking-tighter md:gap-3 md:text-3xl">
                            <span class="block h-6 w-2 bg-[#012d1d] md:h-8"></span>
                            Historical Records
                        </h2>
                        <span class="font-label text-xs font-black uppercase tracking-widest text-[#3d6651]">
                            {{ count($this->historicalOrders) }} closed
                        </span>
                    </div>

                    <div class="flex flex-col gap-4">
                        @forelse($this->historicalOrders as $order)
                            <article class="grid gap-4 border-[3px] border-[#012d1d] bg-[#f4fbf7] p-4 shadow-[3px_3px_0_0_#012d1d] md:grid-cols-[56px_1fr_auto_auto] md:items-center md:p-5">
                                <div class="flex h-12 w-12 items-center justify-center border-[3px] border-[#012d1d] bg-white">
                                    <span class="material-symbols-outlined text-[22px]">
                                        {{ in_array($order['order_status'], ['CANCELLED', 'FAILED', 'EXPIRED'], true) ? 'close' : 'check' }}
                                    </span>
                                </div>

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="font-label text-sm font-black uppercase tracking-widest">
                                            #{{ $order['short_id'] }}
                                        </h3>
                                        <span class="border border-[#012d1d] px-2 py-1 font-label text-[10px] font-black uppercase tracking-wider {{ $this->statusTone($order['order_status']) }}">
                                            {{ $this->statusLabel($order['order_status']) }}
                                        </span>
                                    </div>
                                    <p class="mt-1 font-body text-sm font-semibold text-[#3d6651]">
                                        {{ $formatDate($order['order_date']) }} - {{ $order['item_count'] }} item{{ $order['item_count'] === 1 ? '' : 's' }}
                                    </p>
                                </div>

                                <p class="font-headline text-xl font-black tracking-tighter md:text-right">
                                    Rp {{ number_format($order['total_payment'], 0, ',', '.') }}
                                </p>

                                <a
                                    href="/order/{{ $order['id'] }}"
                                    class="inline-flex items-center justify-center gap-2 border-[3px] border-[#012d1d] bg-white px-4 py-3 font-label text-xs font-black uppercase tracking-widest shadow-[2px_2px_0_0_#012d1d] transition-all hover:bg-[#D4EF70]"
                                >
                                    <span class="material-symbols-outlined text-[18px]">receipt_long</span>
                                    Receipt
                                </a>
                            </article>
                        @empty
                            <div class="border-[3px] border-[#012d1d] bg-white p-6 font-label text-sm font-black uppercase tracking-widest text-[#3d6651]">
                                No completed or cancelled records yet.
                            </div>
                        @endforelse
                    </div>

                </section>
            </main>

            <aside class="border-[3px] border-[#012d1d] bg-[#012d1d] p-5 text-white shadow-[6px_6px_0_0_#012d1d] lg:sticky lg:top-28">
                <h2 class="border-b-[3px] border-[#D4EF70] pb-4 font-headline text-2xl font-black uppercase tracking-tighter">
                    Order Pulse
                </h2>
                <div class="mt-5 grid gap-4">
                    <div class="border-l-[6px] border-[#D4EF70] pl-4">
                        <p class="font-label text-[11px] font-black uppercase tracking-widest text-[#D4EF70]">Open Orders</p>
                        <p class="font-headline text-4xl font-black tracking-tighter">{{ count($this->activeOrders) }}</p>
                    </div>
                    <div class="border-l-[6px] border-white pl-4">
                        <p class="font-label text-[11px] font-black uppercase tracking-widest text-[#D4EF70]">Total Loaded</p>
                        <p class="font-headline text-4xl font-black tracking-tighter">{{ count($orders) }}</p>
                    </div>
                </div>

                <div class="mt-6 border-[3px] border-[#D4EF70] p-4">
                    <p class="font-label text-[11px] font-black uppercase tracking-widest text-[#D4EF70]">Next Step</p>
                    <p class="mt-2 font-body text-sm font-bold leading-relaxed">
                        Use Track Package on active orders to inspect delivery, payment, and address detail.
                    </p>
                </div>
            </aside>
        </div>
    </div>
</section>
