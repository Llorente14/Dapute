<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-start w-full">
    <!-- Left Column: Forms -->
    <div class="lg:col-span-7 flex flex-col gap-12 md:gap-16 relative z-50">
        <!-- Address Input Section -->
        <section class="flex flex-col gap-6">
            <header class="flex items-baseline justify-between mb-2">
                <h1 class="font-headline font-bold text-3xl md:text-4xl uppercase tracking-tight text-primary">Shipping details</h1>
            </header>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="sr-only" for="recipient_name">Recipient Name</label>
                    <input wire:model="recipient_name" class="w-full bg-surface-container-lowest border-[3px] border-primary p-4 font-label text-sm font-bold uppercase text-primary placeholder-primary/40 focus:bg-surface-container-lowest focus:outline-none focus:ring-0 transition-all duration-200 hover:-translate-y-0.5 focus:-translate-y-0.5 focus:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] hover:shadow-[2px_2px_0px_0px_rgba(1,45,29,1)]" id="recipient_name" placeholder="RECIPIENT NAME" type="text"/>
                </div>
                <div>
                    <label class="sr-only" for="recipient_phone">Phone Number</label>
                    <input wire:model="recipient_phone" class="w-full bg-surface-container-lowest border-[3px] border-primary p-4 font-label text-sm font-bold uppercase text-primary placeholder-primary/40 focus:bg-surface-container-lowest focus:outline-none focus:ring-0 transition-all duration-200 hover:-translate-y-0.5 focus:-translate-y-0.5 focus:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] hover:shadow-[2px_2px_0px_0px_rgba(1,45,29,1)]" id="recipient_phone" placeholder="PHONE NUMBER" type="tel"/>
                </div>
                <div class="md:col-span-2 relative group z-50" x-data="{ open: @entangle('showAddressDropdown') }" @click.outside="open = false">
                    <label class="sr-only" for="address">Street Address</label>
                    <div class="relative w-full z-40">
                        <input wire:model.live.debounce.300ms="address" x-on:focus="open = true" autocomplete="off" class="w-full bg-surface-container-lowest border-[3px] border-primary p-4 pr-24 font-label text-sm font-bold uppercase text-primary placeholder-primary/40 focus:bg-surface-container-lowest focus:outline-none focus:ring-0 transition-all duration-200 hover:-translate-y-0.5 focus:-translate-y-0.5 focus:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] hover:shadow-[2px_2px_0px_0px_rgba(1,45,29,1)]" id="address" placeholder="STREET ADDRESS" type="text"/>
                        
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-primary transition-transform duration-200 pointer-events-none" :class="open ? 'rotate-180' : ''">expand_more</span>
                            <div class="h-6 w-[2px] bg-primary/20"></div>
                            <a href="/profile" class="flex items-center justify-center w-8 h-8 bg-primary text-surface hover:bg-surface hover:text-primary border-[2px] border-primary transition-all duration-150 hover:-translate-y-0.5 hover:shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] cursor-pointer" title="Manage Address in Profile">
                                <span class="material-symbols-outlined text-xl">add</span>
                            </a>
                        </div>
                        
                        <div x-show="open" style="display: none;" class="absolute top-full left-0 w-full mt-2 bg-surface border-[3px] border-primary shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] z-50 flex flex-col max-h-60 overflow-y-auto custom-scrollbar">
                            @foreach($filteredAddresses as $addr)
                            <button type="button" wire:click="selectAddress('{{ $addr }}')" class="w-full text-left p-4 border-b-[3px] border-primary hover:bg-primary hover:text-surface font-label font-bold text-sm text-primary transition-colors duration-150 focus:bg-primary focus:text-surface focus:outline-none cursor-pointer last:border-b-0">
                                {{ $addr }}
                            </button>
                            @endforeach
                            
                            @if(count($filteredAddresses) === 0 && strlen($address) > 0)
                            <div class="p-4 font-label text-sm text-primary/60 font-semibold uppercase">
                                No suggestions found.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div>
                    <label class="sr-only" for="city">City</label>
                    <input wire:model="city" class="w-full bg-surface-container-lowest border-[3px] border-primary p-4 font-label text-sm font-bold uppercase text-primary placeholder-primary/40 focus:bg-surface-container-lowest focus:outline-none focus:ring-0 transition-all duration-200 hover:-translate-y-0.5 focus:-translate-y-0.5 focus:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] hover:shadow-[2px_2px_0px_0px_rgba(1,45,29,1)]" id="city" placeholder="CITY" type="text"/>
                </div>
                <div class="relative w-full z-40">
                    <label class="sr-only" for="state">State</label>
                    <div class="relative w-full" x-data="{ open: false }" @click.outside="open = false">
                        <input wire:model.live.debounce.300ms="state" x-on:focus="open = true" autocomplete="off" class="w-full bg-surface-container-lowest border-[3px] border-primary p-4 pr-12 font-label text-sm font-bold uppercase text-primary placeholder-primary/40 focus:bg-surface-container-lowest focus:outline-none focus:ring-0 transition-all duration-200 hover:-translate-y-0.5 focus:-translate-y-0.5 focus:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] hover:shadow-[2px_2px_0px_0px_rgba(1,45,29,1)]" id="state" placeholder="STATE / PROVINCE" type="text"/>
                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-primary transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                        
                        <div x-show="open" style="display: none;" class="absolute top-full left-0 w-full mt-2 bg-surface border-[3px] border-primary shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] z-50 flex flex-col max-h-60 overflow-y-auto custom-scrollbar">
                            @foreach($this->filteredProvinces as $prov)
                            <button type="button" @click="$wire.set('state', '{{ $prov }}'); open = false;" class="w-full text-left p-4 border-b-[3px] border-primary hover:bg-primary hover:text-surface font-label font-bold text-sm text-primary transition-colors duration-150 focus:bg-primary focus:text-surface focus:outline-none cursor-pointer last:border-b-0">
                                {{ $prov }}
                            </button>
                            @endforeach
                            @if(count($this->filteredProvinces) === 0 && strlen($state) > 0)
                            <div class="p-4 font-label text-sm text-primary/60 font-semibold uppercase">
                                No suggestions found.
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="md:col-span-2">
                    <label class="sr-only" for="postal_code">Postal Code</label>
                    <input wire:model="postal_code" class="w-full bg-surface-container-lowest border-[3px] border-primary p-4 font-label text-sm font-bold uppercase text-primary placeholder-primary/40 focus:bg-surface-container-lowest focus:outline-none focus:ring-0 transition-all duration-200 hover:-translate-y-0.5 focus:-translate-y-0.5 focus:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] hover:shadow-[2px_2px_0px_0px_rgba(1,45,29,1)]" id="postal_code" placeholder="POSTAL CODE" type="text"/>
                </div>
            </div>
        </section>

        <!-- Delivery Method Options -->
        <section class="flex flex-col gap-6" wire:init="fetchCouriers">
            <header class="flex items-baseline justify-between mb-2">
                <h2 class="font-headline font-bold text-2xl md:text-3xl uppercase tracking-tight text-primary">Delivery Method</h2>
            </header>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 relative">
                
                <!-- Loading Skeleton -->
                <div wire:loading wire:target="fetchCouriers" class="absolute inset-0 bg-surface/50 backdrop-blur-sm z-10 flex items-center justify-center">
                    <div class="flex gap-2">
                        <div class="w-4 h-4 bg-primary animate-[bounce_1s_infinite] rounded-none"></div>
                        <div class="w-4 h-4 bg-primary animate-[bounce_1s_infinite_100ms] rounded-none"></div>
                        <div class="w-4 h-4 bg-primary animate-[bounce_1s_infinite_200ms] rounded-none"></div>
                    </div>
                </div>

                @if(empty($couriers))
                    <!-- Placeholder skeleton structure if empty initially -->
                    <div wire:loading class="w-full bg-surface-container-lowest border-[3px] border-primary p-6 animate-pulse">
                        <div class="h-8 bg-surface-variant w-1/3 mb-4"></div>
                        <div class="h-6 bg-surface-variant w-1/2"></div>
                    </div>
                    <div wire:loading class="w-full bg-surface-container-lowest border-[3px] border-primary p-6 animate-pulse">
                        <div class="h-8 bg-surface-variant w-1/3 mb-4"></div>
                        <div class="h-6 bg-surface-variant w-1/2"></div>
                    </div>
                @endif

                @foreach($couriers as $courier)
                <label class="relative block cursor-pointer group" wire:loading.remove wire:target="fetchCouriers">
                    <input wire:model.live="selectedCourier" class="peer sr-only" name="delivery" type="radio" value="{{ $courier['id'] }}"/>
                    <div class="w-full bg-surface-container-lowest border-[3px] border-primary p-6 transition-all duration-200 peer-checked:bg-primary peer-checked:text-surface hover:-translate-y-1 hover:-translate-x-1 hover:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] active:translate-y-0 active:translate-x-0 active:shadow-none peer-checked:shadow-[4px_4px_0px_0px_rgba(211,238,111,1)] h-full flex flex-col">
                        <div class="flex justify-between items-start mb-4">
                            <span class="material-symbols-outlined text-3xl peer-checked:text-tertiary-fixed transition-transform duration-300 peer-checked:scale-110">{{ $courier['icon'] ?? 'local_shipping' }}</span>
                            <span class="font-label font-bold text-lg peer-checked:text-tertiary-fixed">
                                {{ $courier['price'] == 0 ? 'FREE' : 'Rp '.number_format($courier['price'], 0, ',', '.') }}
                            </span>
                        </div>
                        <h3 class="font-headline font-bold text-xl uppercase mb-1">{{ $courier['name'] }}</h3>
                        <p class="font-body text-sm font-semibold opacity-80">{{ $courier['estimate'] }}</p>
                    </div>
                </label>
                @endforeach
            </div>
        </section>
    </div>

    <!-- Right Column: Order Summary -->
    <div class="lg:col-span-5 relative">
        <div class="sticky top-24 bg-surface border-[3px] border-primary shadow-[8px_8px_0px_0px_rgba(1,45,29,1)] p-6 md:p-8 flex flex-col gap-8">
            <header class="border-b-[3px] border-primary pb-4">
                <h2 class="font-headline font-black text-2xl uppercase tracking-tighter text-primary">Order Blueprint</h2>
            </header>

            <!-- Items List -->
            <div class="flex flex-col gap-6 max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar">
                @forelse($items as $item)
                <div class="flex gap-4 items-center">
                    <div class="w-20 h-20 bg-secondary-container border-[3px] border-primary flex-shrink-0 relative overflow-hidden">
                        <img alt="{{ $item['cake_name_snapshot'] }}" class="absolute inset-0 w-full h-full object-cover grayscale mix-blend-multiply opacity-80" src="{{ Storage::url($item['image_url_snapshot']) }}" onerror="this.src='https://placehold.co/100?text=No+Image'"/>
                    </div>
                    <div class="flex-grow flex flex-col">
                        <span class="font-headline font-bold text-sm uppercase text-primary leading-tight">{{ $item['cake_name_snapshot'] }}</span>
                        <span class="font-label text-xs text-primary/70 mt-1">QTY: {{ $item['quantity'] }}</span>
                    </div>
                    <div class="font-label font-bold text-sm text-primary text-right whitespace-nowrap pl-2">
                        Rp {{ number_format($item['price_snapshot'] * $item['quantity'], 0, ',', '.') }}
                    </div>
                </div>
                @empty
                <div class="text-sm font-body font-bold text-primary/60">Cart is empty.</div>
                @endforelse
            </div>

            <!-- Totals -->
            <div class="bg-surface-container-lowest border-[3px] border-primary p-4 flex flex-col gap-3 font-label font-bold text-sm uppercase text-primary transition-all duration-300">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Shipping</span>
                    <span class="transition-all duration-300">{{ $shippingCost == 0 ? 'TBD' : 'Rp '.number_format($shippingCost, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-primary/60">
                    <span>Admin Fee</span>
                    <span>Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Final Total -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-1 sm:gap-4 pt-4 border-t-[3px] border-primary transition-all duration-300">
                <span class="font-headline font-bold text-xl uppercase text-primary">Total</span>
                <span class="font-label font-bold text-2xl sm:text-3xl text-primary transition-all duration-300 text-left sm:text-right break-all sm:break-normal">Rp {{ number_format($total, 0, ',', '.') }}</span>
            </div>

            <!-- CTA -->
            <button wire:click="processPayment" 
                    wire:loading.attr="disabled"
                    @if(!$selectedCourier) disabled @endif
                    class="w-full bg-primary text-surface border-[3px] border-primary py-5 px-6 font-headline font-black text-lg uppercase tracking-wider flex justify-between items-center group transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-x-0 disabled:hover:translate-y-0 disabled:hover:shadow-none hover:-translate-y-1 hover:-translate-x-1 hover:shadow-[6px_6px_0px_0px_rgba(211,238,111,1)] active:translate-y-0 active:translate-x-0 active:shadow-none" 
                    type="button">
                
                <span wire:loading.remove wire:target="processPayment">Pay Now</span>
                <span wire:loading wire:target="processPayment">Processing...</span>
                
                <span wire:loading.remove wire:target="processPayment" class="material-symbols-outlined group-hover:translate-x-2 transition-transform">arrow_forward</span>
                <span wire:loading wire:target="processPayment" class="material-symbols-outlined animate-spin">sync</span>
            </button>

            <div class="text-center font-body text-xs font-semibold text-primary/60">
                By confirming, you agree to our structural terms of service.
            </div>
        </div>
    </div>
</div>
