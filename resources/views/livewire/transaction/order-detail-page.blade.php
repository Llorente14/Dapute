@php
    $createdAt = $order['order_date'] ?? $order['created_at'] ?? null;
    $subtotal = (int) ($order['subtotal_amount'] ?? 0);
    $shipping = (int) ($order['shipping_fee'] ?? 0);
    $total = (int) ($order['total_payment'] ?? 0);
    $adminFee = max(0, $total - $subtotal - $shipping);
@endphp

<section class="min-h-screen bg-[#f4fbf7] text-[#012d1d]" x-data="{ showCancelOrderModal: false }">
    <div class="mx-auto max-w-[1440px] px-4 md:px-8 py-10 md:py-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            <div class="lg:col-span-8 flex flex-col gap-8">
                <header class="bg-white border-[3px] border-[#012d1d] p-6 md:p-8 shadow-[8px_8px_0_0_#012d1d]">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
                        <div>
                            <p class="font-label font-bold text-xs uppercase tracking-[0.24em] text-[#3d6651] mb-3">
                                Order History Detail
                            </p>
                            <h1 class="font-headline font-black text-4xl md:text-6xl uppercase tracking-tighter leading-none">
                                Order #{{ substr($order['id'], 0, 8) }}
                            </h1>
                            <div x-data="{ copied: false }" class="mt-4 flex flex-col sm:flex-row sm:items-center gap-3">
                                <p class="font-body font-semibold text-sm md:text-base text-[#3d6651] break-all">
                                    {{ $order['id'] }}
                                </p>
                                <button
                                    type="button"
                                    title="Copy order ID"
                                    aria-label="Copy order ID"
                                    @click="navigator.clipboard.writeText('{{ $order['id'] }}').then(() => { copied = true; setTimeout(() => copied = false, 1600); })"
                                    class="shrink-0 inline-flex items-center justify-center w-10 h-10 border-[3px] border-[#012d1d] bg-[#D4EF70] text-[#012d1d] shadow-[4px_4px_0_0_#012d1d] hover:shadow-[6px_6px_0_0_#012d1d] hover:-translate-x-0.5 hover:-translate-y-0.5 active:translate-x-0 active:translate-y-0 transition-all">
                                    <span class="material-symbols-outlined text-[20px]" x-text="copied ? 'check' : 'content_copy'"></span>
                                </button>
                                <span x-show="copied" x-transition class="font-label font-black text-xs uppercase tracking-widest text-[#012d1d]" style="display: none;">
                                    Copied
                                </span>
                            </div>
                        </div>
                        <div class="flex flex-col items-start md:items-end gap-3">
                            <span class="inline-flex border-[3px] border-[#012d1d] px-4 py-2 font-label font-black text-xs uppercase tracking-widest {{ $this->statusTone }}">
                                {{ $this->statusLabel }}
                            </span>
                            <span class="font-label font-bold text-xs uppercase tracking-wider text-[#3d6651]">
                                {{ $createdAt ? \Carbon\Carbon::parse($createdAt)->format('d M Y, H:i') : 'Date unavailable' }}
                            </span>
                        </div>
                    </div>

                    @if($this->canManagePendingPayment)
                        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-3 border-t-[3px] border-[#012d1d] pt-6">
                            <button
                                type="button"
                                wire:click="payNow"
                                wire:loading.attr="disabled"
                                wire:target="payNow"
                                class="inline-flex items-center justify-center gap-2 border-[3px] border-[#012d1d] bg-[#D4EF70] px-5 py-4 font-label font-black text-xs uppercase tracking-widest text-[#012d1d] shadow-[4px_4px_0_0_#012d1d] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:shadow-[6px_6px_0_0_#012d1d] disabled:opacity-60">
                                <span class="material-symbols-outlined text-[20px]" wire:loading.remove wire:target="payNow">payments</span>
                                <span class="material-symbols-outlined text-[20px] animate-spin" wire:loading wire:target="payNow">sync</span>
                                <span wire:loading.remove wire:target="payNow">Pay Now</span>
                                <span wire:loading wire:target="payNow">Opening Payment</span>
                            </button>

                            <button
                                type="button"
                                @click="showCancelOrderModal = true"
                                wire:loading.attr="disabled"
                                wire:target="cancelOrder"
                                class="inline-flex items-center justify-center gap-2 border-[3px] border-[#012d1d] bg-white px-5 py-4 font-label font-black text-xs uppercase tracking-widest text-[#012d1d] shadow-[4px_4px_0_0_#012d1d] transition-all hover:-translate-x-0.5 hover:-translate-y-0.5 hover:bg-[#ffdad6] hover:shadow-[6px_6px_0_0_#012d1d] disabled:opacity-60">
                                <span class="material-symbols-outlined text-[20px]" wire:loading.remove wire:target="cancelOrder">cancel</span>
                                <span class="material-symbols-outlined text-[20px] animate-spin" wire:loading wire:target="cancelOrder">sync</span>
                                <span wire:loading.remove wire:target="cancelOrder">Cancel Order</span>
                                <span wire:loading wire:target="cancelOrder">Cancelling</span>
                            </button>
                        </div>

                        @error('order_action')
                            <div class="mt-5 border-[3px] border-[#ba1a1a] bg-[#ffdad6] p-4 font-label text-xs font-black uppercase tracking-widest text-[#93000a]">
                                {{ $message }}
                            </div>
                        @enderror
                    @endif
                </header>

                <section class="bg-white border-[3px] border-[#012d1d] p-6 md:p-8 shadow-[4px_4px_0_0_#012d1d]">
                    <div class="flex items-center justify-between gap-4 border-b-[3px] border-[#012d1d] pb-4 mb-6">
                        <h2 class="font-headline font-black text-2xl md:text-3xl uppercase tracking-tighter">
                            Ordered Items
                        </h2>
                        <span class="font-label font-bold text-xs uppercase tracking-widest text-[#3d6651]">
                            {{ count($items) }} item{{ count($items) === 1 ? '' : 's' }}
                        </span>
                    </div>

                    <div class="flex flex-col gap-5">
                        @forelse($items as $item)
                            <article class="grid grid-cols-[88px_1fr] md:grid-cols-[104px_1fr_auto] gap-4 md:gap-6 items-center border-[3px] border-[#012d1d] bg-[#f4fbf7] p-4 shadow-[4px_4px_0_0_#012d1d]">
                                <div class="w-[88px] h-[88px] md:w-[104px] md:h-[104px] border-[3px] border-[#012d1d] bg-[#dfe8e2] overflow-hidden">
                                    <img
                                        src="{{ $item['image_url'] ?: 'https://placehold.co/160x160/012d1d/D4EF70?text=DAPUTE' }}"
                                        alt="{{ $item['cake_name_snapshot'] }}"
                                        class="w-full h-full object-cover grayscale mix-blend-multiply"
                                        onerror="this.src='https://placehold.co/160x160/012d1d/D4EF70?text=DAPUTE'"
                                    />
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-headline font-black text-xl uppercase tracking-tight leading-tight">
                                        {{ $item['cake_name_snapshot'] }}
                                    </h3>
                                    <p class="font-label font-bold text-xs uppercase tracking-wider text-[#3d6651] mt-2">
                                        Qty {{ $item['quantity'] }} x Rp {{ number_format($item['price_snapshot'], 0, ',', '.') }}
                                    </p>
                                </div>
                                <div class="col-span-2 md:col-span-1 md:text-right border-t-[3px] md:border-t-0 md:border-l-[3px] border-[#012d1d] pt-3 md:pt-0 md:pl-6">
                                    <p class="font-label font-black text-lg uppercase whitespace-nowrap">
                                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </p>
                                </div>
                            </article>
                        @empty
                            <div class="border-[3px] border-[#012d1d] bg-[#f4fbf7] p-6 font-label font-bold uppercase">
                                No order items found.
                            </div>
                        @endforelse
                    </div>
                </section>

                <section class="bg-white border-[3px] border-[#012d1d] p-6 md:p-8 shadow-[4px_4px_0_0_#012d1d]">
                    <div class="flex flex-col gap-4 border-b-[3px] border-[#012d1d] pb-5 mb-6 md:flex-row md:items-end md:justify-between">
                        <div>
                            <p class="font-label font-black text-xs uppercase tracking-[0.24em] text-[#3d6651] mb-2">
                                Delivery Movement
                            </p>
                            <h2 class="font-headline font-black text-2xl md:text-3xl uppercase tracking-tighter">
                                Tracking Timeline
                            </h2>
                        </div>
                        <div class="border-[3px] border-[#012d1d] bg-[#D4EF70] px-4 py-3 font-label font-black text-xs uppercase tracking-widest">
                            {{ $order['tracking_id'] ?? 'Tracking Pending' }}
                        </div>
                    </div>

                    @if($this->currentTrackingEvent)
                        <div class="mb-6 border-[3px] border-[#012d1d] bg-[#D4EF70] p-4 shadow-[4px_4px_0_0_#012d1d]">
                            <div class="flex items-start gap-3">
                                <span class="material-symbols-outlined text-[28px]">
                                    {{ $this->trackingIcon($this->currentTrackingEvent['status']) }}
                                </span>
                                <div>
                                    <p class="font-label font-black text-xs uppercase tracking-widest text-[#3d6651]">
                                        Current Status
                                    </p>
                                    <h3 class="mt-1 font-headline text-2xl font-black uppercase tracking-tight">
                                        {{ $this->currentTrackingEvent['label'] }}
                                    </h3>
                                    <p class="mt-2 font-body text-sm font-bold leading-relaxed text-[#012d1d]">
                                        {{ $this->currentTrackingEvent['description'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="relative">
                        @forelse($this->displayTrackingEvents as $index => $tracking)
                            <article class="relative grid grid-cols-[44px_1fr] gap-4 pb-6 last:pb-0">
                                @if(!$loop->last)
                                    <span class="absolute left-[21px] top-11 h-[calc(100%-44px)] w-[3px] bg-[#012d1d]"></span>
                                @endif

                                <div class="relative z-10 flex h-11 w-11 items-center justify-center border-[3px] border-[#012d1d] {{ $index === 0 ? 'bg-[#D4EF70]' : 'bg-white' }}">
                                    <span class="material-symbols-outlined text-[22px]">
                                        {{ $this->trackingIcon($tracking['status']) }}
                                    </span>
                                </div>

                                <div class="border-[3px] border-[#012d1d] {{ $index === 0 ? 'bg-[#f4fbf7]' : 'bg-white' }} p-4">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                                        <h3 class="font-headline text-xl font-black uppercase tracking-tight">
                                            {{ $tracking['label'] }}
                                        </h3>
                                        <time class="font-label text-[11px] font-black uppercase tracking-widest text-[#3d6651]">
                                            {{ $tracking['timestamp'] ? \Carbon\Carbon::parse($tracking['timestamp'])->format('d M Y, H:i') : 'Time unavailable' }}
                                        </time>
                                    </div>
                                    <p class="mt-2 font-body text-sm font-semibold leading-relaxed text-[#3d6651]">
                                        {{ $tracking['description'] }}
                                    </p>
                                </div>
                            </article>
                        @empty
                            <div class="border-[3px] border-[#012d1d] bg-[#f4fbf7] p-6 text-center">
                                <span class="material-symbols-outlined mx-auto text-[36px]">inventory_2</span>
                                <p class="mt-3 font-headline text-2xl font-black uppercase tracking-tight">
                                    No Delivery Movement Yet
                                </p>
                                <p class="mt-2 font-body text-sm font-semibold text-[#3d6651]">
                                    Courier has not picked up package yet. Current order status: {{ $this->statusLabel }}.
                                </p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <aside class="lg:col-span-4 flex flex-col gap-6 lg:sticky lg:top-28">
                <section class="bg-white border-[3px] border-[#012d1d] p-6 shadow-[6px_6px_0_0_#012d1d]">
                    <h2 class="font-headline font-black text-2xl uppercase tracking-tighter border-b-[3px] border-[#012d1d] pb-4 mb-5">
                        Payment Summary
                    </h2>
                    <div class="flex flex-col gap-3 font-label font-bold text-sm uppercase">
                        <div class="flex justify-between gap-4">
                            <span>Subtotal</span>
                            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between gap-4">
                            <span>Shipping</span>
                            <span>Rp {{ number_format($shipping, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between gap-4 text-[#3d6651]">
                            <span>Admin Fee</span>
                            <span>Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    <div class="mt-5 pt-5 border-t-[3px] border-[#012d1d] flex items-end justify-between gap-4">
                        <span class="font-headline font-black text-xl uppercase">Total</span>
                        <span class="font-label font-black text-2xl text-right">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </section>

                <section class="bg-white border-[3px] border-[#012d1d] p-6 shadow-[4px_4px_0_0_#012d1d]">
                    <h2 class="font-headline font-black text-2xl uppercase tracking-tighter border-b-[3px] border-[#012d1d] pb-4 mb-5">
                        Delivery Address
                    </h2>
                    @if($address)
                        <div class="font-body font-bold text-sm leading-relaxed">
                            <p class="font-headline font-black text-lg uppercase">{{ $address['recipient_name'] }}</p>
                            <p class="text-[#3d6651]">{{ $address['recipient_phone'] }}</p>
                            <p class="mt-4">{{ $address['shipping_address'] }}</p>
                            <p>{{ $address['city'] }}, {{ $address['postal_code'] }}</p>
                        </div>
                    @else
                        <p class="font-label font-bold text-sm uppercase text-[#3d6651]">Address snapshot unavailable.</p>
                    @endif
                </section>

                <section class="bg-[#D4EF70] border-[3px] border-[#012d1d] p-6 shadow-[4px_4px_0_0_#012d1d]">
                    <h2 class="font-headline font-black text-2xl uppercase tracking-tighter border-b-[3px] border-[#012d1d] pb-4 mb-5">
                        Shipment
                    </h2>
                    <dl class="grid grid-cols-1 gap-3 font-label font-bold text-sm uppercase">
                        <div class="flex justify-between gap-4">
                            <dt>Status</dt>
                            <dd class="text-right">{{ $this->statusLabel }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt>Tracking</dt>
                            <dd class="text-right break-all">{{ $order['tracking_id'] ?? 'Not available' }}</dd>
                        </div>
                        <div class="flex justify-between gap-4">
                            <dt>Latest</dt>
                            <dd class="text-right">{{ $this->currentTrackingEvent['label'] ?? $this->statusLabel }}</dd>
                        </div>
                    </dl>
                    @if(!empty($order['notes']))
                        <div class="mt-5 pt-5 border-t-[3px] border-[#012d1d]">
                            <p class="font-label font-black text-xs uppercase tracking-widest mb-2">Order Notes</p>
                            <p class="font-body font-semibold text-sm leading-relaxed">{{ $order['notes'] }}</p>
                        </div>
                    @endif

                    @if($this->canManagePendingPayment)
                        <div class="mt-5 pt-5 border-t-[3px] border-[#012d1d]">
                            <p class="font-label font-black text-xs uppercase tracking-widest mb-2">Payment Required</p>
                            <p class="font-body font-semibold text-sm leading-relaxed">
                                This order is saved but not paid yet. Pay now to continue processing, or cancel it from this page.
                            </p>
                        </div>
                    @endif
                </section>
            </aside>
        </div>
    </div>

    @if($this->canManagePendingPayment)
        <div
            x-show="showCancelOrderModal"
            x-cloak
            @keydown.escape.window="showCancelOrderModal = false"
            class="fixed inset-0 z-[100] flex items-center justify-center bg-[#012d1d]/60 p-4"
            style="display: none;"
        >
            <div class="absolute inset-0" @click="showCancelOrderModal = false"></div>
            <section
                x-show="showCancelOrderModal"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-3"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-3"
                class="relative w-full max-w-[480px] border-[3px] border-[#012d1d] bg-white p-6 shadow-[8px_8px_0_0_#012d1d]"
            >
                <div class="mb-5 flex items-start justify-between gap-4 border-b-[3px] border-[#012d1d] pb-4">
                    <div>
                        <p class="font-label text-[11px] font-black uppercase tracking-[0.24em] text-[#93000a]">
                            Pending Payment
                        </p>
                        <h2 class="mt-2 font-headline text-3xl font-black uppercase leading-none tracking-tighter text-[#012d1d]">
                            Cancel Order?
                        </h2>
                    </div>
                    <button
                        type="button"
                        @click="showCancelOrderModal = false"
                        class="flex h-10 w-10 shrink-0 items-center justify-center border-[3px] border-[#012d1d] bg-[#f4fbf7] shadow-[2px_2px_0_0_#012d1d] hover:bg-[#D4EF70]"
                        aria-label="Close cancel order modal"
                    >
                        <span class="material-symbols-outlined text-[20px]">close</span>
                    </button>
                </div>

                <p class="font-body text-sm font-bold leading-relaxed text-[#3d6651]">
                    This order has not been paid. Cancelling will mark this order as cancelled and stop payment continuation from this page.
                </p>

                <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-2">
                    <button
                        type="button"
                        @click="showCancelOrderModal = false"
                        class="border-[3px] border-[#012d1d] bg-[#f4fbf7] px-4 py-3 font-label text-xs font-black uppercase tracking-widest text-[#012d1d] shadow-[3px_3px_0_0_#012d1d] hover:bg-[#D4EF70]"
                    >
                        Keep Order
                    </button>
                    <button
                        type="button"
                        wire:click="cancelOrder"
                        @click="showCancelOrderModal = false"
                        wire:loading.attr="disabled"
                        wire:target="cancelOrder"
                        class="inline-flex items-center justify-center gap-2 border-[3px] border-[#ba1a1a] bg-[#ba1a1a] px-4 py-3 font-label text-xs font-black uppercase tracking-widest text-white shadow-[3px_3px_0_0_#012d1d] hover:bg-[#93000a] disabled:opacity-60"
                    >
                        <span class="material-symbols-outlined text-[18px]" wire:loading.remove wire:target="cancelOrder">delete_forever</span>
                        <span class="material-symbols-outlined text-[18px] animate-spin" wire:loading wire:target="cancelOrder">sync</span>
                        <span wire:loading.remove wire:target="cancelOrder">Yes, Cancel</span>
                        <span wire:loading wire:target="cancelOrder">Cancelling</span>
                    </button>
                </div>
            </section>
        </div>
    @endif
</section>
