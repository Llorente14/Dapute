{{--
    Product Grid — /catalog
    Forest Brutalist Design System
    Shows all active products in a responsive grid.
    Columns: 2 mobile → 3 tablet → 4 desktop
--}}
<div>
    {{-- ── Page Header ──────────────────────────────────── --}}
    <section class="w-full max-w-[1200px] mx-auto px-6 pt-10 pb-6">
        <p class="font-[var(--font-ui)] uppercase tracking-widest text-[11px] text-[#3d6651] mb-2">
            Catalog
        </p>
        <h1 class="font-[var(--font-display)] font-black text-3xl md:text-4xl text-[#012d1d] tracking-tight">
            Our Collection
        </h1>
        <div class="w-20 h-[3px] bg-[#012d1d] mt-4"></div>
    </section>

    {{-- ── Product Grid ─────────────────────────────────── --}}
    <section class="w-full max-w-[1200px] mx-auto px-6 pb-16">
        @if($products->isEmpty())
            {{-- ── Empty State ──────────────────────────── --}}
            <div class="border-[3px] border-[#012d1d] bg-white p-12 text-center shadow-[4px_4px_0_0_#012d1d]">
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
                    <a href="{{ route('catalog.show', $product->id) }}"
                       class="group block">
                        {{-- ── Product Card ─────────────── --}}
                        <x-ui.card class="flex flex-col h-full overflow-hidden">
                            {{-- Image --}}
                            <div class="relative overflow-hidden">
                                <img
                                    src="{{ $product->image_url ?? 'https://placehold.co/400x400/012d1d/D4EF70?text=DAPUTE' }}"
                                    alt="{{ $product->cake_name }}"
                                    class="w-full aspect-square object-cover border-b-[3px] border-[#012d1d]
                                           group-hover:scale-105 transition-transform duration-300"
                                    loading="lazy"
                                >
                                {{-- Badge —  Aktif chip --}}
                                @if($product->is_active)
                                    <div class="absolute top-2.5 left-2.5">
                                        <x-ui.badge variant="bestseller">Aktif</x-ui.badge>
                                    </div>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="p-3 md:p-4 flex flex-col flex-1 gap-1.5">
                                {{-- Cake Name --}}
                                <h2 class="font-[var(--font-display)] font-bold text-sm md:text-base text-[#012d1d] leading-tight line-clamp-2">
                                    {{ $product->cake_name }}
                                </h2>

                                {{-- Price --}}
                                <p class="font-[var(--font-ui)] font-bold text-xs md:text-sm text-[#012d1d] mt-auto">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </p>
                            </div>
                        </x-ui.card>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
</div>
