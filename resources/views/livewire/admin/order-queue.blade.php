@php
    $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->format('d M Y, H:i') : 'Date unavailable';
@endphp

<section
    x-data="{
        syncPerPage() {
            const target = window.innerWidth < 768 ? 5 : 10;
            this.$wire.setPerPage(target);
        }
    }"
    x-init="syncPerPage(); window.addEventListener('resize', () => syncPerPage())"
    class="min-h-screen px-4 py-6 md:px-8 md:py-10 text-[#012d1d]"
>
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
                <p class="font-headline text-4xl font-black tracking-tighter">{{ $totalOrders }}</p>
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
            <div class="px-4 py-4">Date Entered</div>
            <div class="px-4 py-4 text-right">Actions</div>
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
                                class="group inline-flex items-center gap-3 font-label text-sm font-black uppercase tracking-widest text-[#012d1d]"
                            >
                                <span
                                    wire:loading.remove
                                    wire:target="toggleDetails('{{ $order['id'] }}')"
                                    class="material-symbols-outlined grid h-7 w-7 place-items-center border-[2px] border-transparent text-[18px] transition-all group-hover:-translate-y-0.5 group-hover:border-[#012d1d] group-hover:bg-[#D4EF70] group-hover:shadow-[2px_2px_0_0_#012d1d]"
                                >
                                    {{ $expandedOrderId === $order['id'] ? 'expand_less' : 'expand_more' }}
                                </span>
                                <span
                                    wire:loading.grid
                                    wire:target="toggleDetails('{{ $order['id'] }}')"
                                    class="hidden h-7 w-7 place-items-center border-[2px] border-[#012d1d] bg-[#D4EF70] shadow-[2px_2px_0_0_#012d1d]"
                                >
                                    <span class="material-symbols-outlined animate-spin text-[18px]">sync</span>
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
                            <p class="lg:hidden mb-1 font-label text-[10px] font-black uppercase tracking-widest text-[#414844]">Date Entered</p>
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
                                            @if($option['action'] === 'status')
                                                wire:click="updateStatus('{{ $order['id'] }}', '{{ $option['status'] }}')"
                                            @elseif($option['action'] === 'pickup')
                                                wire:click="requestPickup('{{ $order['id'] }}')"
                                            @elseif($option['action'] === 'manual')
                                                wire:click="openManualShipmentModal('{{ $order['id'] }}')"
                                            @endif
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

                    <div
                        wire:loading.flex
                        wire:target="toggleDetails('{{ $order['id'] }}')"
                        class="hidden border-t-[3px] border-[#012d1d] bg-[#eef5f1] p-4"
                    >
                        <div class="flex w-full items-center gap-3 border-[3px] border-[#012d1d] bg-white p-4 shadow-[3px_3px_0_0_#012d1d]">
                            <span class="material-symbols-outlined animate-spin text-[22px]">sync</span>
                            <span class="font-label text-xs font-black uppercase tracking-widest text-[#012d1d]">Loading Item Detail</span>
                        </div>
                    </div>

                    @if($expandedOrderId === $order['id'])
                        <div
                            wire:loading.remove
                            wire:target="toggleDetails('{{ $order['id'] }}')"
                            class="border-t-[3px] border-[#012d1d] bg-[#eef5f1] p-4"
                        >
                            <div class="mb-3 flex items-center gap-2">
                                <span class="h-5 w-2 bg-[#012d1d]"></span>
                                <h3 class="font-headline text-lg font-black uppercase tracking-tighter">Item Detail</h3>
                            </div>

                            <div class="grid gap-3">
                                @forelse($this->itemsForOrder($order['id']) as $item)
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

        @if($totalOrders > 0)
            <div class="flex flex-col gap-4 border-t-[3px] border-[#012d1d] bg-[#eef5f1] p-4 md:flex-row md:items-center md:justify-between">
                <p class="font-label text-xs font-black uppercase tracking-widest text-[#414844]">
                    Showing {{ $this->pageStart() }}-{{ $this->pageEnd() }} of {{ $totalOrders }} orders
                </p>

                <div class="flex flex-wrap items-center gap-2">
                    <button
                        type="button"
                        wire:click="previousPage"
                        wire:loading.attr="disabled"
                        @disabled($page <= 1)
                        class="inline-flex h-10 min-w-10 items-center justify-center border-[3px] border-[#012d1d] bg-white px-3 font-label text-[11px] font-black uppercase tracking-widest text-[#012d1d] shadow-[2px_2px_0_0_#012d1d] transition-all hover:bg-[#D4EF70] disabled:cursor-not-allowed disabled:border-[#717973] disabled:bg-[#eef5f1] disabled:text-[#717973] disabled:shadow-none"
                        aria-label="Previous page"
                    >
                        <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                    </button>

                    @foreach($this->paginationPages() as $pageNumber)
                        <button
                            type="button"
                            wire:click="goToPage({{ $pageNumber }})"
                            wire:loading.attr="disabled"
                            class="inline-flex h-10 min-w-10 items-center justify-center border-[3px] border-[#012d1d] px-3 font-label text-[11px] font-black uppercase tracking-widest shadow-[2px_2px_0_0_#012d1d] transition-all hover:-translate-y-0.5 hover:bg-[#D4EF70] {{ $page === $pageNumber ? 'bg-[#012d1d] text-white' : 'bg-white text-[#012d1d]' }}"
                            aria-label="Page {{ $pageNumber }}"
                        >
                            {{ $pageNumber }}
                        </button>
                    @endforeach

                    <button
                        type="button"
                        wire:click="nextPage"
                        wire:loading.attr="disabled"
                        @disabled($page >= $this->totalPages())
                        class="inline-flex h-10 min-w-10 items-center justify-center border-[3px] border-[#012d1d] bg-white px-3 font-label text-[11px] font-black uppercase tracking-widest text-[#012d1d] shadow-[2px_2px_0_0_#012d1d] transition-all hover:bg-[#D4EF70] disabled:cursor-not-allowed disabled:border-[#717973] disabled:bg-[#eef5f1] disabled:text-[#717973] disabled:shadow-none"
                        aria-label="Next page"
                    >
                        <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                    </button>
                </div>
            </div>
        @endif
    </div>

    @if($showManualShipmentModal && $manualShipmentOrder)
        <div
            class="fixed inset-0 z-[80] flex items-center justify-center bg-[#012d1d]/40 px-4 py-6 backdrop-blur-sm"
            role="dialog"
            aria-modal="true"
            aria-labelledby="manual-shipment-title"
        >
            <div class="w-full max-w-[620px] border-[4px] border-[#012d1d] bg-[#f4fbf7] shadow-[8px_8px_0_0_#012d1d]">
                <div class="flex items-start justify-between gap-4 border-b-[3px] border-[#012d1d] bg-white p-5">
                    <div>
                        <p class="font-label text-[11px] font-black uppercase tracking-[0.24em] text-[#414844]">Manual Shipment</p>
                        <h2 id="manual-shipment-title" class="mt-1 font-headline text-2xl font-black uppercase tracking-tighter text-[#012d1d]">
                            #{{ $manualShipmentOrder['short_id'] }}
                        </h2>
                    </div>

                    <button
                        type="button"
                        wire:click="closeManualShipmentModal"
                        class="grid h-10 w-10 place-items-center border-[3px] border-[#012d1d] bg-white text-[#012d1d] shadow-[2px_2px_0_0_#012d1d] transition-all hover:bg-[#D4EF70]"
                        aria-label="Close manual shipment modal"
                    >
                        <span class="material-symbols-outlined text-[18px]">close</span>
                    </button>
                </div>

                <div class="grid gap-4 p-5">
                    <div class="grid gap-3 md:grid-cols-2">
                        <div class="border-[3px] border-[#012d1d] bg-white p-4">
                            <p class="font-label text-[10px] font-black uppercase tracking-widest text-[#414844]">Customer</p>
                            <p class="mt-1 font-headline text-lg font-black uppercase tracking-tight text-[#012d1d]">{{ $manualShipmentOrder['customer_name'] }}</p>
                            @if($manualShipmentOrder['customer_email'])
                                <p class="mt-1 break-all font-body text-xs font-bold text-[#414844]">{{ $manualShipmentOrder['customer_email'] }}</p>
                            @endif
                        </div>

                        <div class="border-[3px] border-[#012d1d] bg-white p-4">
                            <p class="font-label text-[10px] font-black uppercase tracking-widest text-[#414844]">Status</p>
                            <p class="mt-1 font-label text-xs font-black uppercase tracking-widest text-[#012d1d]">
                                {{ $this->statusLabel($manualShipmentOrder['order_status']) }}
                            </p>
                            <p class="mt-2 font-body text-xs font-bold text-[#414844]">
                                Biteship ID: {{ $manualShipmentOrder['biteship_order_id'] ?: 'Not assigned' }}
                            </p>
                        </div>
                    </div>

                    <div class="border-[3px] border-[#012d1d] bg-white p-4">
                        <p class="font-label text-[10px] font-black uppercase tracking-widest text-[#414844]">Destination</p>
                        <p class="mt-1 font-body text-sm font-bold leading-relaxed text-[#012d1d]">
                            {{ $manualShipmentOrder['recipient_name'] ?: $manualShipmentOrder['customer_name'] }}
                            @if($manualShipmentOrder['recipient_phone'])
                                / {{ $manualShipmentOrder['recipient_phone'] }}
                            @endif
                        </p>
                        <p class="mt-1 font-body text-sm font-semibold leading-relaxed text-[#414844]">
                            {{ $manualShipmentOrder['shipping_address'] ?: 'Address not available' }}
                            @if($manualShipmentOrder['city'] || $manualShipmentOrder['postal_code'])
                                <br>{{ $manualShipmentOrder['city'] }} {{ $manualShipmentOrder['postal_code'] }}
                            @endif
                        </p>
                    </div>

                    <form wire:submit.prevent="submitManualShipment" class="grid gap-3">
                        <label for="manual_tracking_id" class="font-label text-[11px] font-black uppercase tracking-widest text-[#012d1d]">
                            Tracking Number
                        </label>
                        <input
                            id="manual_tracking_id"
                            type="text"
                            wire:model.defer="manualTrackingId"
                            class="w-full border-[3px] border-[#012d1d] bg-white px-4 py-3 font-body text-sm font-bold text-[#012d1d] shadow-[3px_3px_0_0_#012d1d] outline-none transition-all focus:bg-[#D4EF70]"
                            placeholder="MANUAL-TRACKING-001"
                        >
                        @error('manualTrackingId')
                            <p class="font-label text-[11px] font-black uppercase tracking-widest text-[#ba1a1a]">{{ $message }}</p>
                        @enderror

                        <div class="mt-2 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                            <button
                                type="button"
                                wire:click="closeManualShipmentModal"
                                class="border-[3px] border-[#012d1d] bg-white px-4 py-3 font-label text-xs font-black uppercase tracking-widest text-[#012d1d] shadow-[3px_3px_0_0_#012d1d] transition-all hover:bg-[#eef5f1]"
                            >
                                Cancel
                            </button>
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="submitManualShipment"
                                class="inline-flex items-center justify-center gap-2 border-[3px] border-[#012d1d] bg-[#012d1d] px-4 py-3 font-label text-xs font-black uppercase tracking-widest text-white shadow-[3px_3px_0_0_#D4EF70] transition-all hover:bg-[#D4EF70] hover:text-[#012d1d] disabled:cursor-wait disabled:opacity-70"
                            >
                                <span wire:loading.remove wire:target="submitManualShipment">Save Manual Shipment</span>
                                <span wire:loading.inline-flex wire:target="submitManualShipment" class="hidden items-center gap-2">
                                    <span class="material-symbols-outlined animate-spin text-[18px]">sync</span>
                                    Processing...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</section>
