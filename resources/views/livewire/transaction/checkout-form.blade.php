<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-start w-full">
    <!-- Left Column: Forms -->
    <div class="lg:col-span-7 flex flex-col gap-12 md:gap-16 relative z-50">
        <!-- Address Input Section -->
        <section class="flex flex-col gap-6" x-data="checkoutAddressSelector(@js((string) auth()->id()), @js($recipient_name), $wire.entangle('selected_address').live, $wire.entangle('courier_type').live)" x-init="init($wire)">
            <header class="flex items-baseline justify-between mb-2">
                <h1 class="font-headline font-bold text-3xl md:text-4xl uppercase tracking-tight text-primary">Shipping
                    Details</h1>
                <a href="/profile"
                    class="font-label text-xs font-bold uppercase text-primary border-[3px] border-primary px-3 py-2 hover:bg-tertiary-fixed transition-colors">
                    Manage
                </a>
            </header>

            @error('selected_address')
                <div
                    class="bg-error-container text-on-error-container border-[3px] border-error p-4 font-label text-sm font-bold uppercase">
                    {{ $message }}
                </div>
            @enderror

            <div class="grid grid-cols-2 gap-3">
                <button type="button"
                    x-on:click="setCourierType('regular')"
                    class="border-[3px] border-primary px-4 py-3 font-label font-black text-xs uppercase tracking-widest transition-all shadow-[4px_4px_0_0_#012d1d]"
                    :class="courierType === 'regular' ? 'bg-primary text-surface' : 'bg-surface-container-lowest text-primary hover:bg-tertiary-fixed'">
                    Regular
                </button>
                <button type="button"
                    x-on:click="setCourierType('instant')"
                    class="border-[3px] border-primary px-4 py-3 font-label font-black text-xs uppercase tracking-widest transition-all shadow-[4px_4px_0_0_#012d1d]"
                    :class="courierType === 'instant' ? 'bg-primary text-surface' : 'bg-surface-container-lowest text-primary hover:bg-tertiary-fixed'">
                    Instant
                </button>
            </div>

            <div x-show="addresses.length > 0 && addresses.length <= 3" class="grid grid-cols-1 gap-4">
                <template x-for="address in addresses" :key="address.id">
                    <label class="block cursor-pointer">
                        <input x-model="selectedId" x-on:change="select(address)" class="peer sr-only"
                            name="shipping_address" type="radio" :value="address.id" />
                        <div class="bg-surface-container-lowest border-[3px] border-primary p-5 transition-all peer-checked:bg-primary peer-checked:text-surface hover:-translate-y-1"
                            style="box-shadow: 4px 4px 0px 0px #012d1d;">
                            <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                                <span class="bg-[#D4EF70] text-primary font-label text-xs font-bold uppercase px-2 py-1"
                                    x-text="address.is_default ? 'Default' : address.label"></span>
                                <span class="material-symbols-outlined text-2xl">radio_button_checked</span>
                            </div>
                            <p class="font-headline font-bold text-lg uppercase" x-text="address.recipient_name"></p>
                            <p class="font-body text-sm font-semibold opacity-80" x-text="address.recipient_phone"></p>
                            <p class="font-body text-sm font-semibold opacity-80 mt-2">
                                <span x-text="address.address"></span><br />
                                <span x-text="address.city"></span>,
                                <span x-text="address.postal_code"></span>
                            </p>
                        </div>
                    </label>
                </template>
            </div>

            <div x-show="addresses.length > 3" class="flex flex-col gap-4">
                <div class="relative z-20" x-data="{ open: false }">
                    <label class="sr-only" for="shipping_address_select">Shipping Address</label>
                    <button id="shipping_address_select" @click="open = !open" @click.outside="open = false"
                        type="button"
                        class="w-full bg-surface-container-lowest border-[3px] border-primary px-4 py-4 shadow-[4px_4px_0_0_#012d1d] hover:shadow-[6px_6px_0_0_#012d1d] focus:outline-none focus:shadow-[6px_6px_0_0_#012d1d] transition-all duration-150 flex items-center justify-between gap-4 text-left">
                        <span class="font-label text-sm font-bold uppercase text-primary truncate"
                            x-text="selectedAddressDetails() ? `${selectedAddressDetails().is_default ? 'Default' : selectedAddressDetails().label} - ${selectedAddressDetails().recipient_name} (${selectedAddressDetails().postal_code})` : 'Select Address'"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" stroke="#012d1d"
                            stroke-width="2" class="w-4 h-4 shrink-0 transition-transform duration-200"
                            :class="open ? 'rotate-180' : ''">
                            <path stroke-linecap="square" stroke-linejoin="miter" d="M6 8l4 4 4-4" />
                        </svg>
                    </button>

                    <div x-show="open" style="display:none;" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-100"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2"
                        class="absolute left-0 right-0 mt-1 bg-white border-[3px] border-primary shadow-[4px_4px_0_0_#012d1d]">
                        <template x-for="address in addresses" :key="address.id">
                            <button type="button" @click="select(address); open = false"
                                class="w-full px-4 py-3 font-label font-bold text-xs uppercase tracking-wider text-left transition-colors"
                                :class="String(selectedId) === String(address.id) ? 'bg-primary text-white' :
                                    'text-primary hover:bg-primary hover:text-white'">
                                <span
                                    x-text="`${address.is_default ? 'Default' : address.label} - ${address.recipient_name} (${address.postal_code})`"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <template x-if="selectedAddressDetails()">
                    <div class="bg-primary text-surface border-[3px] border-primary p-5"
                        style="box-shadow: 4px 4px 0px 0px #012d1d;">
                        <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
                            <span class="bg-[#D4EF70] text-primary font-label text-xs font-bold uppercase px-2 py-1"
                                x-text="selectedAddressDetails().is_default ? 'Default' : selectedAddressDetails().label"></span>
                            <span class="material-symbols-outlined text-2xl">radio_button_checked</span>
                        </div>
                        <p class="font-headline font-bold text-lg uppercase"
                            x-text="selectedAddressDetails().recipient_name"></p>
                        <p class="font-body text-sm font-semibold opacity-80"
                            x-text="selectedAddressDetails().recipient_phone"></p>
                        <p class="font-body text-sm font-semibold opacity-80 mt-2">
                            <span x-text="selectedAddressDetails().address"></span><br />
                            <span x-text="selectedAddressDetails().city"></span>,
                            <span x-text="selectedAddressDetails().postal_code"></span>
                        </p>
                    </div>
                </template>
            </div>

            <div x-show="addresses.length === 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="sr-only" for="manual_recipient_name">Recipient Name</label>
                    <input x-model="manual.recipient_name" x-on:input="syncManual()"
                        class="w-full bg-surface-container-lowest border-[3px] border-primary p-4 font-label text-sm font-bold uppercase text-primary placeholder-primary/40 focus:bg-surface-container-lowest focus:outline-none focus:ring-0 transition-all duration-200 hover:-translate-y-0.5 focus:-translate-y-0.5"
                        id="manual_recipient_name" placeholder="RECIPIENT NAME" type="text" />
                    @error('selected_address.recipient_name')
                        <p class="mt-2 font-body text-xs font-bold text-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="sr-only" for="manual_recipient_phone">Phone Number</label>
                    <input x-model="manual.recipient_phone" x-on:input="syncManual()"
                        class="w-full bg-surface-container-lowest border-[3px] border-primary p-4 font-label text-sm font-bold uppercase text-primary placeholder-primary/40 focus:bg-surface-container-lowest focus:outline-none focus:ring-0 transition-all duration-200 hover:-translate-y-0.5 focus:-translate-y-0.5"
                        id="manual_recipient_phone" placeholder="PHONE NUMBER" type="tel" />
                    @error('selected_address.recipient_phone')
                        <p class="mt-2 font-body text-xs font-bold text-error">{{ $message }}</p>
                    @enderror
                </div>
                <div class="md:col-span-2">
                    <label class="sr-only" for="manual_address">Street Address</label>
                    <textarea x-model="manual.address" x-on:input="syncManual()"
                        class="w-full bg-surface-container-lowest border-[3px] border-primary p-4 font-label text-sm font-bold uppercase text-primary placeholder-primary/40 focus:bg-surface-container-lowest focus:outline-none focus:ring-0 transition-all duration-200 hover:-translate-y-0.5 focus:-translate-y-0.5 resize-none"
                        id="manual_address" placeholder="STREET ADDRESS" rows="3"></textarea>
                    @error('selected_address.address')
                        <p class="mt-2 font-body text-xs font-bold text-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="sr-only" for="manual_city">City</label>
                    <input x-model="manual.city" x-on:input="syncManual()"
                        class="w-full bg-surface-container-lowest border-[3px] border-primary p-4 font-label text-sm font-bold uppercase text-primary placeholder-primary/40 focus:bg-surface-container-lowest focus:outline-none focus:ring-0 transition-all duration-200 hover:-translate-y-0.5 focus:-translate-y-0.5"
                        id="manual_city" placeholder="CITY" type="text" />
                    @error('selected_address.city')
                        <p class="mt-2 font-body text-xs font-bold text-error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="sr-only" for="manual_postal_code">Postal Code</label>
                    <input x-model="manual.postal_code" x-on:input="syncManual()"
                        class="w-full bg-surface-container-lowest border-[3px] border-primary p-4 font-label text-sm font-bold uppercase text-primary placeholder-primary/40 focus:bg-surface-container-lowest focus:outline-none focus:ring-0 transition-all duration-200 hover:-translate-y-0.5 focus:-translate-y-0.5"
                        id="manual_postal_code" placeholder="POSTAL CODE" type="text" inputmode="numeric" />
                    @error('selected_address.postal_code')
                        <p class="mt-2 font-body text-xs font-bold text-error">{{ $message }}</p>
                    @enderror
                </div>
                <div
                    class="md:col-span-2 bg-tertiary-fixed border-[3px] border-primary p-4 font-label text-xs font-bold uppercase text-primary">
                    No saved address found. This checkout address is sent directly to Livewire.
                </div>
            </div>

            <div x-show="addresses.length > 0">
                @error('selected_address.recipient_name')
                    <p class="mt-2 font-body text-xs font-bold text-error">{{ $message }}</p>
                @enderror
                @error('selected_address.recipient_phone')
                    <p class="mt-2 font-body text-xs font-bold text-error">{{ $message }}</p>
                @enderror
                @error('selected_address.address')
                    <p class="mt-2 font-body text-xs font-bold text-error">{{ $message }}</p>
                @enderror
                @error('selected_address.city')
                    <p class="mt-2 font-body text-xs font-bold text-error">{{ $message }}</p>
                @enderror
                @error('selected_address.postal_code')
                    <p class="mt-2 font-body text-xs font-bold text-error">{{ $message }}</p>
                @enderror
                @error('selected_address.coordinates')
                    <p class="mt-2 font-body text-xs font-bold text-error">{{ $message }}</p>
                @enderror
            </div>

            <div x-show="courierType === 'instant'" x-effect="if (courierType === 'instant') initCoordinatePicker()"
                class="bg-surface-container-lowest border-[3px] border-primary p-5 flex flex-col gap-4"
                style="box-shadow: 4px 4px 0px 0px #012d1d;">
                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                    <div>
                        <h2 class="font-headline font-black text-2xl uppercase tracking-tighter text-primary">
                            Instant Pin Location
                        </h2>
                        <p class="font-body text-sm font-semibold text-primary/70 mt-1">
                            Instant courier requires accurate latitude and longitude.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <button type="button"
                            x-on:click="useMyLocation()"
                            class="inline-flex items-center gap-2 border-[3px] border-primary bg-tertiary-fixed px-3 py-2 font-label font-black text-xs uppercase tracking-wider text-primary shadow-[3px_3px_0_0_#012d1d] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all">
                            <span class="material-symbols-outlined text-[18px]">my_location</span>
                            Use My Location
                        </button>
                        <button type="button"
                            x-on:click="geocodeAddress(true)"
                            class="inline-flex items-center gap-2 border-[3px] border-primary bg-white px-3 py-2 font-label font-black text-xs uppercase tracking-wider text-primary shadow-[3px_3px_0_0_#012d1d] hover:-translate-x-0.5 hover:-translate-y-0.5 transition-all">
                            <span class="material-symbols-outlined text-[18px]">travel_explore</span>
                            Geocode Address
                        </button>
                    </div>
                </div>

                <div x-ref="coordinateMap" wire:ignore
                    class="h-[320px] w-full border-[3px] border-primary bg-secondary-container overflow-hidden"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div class="border-[3px] border-primary bg-white p-3 font-label text-xs font-black uppercase text-primary">
                        Latitude:
                        <span x-text="currentCoordinates() ? currentCoordinates().latitude : 'Not selected'"></span>
                    </div>
                    <div class="border-[3px] border-primary bg-white p-3 font-label text-xs font-black uppercase text-primary">
                        Longitude:
                        <span x-text="currentCoordinates() ? currentCoordinates().longitude : 'Not selected'"></span>
                    </div>
                </div>

                <p class="font-body text-xs font-bold text-primary/70" x-text="coordinateStatus"></p>
            </div>
        </section>

        <!-- Delivery Method Options -->
        <section class="flex flex-col gap-6" wire:init="fetchCouriers">
            <header class="flex items-baseline justify-between mb-2">
                <h2 class="font-headline font-bold text-2xl md:text-3xl uppercase tracking-tight text-primary">Delivery
                    Method</h2>
            </header>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 relative">

                <!-- Loading Skeleton -->
                <div wire:loading wire:target="fetchCouriers"
                    class="absolute inset-0 bg-surface/50 backdrop-blur-sm z-10 flex items-center justify-center">
                    <div class="flex gap-2">
                        <div class="w-4 h-4 bg-primary animate-[bounce_1s_infinite]"></div>
                        <div class="w-4 h-4 bg-primary animate-[bounce_1s_infinite_100ms]"></div>
                        <div class="w-4 h-4 bg-primary animate-[bounce_1s_infinite_200ms]"></div>
                    </div>
                </div>

                @if ($courierError)
                    <div
                        class="sm:col-span-2 bg-error-container text-on-error-container border-[3px] border-error p-4 font-label text-sm font-bold uppercase">
                        {{ $courierError }}
                    </div>
                @endif

                @if (empty($couriers) && !$courierError)
                    <!-- Placeholder skeleton structure if empty initially -->
                    <div wire:loading wire:target="fetchCouriers"
                        class="w-full bg-surface-container-lowest border-[3px] border-primary p-6 animate-pulse">
                        <div class="h-8 bg-surface-variant w-1/3 mb-4"></div>
                        <div class="h-6 bg-surface-variant w-1/2"></div>
                    </div>
                    <div wire:loading wire:target="fetchCouriers"
                        class="w-full bg-surface-container-lowest border-[3px] border-primary p-6 animate-pulse">
                        <div class="h-8 bg-surface-variant w-1/3 mb-4"></div>
                        <div class="h-6 bg-surface-variant w-1/2"></div>
                    </div>
                    <div wire:loading.remove wire:target="fetchCouriers"
                        class="sm:col-span-2 bg-surface-container-lowest border-[3px] border-primary p-4 font-label text-xs font-bold uppercase text-primary">
                        {{ empty($selected_address) ? 'Complete shipping address to load courier rates.' : 'Waiting for courier rates. Select another address if this stays empty.' }}
                    </div>
                @endif

                @foreach ($couriers as $courier)
                    <label class="relative block cursor-pointer group" wire:loading.remove
                        wire:target="fetchCouriers">
                        <input wire:model.live="selected_courier" class="peer sr-only" name="delivery"
                            type="radio" value="{{ $courier['id'] }}" />
                        <div class="w-full bg-surface-container-lowest border-[3px] border-primary p-6 transition-all duration-200 peer-checked:bg-primary peer-checked:text-surface hover:-translate-y-1 hover:-translate-x-1 active:translate-y-0 active:translate-x-0 h-full flex flex-col"
                            style="box-shadow: 4px 4px 0px 0px #012d1d;">
                            <div class="flex justify-between items-start mb-4">
                                <span
                                    class="material-symbols-outlined text-3xl peer-checked:text-tertiary-fixed transition-transform duration-300 peer-checked:scale-110">{{ $courier['icon'] ?? 'local_shipping' }}</span>
                                <span class="font-label font-bold text-lg peer-checked:text-tertiary-fixed">
                                    {{ $courier['price'] == 0 ? 'FREE' : 'Rp ' . number_format($courier['price'], 0, ',', '.') }}
                                </span>
                            </div>
                            <h3 class="font-headline font-bold text-xl uppercase mb-1">{{ $courier['name'] }}</h3>
                            <p class="font-body text-sm font-semibold opacity-80">{{ $courier['service'] }}</p>
                            <p class="font-body text-sm font-semibold opacity-80">{{ $courier['estimate'] }}</p>
                        </div>
                    </label>
                @endforeach
                @error('selected_courier')
                    <div
                        class="sm:col-span-2 bg-error-container text-on-error-container border-[3px] border-error p-4 font-label text-sm font-bold uppercase">
                        {{ $message }}
                    </div>
                @enderror
            </div>
        </section>
    </div>

    <!-- Right Column: Order Summary -->
    <div class="lg:col-span-5 relative">
        <div class="sticky top-24 bg-surface border-[3px] border-primary p-6 md:p-8 flex flex-col gap-8"
            style="box-shadow: 8px 8px 0px 0px #012d1d;">
            <header class="border-b-[3px] border-primary pb-4">
                <h2 class="font-headline font-black text-2xl uppercase tracking-tighter text-primary">Order Blueprint
                </h2>
            </header>

            <!-- Items List -->
            <div class="flex flex-col gap-6 max-h-[40vh] overflow-y-auto pr-2 custom-scrollbar">
                @forelse($items as $item)
                    <div class="flex gap-4 items-center">
                        <div
                            class="w-20 h-20 bg-secondary-container border-[3px] border-primary flex-shrink-0 relative overflow-hidden">
                            @php
                                $imageUrl = $item['image_url_snapshot'] ?: 'https://placehold.co/100?text=No+Image';
                            @endphp
                            <img alt="{{ $item['cake_name_snapshot'] }}"
                                class="absolute inset-0 w-full h-full object-cover grayscale mix-blend-multiply opacity-80"
                                src="{{ $imageUrl }}"
                                onerror="this.src='https://placehold.co/100?text=No+Image'" />
                        </div>
                        <div class="flex-grow flex flex-col">
                            <span
                                class="font-headline font-bold text-sm uppercase text-primary leading-tight">{{ $item['cake_name_snapshot'] }}</span>
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

            <!-- Notes -->
            <div>
                <label class="sr-only" for="checkout_notes">Order Notes</label>
                <textarea wire:model.live.debounce.500ms="notes" id="checkout_notes" rows="3" maxlength="500"
                    class="w-full bg-surface-container-lowest border-[3px] border-primary p-4 font-label text-sm font-bold uppercase text-primary placeholder-primary/40 focus:bg-surface-container-lowest focus:outline-none focus:ring-0 resize-none"
                    placeholder="ORDER NOTES"></textarea>
            </div>

            <!-- Totals -->
            <div
                class="bg-surface-container-lowest border-[3px] border-primary p-4 flex flex-col gap-3 font-label font-bold text-sm uppercase text-primary transition-all duration-300">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Shipping</span>
                    <span
                        class="transition-all duration-300">{{ $shippingCost == 0 ? 'TBD' : 'Rp ' . number_format($shippingCost, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-primary/60">
                    <span>Admin Fee</span>
                    <span>Rp {{ number_format($adminFee, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Final Total -->
            <div
                class="flex flex-col sm:flex-row sm:justify-between sm:items-end gap-1 sm:gap-4 pt-4 border-t-[3px] border-primary transition-all duration-300">
                <span class="font-headline font-bold text-xl uppercase text-primary">Total</span>
                <span
                    class="font-label font-bold text-2xl sm:text-3xl text-primary transition-all duration-300 text-left sm:text-right break-all sm:break-normal">Rp
                    {{ number_format($total, 0, ',', '.') }}</span>
            </div>

            <!-- CTA -->
            @error('order')
                <div
                    class="bg-error-container text-on-error-container border-[3px] border-error p-4 font-label text-sm font-bold uppercase">
                    {{ $message }}
                </div>
            @enderror

            <button wire:click="placeOrder" wire:loading.attr="disabled" wire:target="placeOrder"
                @if (!$selected_courier) disabled @endif
                class="w-full bg-primary text-surface border-[3px] border-primary py-5 px-6 font-headline font-black text-lg uppercase tracking-wider flex justify-between items-center group transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:translate-x-0 disabled:hover:translate-y-0 hover:-translate-y-1 hover:-translate-x-1 active:translate-y-0 active:translate-x-0"
                style="box-shadow: 6px 6px 0px 0px #D4EF70;" type="button">

                <span wire:loading.remove wire:target="placeOrder">Pay Now</span>
                <span wire:loading wire:target="placeOrder">Processing...</span>

                <span wire:loading.remove wire:target="placeOrder"
                    class="material-symbols-outlined group-hover:translate-x-2 transition-transform">arrow_forward</span>
                <span wire:loading wire:target="placeOrder" class="material-symbols-outlined animate-spin">sync</span>
            </button>

            <div class="text-center font-body text-xs font-semibold text-primary/60">
                By confirming, you agree to our structural terms of service.
            </div>
        </div>
    </div>
</div>
