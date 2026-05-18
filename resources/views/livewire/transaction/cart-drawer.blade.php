<div x-data="{ isOpen: false }"
     x-on:open-cart.window="isOpen = true"
     x-on:keydown.escape.window="isOpen = false"
     class="relative z-50">
    <style>
        .qty-btn:hover:not(:disabled) {
            background-color: #d8e2dc;
        }
        .qty-btn:active:not(:disabled) {
            background-color: #bfc9c3;
        }
        .qty-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }
        .qty-input::-webkit-inner-spin-button,
        .qty-input::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .qty-input {
            -moz-appearance: textfield;
        }
    </style>

    <!-- Overlay Dimmer -->
    <div class="fixed inset-0 bg-[#000000]/40 z-40" 
         x-show="isOpen" 
         x-transition.opacity.duration.300ms
         @click="isOpen = false"
         style="display: none;"></div>

    <!-- Cart Sidebar -->
    <aside class="fixed top-0 right-0 h-full w-full max-w-[460px] bg-[#f4fbf7] border-l-[2px] border-[#012d1d] z-50 shadow-[-4px_0px_0px_0px_rgba(1,45,29,1)] flex flex-col"
           x-show="isOpen"
           x-transition:enter="transition transform ease-out duration-300"
           x-transition:enter-start="translate-x-full"
           x-transition:enter-end="translate-x-0"
           x-transition:leave="transition transform ease-in duration-300"
           x-transition:leave-start="translate-x-0"
           x-transition:leave-end="translate-x-full"
           style="display: none;">

        <!-- Sidebar Header -->
        <header class="flex justify-between items-center p-6 border-b-[3px] border-[#012d1d] bg-[#eef5f1]">
            <h2 class="font-label font-bold text-[16px] text-[#012d1d] uppercase tracking-widest">Your Basket</h2>
            <button @click="isOpen = false" aria-label="Close Cart" class="text-[#012d1d] hover:bg-[#d8e2dc] p-2 border-[3px] border-transparent hover:border-[#012d1d] transition-colors flex items-center justify-center">
                <span class="material-symbols-outlined" data-icon="close">close</span>
            </button>
        </header>

        <!-- Cart Items Scrollable Area -->
        <div class="flex-grow overflow-y-auto p-6 space-y-6">
            @if(count($items) > 0)
                <div>
                    @foreach($items as $item)
                        <!-- Cart Item -->
                        <div class="flex gap-4 border-[3px] border-[#012d1d] bg-[#ffffff] p-4 relative shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] mb-6">
                            <!-- Item Image -->
                            <div class="w-[72px] h-[72px] border-[3px] border-[#012d1d] bg-[#e8efec] shrink-0 overflow-hidden">
                                <img alt="{{ $item['cake_name_snapshot'] }}" class="w-full h-full object-cover" src="{{ $item['image_url_snapshot'] }}" />
                            </div>

                            <!-- Item Details -->
                            <div class="flex flex-col flex-grow justify-between">
                                <div class="flex justify-between items-start gap-2">
                                    <div>
                                        <h3 class="font-headline font-bold text-[14px] text-[#012d1d] mb-1">{{ $item['cake_name_snapshot'] }}</h3>
                                    </div>
                                    <button wire:click="removeItem('{{ $item['cart_item_id'] }}')" wire:loading.attr="disabled" class="text-[#57615c] hover:text-[#ba1a1a] disabled:opacity-40 disabled:cursor-not-allowed transition-colors mt-[-4px] mr-[-4px] p-1 relative">
                                        <span wire:loading.remove wire:target="removeItem('{{ $item['cart_item_id'] }}')" class="material-symbols-outlined text-[20px]" data-icon="delete">delete</span>
                                        <svg wire:loading wire:target="removeItem('{{ $item['cart_item_id'] }}')" class="animate-spin h-5 w-5 text-[#ba1a1a]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </button>
                                </div>

                                <div class="flex justify-between items-end mt-4">
                                    <!-- Quantity Control -->
                                    <div class="flex items-stretch border-[2px] border-[#012d1d] bg-[#f4fbf7] overflow-hidden" style="height:32px">
                                        <!-- Decrease button -->
                                        <button
                                            wire:click="decrementQty('{{ $item['cart_item_id'] }}')"
                                            wire:loading.attr="disabled"
                                            @disabled($item['quantity'] <= 1)
                                            class="qty-btn relative w-[32px] flex items-center justify-center text-[#012d1d] border-r-[2px] border-[#012d1d] select-none disabled:opacity-40 disabled:cursor-not-allowed"
                                            style="transition: background 80ms, transform 80ms, box-shadow 80ms;"
                                        >
                                            <span wire:loading.remove wire:target="decrementQty('{{ $item['cart_item_id'] }}')" class="material-symbols-outlined" style="font-size:15px; font-weight:700;">remove</span>
                                            <svg wire:loading wire:target="decrementQty('{{ $item['cart_item_id'] }}')" class="animate-spin h-4 w-4 text-[#012d1d]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </button>
                                        <!-- Quantity text -->
                                        <input type="number"
                                            value="{{ $item['quantity'] }}"
                                            min="1"
                                            max="99"
                                            oninput="if(this.value > 99) this.value = 99;"
                                            x-on:blur="if($event.target.value === '' || $event.target.value < 1) $event.target.value = 1; $wire.updateQty('{{ $item['cart_item_id'] }}', parseInt($event.target.value))"
                                            x-on:keydown.enter="$event.target.blur()"
                                            class="qty-input font-label font-bold text-[14px] min-w-[36px] w-[36px] flex items-center justify-center text-center text-[#012d1d] bg-transparent border-none outline-none focus:ring-0 p-0"
                                        >
                                        <!-- Increase button -->
                                        <button
                                            wire:click="incrementQty('{{ $item['cart_item_id'] }}')"
                                            wire:loading.attr="disabled"
                                            @disabled($item['quantity'] >= 99)
                                            class="qty-btn relative w-[32px] flex items-center justify-center text-[#012d1d] border-l-[2px] border-[#012d1d] select-none disabled:opacity-40 disabled:cursor-not-allowed"
                                            style="transition: background 80ms, transform 80ms, box-shadow 80ms;"
                                        >
                                            <span wire:loading.remove wire:target="incrementQty('{{ $item['cart_item_id'] }}')" class="material-symbols-outlined" style="font-size:15px; font-weight:700;">add</span>
                                            <svg wire:loading wire:target="incrementQty('{{ $item['cart_item_id'] }}')" class="animate-spin h-4 w-4 text-[#012d1d]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </button>
                                    </div>
                                    <span class="font-label font-bold text-[#012d1d] text-[15px]">{{ 'Rp ' . number_format($item['price_snapshot'] * $item['quantity'], 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="flex flex-col items-center justify-center h-full text-center space-y-4 py-12">
                    <span class="material-symbols-outlined text-[64px] text-[#012d1d]/30">shopping_cart</span>
                    <p class="font-body text-[#012d1d] font-bold">Your cart is currently empty</p>
                    <a href="/catalog" @click="isOpen = false" class="bg-[#012d1d] text-[#ffffff] font-label font-bold uppercase py-3 px-6 border-[3px] border-[#012d1d] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] hover:bg-[#1b4332] transition-all">
                        Start Shopping
                    </a>
                </div>
            @endif
        </div>

        <!-- Sidebar Footer / Checkout Actions -->
        @if(count($items) > 0)
            <footer class="border-t-[3px] border-[#012d1d] bg-[#f4fbf7] p-6 space-y-4">
                <!-- Order Summary -->
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between items-center">
                        <span class="font-label font-bold uppercase text-[#012d1d]">SUBTOTAL</span>
                        <span class="font-label font-bold text-[#012d1d]">{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="font-label font-bold uppercase text-[#012d1d]">TOTAL WEIGHT (G)</span>
                        <span class="font-body font-bold text-[14px] text-[#012d1d]">{{ number_format($totalWeight, 0, ',', '.') }}g</span>
                    </div>
                </div>

                <!-- Total -->
                <div class="flex justify-between items-end border-t-[2px] border-[#012d1d] pt-4 mb-6">
                    <span class="font-headline font-bold text-[18px] text-[#012d1d]">Total</span>
                    <span class="font-headline font-black text-[20px] text-[#012d1d]">{{ 'Rp ' . number_format($subtotal, 0, ',', '.') }}</span>
                </div>

                <!-- Checkout Button -->
                <a href="/checkout" class="w-full bg-[#012d1d] text-[#ffffff] font-label font-bold uppercase py-4 border-[3px] border-[#012d1d] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] hover:bg-[#1b4332] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_rgba(1,45,29,1)] transition-all flex justify-center items-center gap-2">
                    CHECKOUT NOW
                    <span class="material-symbols-outlined" data-icon="arrow_forward">arrow_forward</span>
                </a>
                <p class="text-center font-body text-[11px] text-[#57615c] mt-3">Shipping costs calculated at the next step</p>
            </footer>
        @endif
    </aside>
</div>
