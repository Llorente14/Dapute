@php
    $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d M Y, H:i') : 'Date unavailable';
@endphp

<section class="min-h-screen px-4 py-6 md:px-8 md:py-10 text-[#012d1d]">
    <header class="mb-8 border-b-[4px] border-[#012d1d] pb-6">
        <div class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="mb-3 font-label text-xs font-black uppercase tracking-[0.28em] text-[#414844]">
                    Kitchen Command Center
                </p>
                <h1 class="font-headline text-4xl font-black uppercase leading-[0.9] tracking-tighter md:text-6xl">
                    Order Queue
                </h1>
                <p class="mt-4 max-w-[720px] font-body text-sm font-semibold leading-relaxed text-[#414844] md:text-base">
                    Active customer orders for payment review, kitchen processing, pickup preparation, and delivery monitoring.
                </p>
            </div>

            <div class="border-[3px] border-[#012d1d] bg-[#D4EF70] px-5 py-4 shadow-[4px_4px_0_0_#012d1d]">
                <p class="font-label text-[11px] font-black uppercase tracking-widest text-[#012d1d]">Active Orders</p>
                <p class="font-headline text-4xl font-black tracking-tighter">{{ count($orders) }}</p>
            </div>
        </div>
    </header>

    <div class="mb-6 flex flex-wrap gap-3">
        @foreach($this->filterTabs as $status => $label)
            <button
                type="button"
                wire:click="filterBy('{{ $status }}')"
                class="border-[3px] border-[#012d1d] px-4 py-3 font-label text-xs font-black uppercase tracking-widest shadow-[3px_3px_0_0_#012d1d] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 {{ $statusFilter === $status ? 'bg-[#012d1d] text-white' : 'bg-white text-[#012d1d] hover:bg-[#D4EF70]' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="overflow-x-auto overflow-y-visible border-[3px] border-[#012d1d] bg-white shadow-[6px_6px_0_0_#012d1d]">
        <div class="hidden grid-cols-[1.1fr_1.4fr_1fr_1.1fr_1.2fr_1.2fr] border-b-[3px] border-[#012d1d] bg-[#012d1d] font-label text-[11px] font-black uppercase tracking-widest text-white lg:grid">
            <div class="px-4 py-4">No. Order</div>
            <div class="px-4 py-4">Customer</div>
            <div class="px-4 py-4 text-right">Total</div>
            <div class="px-4 py-4">Status</div>
            <div class="px-4 py-4">Tanggal Masuk</div>
            <div class="px-4 py-4 text-right">Aksi</div>
        </div>

        <div class="divide-y-[3px] divide-[#012d1d]">
            @forelse($orders as $order)
                <article wire:key="order-row-{{ $order['id'] }}" class="bg-white">
                    <div class="grid gap-4 p-4 lg:grid-cols-[1.1fr_1.4fr_1fr_1.1fr_1.2fr_1.2fr] lg:items-center">
                        <div>
                            <p class="lg:hidden mb-1 font-label text-[10px] font-black uppercase tracking-widest text-[#414844]">No. Order</p>
                            <button
                                type="button"
                                wire:click="toggleDetails('{{ $order['id'] }}')"
                                class="inline-flex items-center gap-2 font-label text-sm font-black uppercase tracking-widest text-[#012d1d] hover:bg-[#D4EF70]"
                            >
                                <span class="material-symbols-outlined text-[18px]">
                                    {{ $expandedOrderId === $order['id'] ? 'expand_less' : 'expand_more' }}
                                </span>
                                #{{ $order['short_id'] }}
                            </button>
                        </div>

                        <div class="min-w-0">
                            <p class="lg:hidden mb-1 font-label text-[10px] font-black uppercase tracking-widest text-[#414844]">Customer</p>
                            <p class="truncate font-headline text-base font-black uppercase tracking-tight">{{ $order['customer_name'] }}</p>
                            @if($order['customer_email'])
                                <p class="truncate font-body text-xs font-bold text-[#414844]">{{ $order['customer_email'] }}</p>
                            @endif
                        </div>

                        <div class="lg:text-right">
                            <p class="lg:hidden mb-1 font-label text-[10px] font-black uppercase tracking-widest text-[#414844]">Total</p>
                            <p class="font-headline text-xl font-black tracking-tighter">Rp {{ number_format($order['total_payment'], 0, ',', '.') }}</p>
                        </div>

                        <div>
                            <p class="lg:hidden mb-1 font-label text-[10px] font-black uppercase tracking-widest text-[#414844]">Status</p>
                            <span class="inline-flex border-[2px] border-[#012d1d] px-3 py-2 font-label text-[10px] font-black uppercase tracking-widest {{ $this->statusBadgeClass($order['order_status']) }}">
                                {{ $this->statusLabel($order['order_status']) }}
                            </span>
                        </div>

                        <div>
                            <p class="lg:hidden mb-1 font-label text-[10px] font-black uppercase tracking-widest text-[#414844]">Tanggal Masuk</p>
                            <p class="font-body text-sm font-bold text-[#414844]">{{ $formatDate($order['order_date']) }}</p>
                        </div>

                        <div class="relative overflow-visible lg:justify-self-end" x-data="{ open: false }">
                            <button
                                type="button"
                                @click="open = !open"
                                @click.outside="open = false"
                                class="inline-flex w-full min-w-[168px] items-center justify-between gap-3 border-[3px] border-[#012d1d] bg-white px-3 py-2 font-label text-[11px] font-black uppercase tracking-widest text-[#012d1d] shadow-[2px_2px_0_0_#012d1d] transition-all hover:bg-[#D4EF70] lg:w-auto"
                            >
                                <span>Actions</span>
                                <span class="material-symbols-outlined text-[18px]" x-text="open ? 'expand_less' : 'expand_more'"></span>
                            </button>

                            <div
                                x-show="open"
                                x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 -translate-y-1"
                                class="absolute right-0 top-full z-30 mt-2 w-[230px] border-[3px] border-[#012d1d] bg-white shadow-[4px_4px_0_0_#012d1d]"
                                style="display: none;"
                            >
                                @foreach($this->actionOptions($order['order_status']) as $option)
                                    @if($option['available'])
                                        <button
                                            type="button"
                                            wire:click="{{ $option['method'] }}('{{ $order['id'] }}')"
                                            wire:loading.attr="disabled"
                                            @click="open = false"
                                            class="flex w-full items-center justify-between gap-3 border-b-[2px] border-[#012d1d] px-3 py-3 text-left font-label text-[11px] font-black uppercase tracking-widest text-[#012d1d] hover:bg-[#D4EF70] disabled:opacity-60 last:border-b-0"
                                        >
                                            <span class="inline-flex items-center gap-2">
                                                <span class="material-symbols-outlined text-[17px]">{{ $option['icon'] }}</span>
                                                {{ $option['label'] }}
                                            </span>
                                            <span class="border border-[#012d1d] bg-[#D4EF70] px-1.5 py-0.5 text-[9px]">Available</span>
                                        </button>
                                    @else
                                        <button
                                            type="button"
                                            disabled
                                            class="flex w-full cursor-not-allowed items-center justify-between gap-3 border-b-[2px] border-[#012d1d] bg-[#eef5f1] px-3 py-3 text-left font-label text-[11px] font-black uppercase tracking-widest text-[#717973] last:border-b-0"
                                        >
                                            <span class="inline-flex items-center gap-2">
                                                <span class="material-symbols-outlined text-[17px]">{{ $option['icon'] }}</span>
                                                {{ $option['label'] }}
                                            </span>
                                            <span class="border border-[#717973] px-1.5 py-0.5 text-[9px]">Unavailable</span>
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @if($expandedOrderId === $order['id'])
                        <div class="border-t-[3px] border-[#012d1d] bg-[#eef5f1] p-4">
                            <div class="mb-3 flex items-center gap-2">
                                <span class="h-5 w-2 bg-[#012d1d]"></span>
                                <h3 class="font-headline text-lg font-black uppercase tracking-tighter">Item Detail</h3>
                            </div>

                            <div class="grid gap-3">
                                @forelse($order['items'] as $item)
                                    <div class="grid gap-2 border-[3px] border-[#012d1d] bg-white p-3 shadow-[2px_2px_0_0_#012d1d] md:grid-cols-[1fr_80px_140px] md:items-center">
                                        <p class="font-headline text-sm font-black uppercase tracking-tight">{{ $item['cake_name_snapshot'] }}</p>
                                        <p class="font-label text-xs font-black uppercase tracking-widest text-[#414844]">Qty {{ $item['quantity'] }}</p>
                                        <p class="font-label text-sm font-black uppercase md:text-right">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</p>
                                    </div>
                                @empty
                                    <div class="border-[3px] border-[#012d1d] bg-white p-4 font-label text-xs font-black uppercase tracking-widest text-[#414844]">
                                        No item snapshot found.
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endif
                </article>
            @empty
                <div class="p-10 text-center">
                    <p class="font-headline text-3xl font-black uppercase tracking-tighter">No Active Orders</p>
                    <p class="mt-2 font-body text-sm font-bold text-[#414844]">Kitchen queue is empty for this filter.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
