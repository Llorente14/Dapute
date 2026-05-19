{{--
    Product Detail — /catalog/{id}
    Forest Brutalist Design System
    Matches Stitch design: "Dapute - Product Detail (Top Nav)"
    Shows: image, cake_name, description, price, weight_grams, Add to Cart button
    NEVER shows: id, created_at
--}}
<div>
    {{-- ── Breadcrumb ───────────────────────────────────── --}}
    <nav class="w-full max-w-[1200px] mx-auto px-6 pt-6 pb-4">
        <ol class="flex items-center gap-2 font-[var(--font-ui)] uppercase tracking-widest text-[11px] text-[#3d6651]">
            <li>
                <a href="{{ route('catalog.index') }}"
                   class="hover:text-[#012d1d] transition-colors">
                    Catalog
                </a>
            </li>
            <li class="text-[#012d1d] font-bold">›</li>
            <li class="text-[#012d1d] font-bold truncate max-w-[200px] md:max-w-none">
                {{ $product->cake_name }}
            </li>
        </ol>
    </nav>

    {{-- ── Main Product Section ─────────────────────────── --}}
    <section class="w-full max-w-[1200px] mx-auto px-6 pb-16">
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-10">

            {{-- ══ Left Column: Product Images ═══════════════ --}}
            <div class="w-full lg:w-[55%]">
                {{-- Main Image --}}
                <div class="relative border-[3px] border-[#012d1d] overflow-hidden bg-white">
                    <img
                        src="{{ $product->image_url ?? 'https://placehold.co/600x500/012d1d/D4EF70?text=DAPUTE' }}"
                        alt="{{ $product->cake_name }}"
                        class="w-full aspect-[6/5] object-cover"
                    >
                    {{-- Badge overlay on image --}}
                    @if($product->is_active)
                        <div class="absolute top-3 left-3">
                            <x-ui.badge variant="bestseller">Active</x-ui.badge>
                        </div>
                    @endif
                </div>

                {{-- Thumbnail Gallery (2 small images below) --}}
                <div class="grid grid-cols-2 gap-3 mt-3">
                    <div class="border-[3px] border-[#012d1d] overflow-hidden bg-white">
                        <img
                            src="{{ $product->image_url ?? 'https://placehold.co/300x250/012d1d/D4EF70?text=DAPUTE' }}"
                            alt="{{ $product->cake_name }} - detail 1"
                            class="w-full aspect-[6/5] object-cover hover:scale-105 transition-transform duration-300"
                            loading="lazy"
                        >
                    </div>
                    <div class="border-[3px] border-[#012d1d] overflow-hidden bg-white">
                        <img
                            src="{{ $product->image_url ?? 'https://placehold.co/300x250/012d1d/D4EF70?text=DAPUTE' }}"
                            alt="{{ $product->cake_name }} - detail 2"
                            class="w-full aspect-[6/5] object-cover hover:scale-105 transition-transform duration-300"
                            loading="lazy"
                        >
                    </div>
                </div>
            </div>

            {{-- ══ Right Column: Product Info ════════════════ --}}
            <div class="w-full lg:w-[45%] flex flex-col gap-5">

                {{-- Cake Name --}}
                <h1 class="font-[var(--font-display)] font-black text-[2rem] md:text-[2.5rem] lg:text-[2.75rem] text-[#012d1d] tracking-tight leading-[1.1]">
                    {{ $product->cake_name }}
                </h1>

                {{-- Price --}}
                <div class="flex items-baseline gap-2">
                    <span class="font-[var(--font-display)] font-bold text-xl md:text-2xl text-[#012d1d]">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </span>
                    <span class="font-[var(--font-ui)] uppercase tracking-widest text-[10px] text-[#3d6651]">
                        / unit
                    </span>
                </div>

                {{-- Separator line — matches description paragraph width --}}
                <div class="w-full h-[3px] bg-[#012d1d]"></div>

                {{-- Description --}}
                <p class="font-[var(--font-body)] text-[15px] md:text-base text-[#3d6651] leading-relaxed">
                    {{ $product->description }}
                </p>

                {{-- ── Blueprint Specs Card ──────────────── --}}
                <div class="border-[3px] border-[#012d1d] bg-white">
                    <div class="px-4 py-2.5 border-b-[3px] border-[#012d1d] bg-[#e8f3ec]">
                        <h3 class="font-[var(--font-ui)] uppercase tracking-widest text-[11px] font-bold text-[#012d1d] flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="square" stroke-linejoin="miter" d="M9 7h6m-6 4h6m-6 4h4M5 3h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z" />
                            </svg>
                            Blueprint Specs
                        </h3>
                    </div>

                    <div class="divide-y divide-[#e8f3ec]">
                        {{-- Weight / Mass --}}
                        <div class="flex justify-between items-center px-4 py-2.5">
                            <span class="font-[var(--font-ui)] uppercase tracking-widest text-[11px] text-[#3d6651]">
                                Mass
                            </span>
                            <span class="font-[var(--font-display)] font-bold text-[13px] text-[#012d1d] uppercase">
                                {{ $product->weight_grams }}g
                            </span>
                        </div>
                    </div>
                </div>

                {{-- ── Quantity + Add to Cart ─────────────── --}}
                <div class="flex items-stretch gap-4 mt-2">
                    {{-- Quantity Selector (editable input, min=0, max=99) --}}
                    <div class="flex items-center border-[3px] border-[#012d1d] bg-white">
                        <button type="button"
                                wire:click="decrementQty"
                                class="w-9 h-9 flex items-center justify-center font-[var(--font-ui)] font-bold text-sm text-[#012d1d] hover:bg-[#e8f3ec] transition-colors disabled:opacity-40"
                                @disabled($quantity <= 1)>
                            −
                        </button>
                        <input type="number"
                               wire:model="quantity"
                               min="1"
                               max="99"
                               oninput="if(this.value > 99) this.value = 99;"
                               onblur="if(this.value === '' || this.value < 1) this.value = 1; @this.set('quantity', this.value);"
                               class="w-10 h-9 text-center font-[var(--font-display)] font-bold text-sm text-[#012d1d]
                                      border-x-[3px] border-[#012d1d] bg-white
                                      focus:outline-none focus:bg-[#e8f3ec] transition-colors"
                        >
                        <button type="button"
                                wire:click="incrementQty"
                                class="w-9 h-9 flex items-center justify-center font-[var(--font-ui)] font-bold text-sm text-[#012d1d] hover:bg-[#e8f3ec] transition-colors disabled:opacity-40"
                                @disabled($quantity >= 99)>
                            +
                        </button>
                    </div>

                    {{-- Add to Cart Button (with toast trigger) --}}
                    <button type="button"
                            wire:click="addToCart"
                            wire:loading.attr="disabled"
                            class="flex-1 flex items-center justify-center gap-2.5
                                   px-5 py-2.5 bg-[#012d1d] text-white
                                   border-[3px] border-[#012d1d]
                                   shadow-[4px_4px_0_0_#012d1d]
                                   hover:shadow-[6px_6px_0_0_#012d1d]
                                   hover:bg-[#023d28]
                                   active:shadow-[2px_2px_0_0_#012d1d] active:translate-x-[2px] active:translate-y-[2px]
                                   transition-all duration-150
                                   font-[var(--font-ui)] uppercase tracking-widest text-xs font-bold disabled:opacity-50 disabled:cursor-not-allowed">
                        
                        <span wire:loading.remove wire:target="addToCart" class="flex items-center gap-2.5">
                            {{-- Cart Icon --}}
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="square" stroke-linejoin="miter" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
                            </svg>
                            Add to Cart
                        </span>
                        
                        <svg wire:loading wire:target="addToCart" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Bottom Info Sections (Sourcing / Process / Storage) ── --}}
    <section class="bg-[#e8f3ec]">
        <div class="w-full max-w-[1200px] mx-auto px-6 py-12">
            {{-- Separator line — matches footer content width, not full-screen --}}
            <div class="w-full h-[3px] bg-[#012d1d] mb-10"></div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-10">
                {{-- Sourcing --}}
                <div>
                    <h3 class="font-[var(--font-display)] font-bold text-base text-[#012d1d] mb-2">
                        Sourcing
                    </h3>
                    <p class="font-[var(--font-body)] text-[13px] text-[#3d6651] leading-relaxed">
                        We use high-quality ingredients sourced directly from trusted local farmers to ensure freshness and structural integrity in every product.
                    </p>
                </div>
                {{-- Process --}}
                <div>
                    <h3 class="font-[var(--font-display)] font-bold text-base text-[#012d1d] mb-2">
                        Process
                    </h3>
                    <p class="font-[var(--font-body)] text-[13px] text-[#3d6651] leading-relaxed">
                        Our dough undergoes a 48-hour cold-cure process, allowing the flour to perfectly hydrate and develop a complex, earthy flavor prior to baking.
                    </p>
                </div>
                {{-- Storage --}}
                <div>
                    <h3 class="font-[var(--font-display)] font-bold text-base text-[#012d1d] mb-2">
                        Storage
                    </h3>
                    <p class="font-[var(--font-body)] text-[13px] text-[#3d6651] leading-relaxed">
                        Store in an airtight container at room temperature. Optimal consumption is within 72 hours of delivery.
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
