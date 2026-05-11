<div>
    {{-- ══════════════════════════════════════════════════════════
         SEARCH BAR — Forest Brutalist input (Livewire)
         ══════════════════════════════════════════════════════════ --}}
    <section class="bg-[#e8f3ec] border-b-[3px] border-[#012d1d]">
        <div class="w-full max-w-[1200px] mx-auto px-6 py-5">
            <div class="flex flex-col sm:flex-row items-stretch gap-3">
                {{-- Search Input --}}
                <div class="flex-1 relative">
                    <div class="absolute left-4 top-1/2 -translate-y-1/2 text-[#3d6651] pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                            <path stroke-linecap="square" stroke-linejoin="miter" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input
                        wire:model.live.debounce.300ms="search"
                        type="text"
                        placeholder="Cari produk..."
                        class="w-full pl-11 pr-10 py-3
                               bg-white border-[3px] border-[#012d1d]
                               font-[var(--font-body)] text-sm text-[#012d1d]
                               placeholder:text-[#3d6651]/60
                               focus:outline-none focus:shadow-[4px_4px_0_0_#012d1d]
                               transition-shadow duration-150"
                    >
                    
                    {{-- Loading Spinner (absolute inside the input to the right) --}}
                    <div wire:loading wire:target="search" class="absolute right-4 top-1/2 -translate-y-1/2">
                        <svg class="animate-spin h-5 w-5 text-[#012d1d]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>
                </div>

                {{-- Search Button (Disabled during loading) --}}
                <button type="button"
                        class="px-6 py-3 bg-[#012d1d] text-white
                               border-[3px] border-[#012d1d]
                               shadow-[4px_4px_0_0_#012d1d]
                               hover:shadow-[6px_6px_0_0_#012d1d]
                               active:shadow-[2px_2px_0_0_#012d1d] active:translate-x-[2px] active:translate-y-[2px]
                               transition-all duration-150
                               font-[var(--font-ui)] uppercase tracking-widest text-xs font-bold
                               flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="square" stroke-linejoin="miter" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Cari
                </button>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════
         PRODUCT GRID — Cards with images and details
         ══════════════════════════════════════════════════════════ --}}
    <section class="w-full max-w-[1200px] mx-auto px-6 pt-10 pb-16 relative">
        
        {{-- Results info bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
            <p class="font-[var(--font-body)] text-sm text-[#3d6651]">
                Menampilkan <span class="font-bold text-[#012d1d]">{{ count($products) }}</span> produk
            </p>
            {{-- Sort dropdown (Custom Alpine UI) --}}
            <div class="flex items-center gap-2">
                <span class="font-[var(--font-ui)] uppercase tracking-widest text-[10px] text-[#3d6651]">
                    Urutkan:
                </span>
                <div x-data="{ open: false, selected: 'Terbaru', options: ['Terbaru', 'Harga Terendah', 'Harga Tertinggi', 'Nama A-Z'] }" class="relative">
                    <button @click="open = !open" @click.outside="open = false" type="button"
                            class="bg-[#e8f3ec] border-[3px] border-[#012d1d] px-3 py-1.5
                                   font-[var(--font-ui)] text-xs text-[#012d1d] uppercase tracking-wider
                                   focus:outline-none focus:shadow-[4px_4px_0_0_#012d1d] focus:bg-white
                                   transition-shadow duration-150 cursor-pointer flex items-center justify-between w-[170px]">
                        <span x-text="selected"></span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" stroke="#012d1d" stroke-width="2" class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"><path stroke-linecap="square" stroke-linejoin="miter" d="M6 8l4 4 4-4"/></svg>
                    </button>

                    <div x-show="open" style="display: none;"
                         x-transition.opacity.duration.150ms
                         class="absolute z-10 w-full mt-1 bg-white border-[3px] border-[#012d1d] shadow-[4px_4px_0_0_#012d1d]">
                        <template x-for="option in options" :key="option">
                            <div @click="selected = option; open = false"
                                 class="px-3 py-2 font-[var(--font-ui)] text-xs uppercase tracking-wider cursor-pointer transition-colors"
                                 :class="selected === option ? 'bg-[#012d1d] text-white' : 'text-[#012d1d] hover:bg-[#012d1d] hover:text-white'">
                                <span x-text="option"></span>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        @if(empty($products))
            {{-- ── Empty State ──────────────────────────── --}}
            <div class="border-[3px] border-[#012d1d] bg-white p-12 text-center shadow-[4px_4px_0_0_#012d1d]">
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-[#3d6651]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="square" stroke-linejoin="miter" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <span class="font-[var(--font-display)] font-bold text-xl text-[#012d1d] block mb-2">
                    {{ !empty($search) ? 'Produk tidak ditemukan' : 'Belum ada produk' }}
                </span>
                <p class="font-[var(--font-body)] text-sm text-[#3d6651]">
                    {{ !empty($search) ? 'Coba gunakan kata kunci pencarian yang lain.' : 'Produk yang tersedia akan muncul di sini.' }}
                </p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5" wire:loading.class="opacity-50" wire:target="search">
                @foreach($products as $product)
                    <div class="group">
                        {{-- ── Product Card ─────────────── --}}
                        <x-ui.card class="flex flex-col h-full overflow-hidden">
                            {{-- Image (clickable → detail page) --}}
                            <a href="{{ route('catalog.show', $product['id']) }}" class="block relative overflow-hidden">
                                <img
                                    src="{{ $product['image_url'] ?? 'https://placehold.co/400x400/012d1d/D4EF70?text=DAPUTE' }}"
                                    alt="{{ $product['cake_name'] }}"
                                    class="w-full aspect-square object-cover border-b-[3px] border-[#012d1d]
                                           group-hover:scale-105 transition-transform duration-300"
                                    loading="lazy"
                                >
                                {{-- Badge — Aktif chip --}}
                                @if($product['is_active'])
                                    <div class="absolute top-2.5 left-2.5">
                                        <x-ui.badge variant="bestseller">Aktif</x-ui.badge>
                                    </div>
                                @endif
                            </a>

                            {{-- Info --}}
                            <div class="p-3 md:p-4 flex flex-col flex-1 gap-1.5">
                                {{-- Cake Name (clickable → detail page) --}}
                                <a href="{{ route('catalog.show', $product['id']) }}">
                                    <h2 class="font-[var(--font-display)] font-bold text-sm md:text-base text-[#012d1d] leading-tight line-clamp-2 hover:text-[#023d28] transition-colors">
                                        {{ $product['cake_name'] }}
                                    </h2>
                                </a>

                                {{-- Weight --}}
                                <p class="font-[var(--font-ui)] uppercase tracking-widest text-[10px] text-[#3d6651]">
                                    {{ $product['weight_grams'] }}g
                                </p>

                                {{-- Separator --}}
                                <div class="w-full h-[2px] bg-[#e8f3ec] mt-1"></div>

                                {{-- Price + Add to Cart row --}}
                                <div class="flex items-center justify-between mt-auto gap-2">
                                    <p class="font-[var(--font-ui)] font-bold text-xs md:text-sm text-[#012d1d]">
                                        {{ $product['price'] }}
                                    </p>

                                    {{-- Add to Cart button (UI only, triggers toast) --}}
                                    <button type="button"
                                            title="Tambah ke Keranjang"
                                            onclick="window.dispatchEvent(new CustomEvent('show-toast',{detail:{title:'{{ addslashes($product['cake_name']) }}',subtitle:'ditambahkan ke keranjang',type:'cart'}}))"
                                            class="w-8 h-8 md:w-9 md:h-9 flex items-center justify-center
                                                   bg-[#012d1d] text-white
                                                   border-[3px] border-[#012d1d]
                                                   shadow-[2px_2px_0_0_#012d1d]
                                                   hover:shadow-[4px_4px_0_0_#012d1d] hover:bg-[#023d28]
                                                   active:shadow-none active:translate-x-[2px] active:translate-y-[2px]
                                                   transition-all duration-150">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="square" stroke-linejoin="miter" d="M12 4v16m8-8H4" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </x-ui.card>
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
