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
                    <h1
                        class="font-[var(--font-display)] font-black text-[2.5rem] md:text-[3.25rem] text-white tracking-tight leading-[1.05]">
                        Our Collection
                    </h1>
                    <div class="w-16 h-[3px] bg-[#D4EF70] mt-5 mb-5"></div>
                    <p class="font-[var(--font-body)] text-[15px] md:text-base text-[#b8e0c8] leading-relaxed max-w-md">
                        Setiap produk dibangun dengan bahan pilihan dan teknik presisi. Tidak ada kompromi, tidak ada
                        filler.
                    </p>
                </div>

                {{-- Right: Product count badge --}}
                <div class="flex items-center gap-3">
                    <div class="border-[3px] border-[#D4EF70] bg-[#D4EF70] px-4 py-2">
                        <span class="font-[var(--font-display)] font-bold text-lg text-[#012d1d]">
                            {{ $totalCount }}
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
         CATALOG FILTER & GRID
         ══════════════════════════════════════════════════════════ --}}
    <livewire:catalog-filter />


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
                        Setiap produk Dapute melewati proses seleksi bahan baku ketat dan teknik produksi yang presisi.
                        Kami percaya bahwa kualitas adalah fondasi, bukan dekorasi.
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
