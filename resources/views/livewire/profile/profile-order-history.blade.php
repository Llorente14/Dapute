<section class="bg-surface-container-highest neo-border p-6 md:p-8 h-full flex flex-col" style="box-shadow: 4px 4px 0 0 #012d1d;">
    <div class="flex items-center gap-4 mb-6 border-b-[3px] border-primary pb-4">
        <span class="material-symbols-outlined text-2xl text-primary">receipt_long</span>
        <h2 class="font-headline text-2xl font-bold tracking-tight text-primary uppercase">Recent Orders</h2>
    </div>

    <div class="mb-6 flex flex-wrap gap-2">
        @foreach($this->filters() as $status => $label)
            <button
                type="button"
                wire:click="setFilter('{{ $status }}')"
                wire:loading.attr="disabled"
                wire:target="setFilter"
                class="border-[3px] border-primary px-3 py-2 font-label text-[10px] font-black uppercase tracking-widest transition-all hover:-translate-y-0.5 disabled:opacity-60 {{ $filterStatus === $status ? 'bg-primary text-on-primary' : 'bg-surface-container-lowest text-primary hover:bg-[#D4EF70]' }}"
                style="box-shadow: 2px 2px 0 0 #012d1d;"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div wire:loading.flex wire:target="setFilter" class="hidden mb-4 items-center gap-2 border-[3px] border-primary bg-surface-container-lowest px-4 py-3 font-label text-xs font-black uppercase tracking-widest text-primary">
        <span class="material-symbols-outlined animate-spin text-[18px]">sync</span>
        Loading Orders
    </div>

    <div class="flex flex-col gap-4 flex-1">
        @forelse($orders as $order)
            <article
                wire:key="profile-order-{{ $order['id'] }}"
                x-data="{ open: false }"
                class="bg-surface-container-lowest border-[3px] border-primary p-4"
                style="box-shadow: 4px 4px 0 0 #012d1d;"
            >
                <button type="button" x-on:click="open = !open" class="w-full text-left">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <p class="font-label text-[10px] font-black uppercase tracking-widest text-on-surface-variant">
                                Order #{{ strtoupper(substr((string) $order['id'], 0, 8)) }}
                            </p>
                            <h3 class="mt-1 font-headline text-xl font-black uppercase tracking-tight text-primary">
                                Rp {{ number_format($order['total_payment'], 0, ',', '.') }}
                            </h3>
                            <p class="mt-1 font-body text-xs font-bold text-on-surface-variant">
                                {{ $order['order_date'] ? \Carbon\Carbon::parse($order['order_date'])->format('d M Y, H:i') : 'Date unavailable' }}
                            </p>
                        </div>
                        <div class="flex shrink-0 flex-col items-end gap-2">
                            <span class="border-[2px] border-primary px-2 py-1 font-label text-[10px] font-black uppercase tracking-widest {{ $this->statusBadgeClass($order['order_status']) }}">
                                {{ $this->statusLabel($order['order_status']) }}
                            </span>
                            <span class="material-symbols-outlined text-primary" x-text="open ? 'expand_less' : 'expand_more'"></span>
                        </div>
                    </div>
                </button>

                <div x-show="open" x-collapse class="mt-4 border-t-[3px] border-primary pt-4" style="display: none;">
                    <div class="grid gap-3">
                        @forelse($order['items'] as $item)
                            <div class="grid grid-cols-[1fr_auto] gap-3 border-[3px] border-primary bg-[#f4fbf7] p-3">
                                <div>
                                    <p class="font-headline text-sm font-black uppercase tracking-tight text-primary">
                                        {{ $item['cake_name_snapshot'] }}
                                    </p>
                                    <p class="mt-1 font-label text-[10px] font-black uppercase tracking-widest text-on-surface-variant">
                                        Qty {{ $item['quantity'] }} x Rp {{ number_format($item['price_snapshot'], 0, ',', '.') }}
                                    </p>
                                </div>
                                <p class="font-label text-xs font-black uppercase text-primary">
                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                </p>
                            </div>
                        @empty
                            <div class="border-[3px] border-primary bg-[#f4fbf7] p-3 font-label text-xs font-black uppercase tracking-widest text-on-surface-variant">
                                Item snapshot unavailable.
                            </div>
                        @endforelse
                    </div>

                    @if(!empty($order['notes']))
                        <div class="mt-4 border-[3px] border-primary bg-[#D4EF70] p-3">
                            <p class="font-label text-[10px] font-black uppercase tracking-widest text-primary">Notes</p>
                            <p class="mt-1 font-body text-sm font-bold text-primary">{{ $order['notes'] }}</p>
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <div class="bg-surface-container-lowest border-[3px] border-primary p-6 text-center" style="box-shadow: 4px 4px 0 0 #012d1d;">
                <span class="material-symbols-outlined mx-auto text-4xl text-primary">receipt_long</span>
                <p class="mt-3 font-headline text-2xl font-black uppercase tracking-tight text-primary">No orders yet</p>
                <p class="mt-2 font-body text-sm font-semibold text-on-surface-variant">Your orders will appear here.</p>
            </div>
        @endforelse
    </div>
</section>
