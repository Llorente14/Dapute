{{--
    Product Grid — /catalog
    Forest Brutalist Design System
    Shows all active products in a responsive grid.
    Columns: 2 mobile → 3 tablet → 4 desktop
    Features: Hero header, search bar (UI only), product grid, footer
--}}
<div>
    {{-- ══════════════════════════════════════════════════════════
         HERO HEADER — Full-width dark background with brand feel
         ══════════════════════════════════════════════════════════ --}}
    <section class="bg-[#012d1d] relative overflow-hidden">
        {{-- Decorative background pattern --}}
        <div class="absolute inset-0 opacity-[0.04]"
             style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23D4EF70&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');">
        </div>

        <div class="w-full max-w-[1200px] mx-auto px-6 py-14 md:py-20 relative">
            <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-8">
                {{-- Left: Title & subtitle --}}
                <div class="max-w-xl">
                    <p class="font-[var(--font-ui)] uppercase tracking-[0.25em] text-[11px] text-[#D4EF70] mb-3">
                        Dapute Bakery
                    </p>
                    <h1 class="font-[var(--font-display)] font-black text-[2.5rem] md:text-[3.25rem] text-white tracking-tight leading-[1.05]">
                        Our Collection
                    </h1>
                    <div class="w-16 h-[3px] bg-[#D4EF70] mt-5 mb-5"></div>
                    <p class="font-[var(--font-body)] text-[15px] md:text-base text-[#b8e0c8] leading-relaxed max-w-md">
                        Setiap produk dibangun dengan bahan pilihan dan teknik presisi. Tidak ada kompromi, tidak ada filler.
                    </p>
                </div>

                {{-- Right: Product count badge --}}
                <div class="flex items-center gap-3">
                    <div class="border-[3px] border-[#D4EF70] bg-[#D4EF70] px-4 py-2">
                        <span class="font-[var(--font-display)] font-bold text-lg text-[#012d1d]">
                            {{ $products->count() }}
                        </span>
                    </div>
                    <span class="font-[var(--font-ui)] uppercase tracking-widest text-[11px] text-[#b8e0c8]">
                        Produk<br>Tersedia
                    </span>
                </div>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════════════════════════════════
         SEARCH BAR — Forest Brutalist input (UI only, no logic)
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
                        type="text"
                        placeholder="Cari produk..."
                        class="w-full pl-11 pr-4 py-3
                               bg-white border-[3px] border-[#012d1d]
                               font-[var(--font-body)] text-sm text-[#012d1d]
                               placeholder:text-[#3d6651]/60
                               focus:outline-none focus:shadow-[4px_4px_0_0_#012d1d]
                               transition-shadow duration-150"
                    >
                </div>

                {{-- Search Button --}}
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
    <section class="w-full max-w-[1200px] mx-auto px-6 pt-10 pb-16">
        {{-- Results info bar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-8">
            <p class="font-[var(--font-body)] text-sm text-[#3d6651]">
                Menampilkan <span class="font-bold text-[#012d1d]">{{ $products->count() }}</span> produk
            </p>
            {{-- Sort dropdown (UI only) --}}
            <div class="flex items-center gap-2">
                <span class="font-[var(--font-ui)] uppercase tracking-widest text-[10px] text-[#3d6651]">
                    Urutkan:
                </span>
                <select class="bg-[#e8f3ec] border-[3px] border-[#012d1d] px-3 py-1.5
                               font-[var(--font-ui)] text-xs text-[#012d1d] uppercase tracking-wider
                               focus:outline-none focus:shadow-[4px_4px_0_0_#012d1d] focus:bg-white
                               transition-shadow duration-150 cursor-pointer appearance-none pr-8"
                        style="background-image: url('data:image/svg+xml,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%23012d1d%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27M6 8l4 4 4-4%27/%3E%3C/svg%3E');
                               background-position: right 8px center;
                               background-repeat: no-repeat;
                               background-size: 16px;">
                    <option>Terbaru</option>
                    <option>Harga Terendah</option>
                    <option>Harga Tertinggi</option>
                    <option>Nama A-Z</option>
                </select>
            </div>
        </div>

        @if($products->isEmpty())
            {{-- ── Empty State ──────────────────────────── --}}
            <div class="border-[3px] border-[#012d1d] bg-white p-12 text-center shadow-[4px_4px_0_0_#012d1d]">
                <div class="mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto text-[#3d6651]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="square" stroke-linejoin="miter" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                </div>
                <span class="font-[var(--font-display)] font-bold text-xl text-[#012d1d] block mb-2">
                    Belum ada produk
                </span>
                <p class="font-[var(--font-body)] text-sm text-[#3d6651]">
                    Produk yang tersedia akan muncul di sini.
                </p>
            </div>
        @else
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @foreach($products as $product)
                    <div class="group">
                        {{-- ── Product Card ─────────────── --}}
                        <x-ui.card class="flex flex-col h-full overflow-hidden">
                            {{-- Image (clickable → detail page) --}}
                            <a href="{{ route('catalog.show', $product->id) }}" class="block relative overflow-hidden">
                                <img
                                    src="{{ $product->image_url ?? 'https://placehold.co/400x400/012d1d/D4EF70?text=DAPUTE' }}"
                                    alt="{{ $product->cake_name }}"
                                    class="w-full aspect-square object-cover border-b-[3px] border-[#012d1d]
                                           group-hover:scale-105 transition-transform duration-300"
                                    loading="lazy"
                                >
                                {{-- Badge — Aktif chip --}}
                                @if($product->is_active)
                                    <div class="absolute top-2.5 left-2.5">
                                        <x-ui.badge variant="bestseller">Aktif</x-ui.badge>
                                    </div>
                                @endif
                            </a>

                            {{-- Info --}}
                            <div class="p-3 md:p-4 flex flex-col flex-1 gap-1.5">
                                {{-- Cake Name (clickable → detail page) --}}
                                <a href="{{ route('catalog.show', $product->id) }}">
                                    <h2 class="font-[var(--font-display)] font-bold text-sm md:text-base text-[#012d1d] leading-tight line-clamp-2 hover:text-[#023d28] transition-colors">
                                        {{ $product->cake_name }}
                                    </h2>
                                </a>

                                {{-- Weight --}}
                                <p class="font-[var(--font-ui)] uppercase tracking-widest text-[10px] text-[#3d6651]">
                                    {{ $product->weight_grams }}g
                                </p>

                                {{-- Separator --}}
                                <div class="w-full h-[2px] bg-[#e8f3ec] mt-1"></div>

                                {{-- Price + Add to Cart row --}}
                                <div class="flex items-center justify-between mt-auto gap-2">
                                    <p class="font-[var(--font-ui)] font-bold text-xs md:text-sm text-[#012d1d]">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </p>

                                    {{-- Add to Cart button (UI only, triggers toast) --}}
                                    <button type="button"
                                            title="Tambah ke Keranjang"
                                            onclick="window.dispatchEvent(new CustomEvent('show-toast',{detail:{title:'{{ $product->cake_name }}',subtitle:'ditambahkan ke keranjang',type:'cart'}}))"
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

    {{-- ══════════════════════════════════════════════════════════
         FOOTER CTA — Call to action / brand info
         ══════════════════════════════════════════════════════════ --}}
    <section class="bg-[#e8f3ec]">
        <div class="w-full max-w-[1200px] mx-auto px-6 py-14">
            {{-- Separator line — matches content width --}}
            <div class="w-full h-[3px] bg-[#012d1d] mb-10"></div>

            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-8">
                {{-- Left: Brand statement --}}
                <div class="max-w-md">
                    <h2 class="font-[var(--font-display)] font-bold text-xl md:text-2xl text-[#012d1d] mb-3">
                        Dibangun dengan Presisi
                    </h2>
                    <p class="font-[var(--font-body)] text-[13px] text-[#3d6651] leading-relaxed">
                        Setiap produk Dapute melewati proses seleksi bahan baku ketat dan teknik produksi yang presisi. Kami percaya bahwa kualitas adalah fondasi, bukan dekorasi.
                    </p>
                </div>

                {{-- Right: Quick info blocks --}}
                <div class="flex gap-6">
                    <div>
                        <span class="font-[var(--font-display)] font-black text-2xl md:text-3xl text-[#012d1d] block">
                            100%
                        </span>
                        <span class="font-[var(--font-ui)] uppercase tracking-widest text-[10px] text-[#3d6651]">
                            Bahan Pilihan
                        </span>
                    </div>
                    <div class="w-[3px] bg-[#012d1d]"></div>
                    <div>
                        <span class="font-[var(--font-display)] font-black text-2xl md:text-3xl text-[#012d1d] block">
                            48h
                        </span>
                        <span class="font-[var(--font-ui)] uppercase tracking-widest text-[10px] text-[#3d6651]">
                            Cold-Cure
                        </span>
                    </div>
                    <div class="w-[3px] bg-[#012d1d]"></div>
                    <div>
                        <span class="font-[var(--font-display)] font-black text-2xl md:text-3xl text-[#012d1d] block">
                            0%
                        </span>
                        <span class="font-[var(--font-ui)] uppercase tracking-widest text-[10px] text-[#3d6651]">
                            Kompromi
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
