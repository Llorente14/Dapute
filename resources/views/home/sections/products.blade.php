{{-- ═══ FEATURED PRODUCTS / BESTSELLERS ═════════════════════════════════════════
     Hardcoded skeleton. Jika FetchActiveProductsAction (SCRUM-35) mengembalikan
     data (count > 0), gunakan loop @foreach. Jika tidak, tampilkan skeleton ini.
═══════════════════════════════════════════════════════════════════════════════ --}}

@php
    // TODO: SCRUM-35 — Wire ke FetchActiveProductsAction
    // $products = app(\App\Actions\Catalog\FetchActiveProductsAction::class)->execute(limit: 4);
    $products = collect(); // Placeholder kosong — skeleton akan tampil
@endphp

<section class="py-12 md:py-20 px-4 md:px-8 max-w-[1200px] mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 md:mb-16 gap-4 md:gap-8">
        <div>
            <span class="font-label text-tertiary font-bold tracking-widest uppercase text-xs md:text-base">Our Collection</span>
            <h2 class="font-headline font-black text-3xl md:text-5xl text-primary mt-2">Our Bestsellers</h2>
        </div>
        <a href="/catalog" class="bg-surface text-primary font-label px-6 py-3 border-[3px] border-primary font-bold hover:bg-secondary-container transition-all inline-block">
            VIEW FULL CATALOG →
        </a>
    </div>

    {{-- Product Grid: 1 col mobile, 2 col tablet, 4 col desktop --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8">

        @if($products->count() > 0)
            {{-- ── Dynamic products from FetchActiveProductsAction ── --}}
            @foreach($products->take(4) as $product)
                <div class="group bg-surface-container-lowest border-[3px] border-primary neo-shadow p-4 md:p-6 transition-all hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#012d1d]">
                    <div class="aspect-square mb-4 md:mb-6 border-[3px] border-primary overflow-hidden">
                        <img
                            alt="{{ $product->cake_name ?? 'Product image' }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                            src="{{ $product->image_url ?? '' }}"
                        />
                    </div>
                    <p class="font-label font-bold text-primary mb-1">Rp {{ number_format($product->price ?? 0, 0, ',', '.') }}</p>
                    <h3 class="font-headline font-black text-xl md:text-2xl text-primary mb-2 md:mb-4">{{ $product->cake_name }}</h3>
                    <div class="flex justify-between items-center">
                        <p class="font-body text-sm text-primary/70">{{ $product->short_description ?? '' }}</p>
                        <a href="/catalog" class="w-10 h-10 bg-primary text-on-primary flex items-center justify-center neo-shadow active:translate-y-[2px] active:shadow-none shrink-0">
                            <span class="material-symbols-outlined">add</span>
                        </a>
                    </div>
                </div>
            @endforeach
        @else
            {{-- ── Hardcoded skeleton / placeholder products ── --}}

            {{-- Card 1: The Classic Nastar --}}
            <div class="group bg-surface-container-lowest border-[3px] border-primary neo-shadow p-4 md:p-6 transition-all hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#012d1d]">
                <div class="aspect-square mb-4 md:mb-6 border-[3px] border-primary overflow-hidden">
                    <img
                        alt="Traditional Indonesian pineapple tart cookies (Nastar) neatly arranged on a ceramic plate"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCK4Y7Y2rJQxN74nSDhvoM6w1b2rgDa2rixGTwcFZp9sRc2ArcU40AIZensLexvjGj7CvBf2uLUx2coImKGOmFimcOShumnB21vGhScxE64k0xLadF4nWRpTruwv_BR7JtASCMIoUlR6up9vNKRWs_ymJt3ZR0vTCMPI3S_1uc3w74wACAJJqUvyp5df4GCev9yZSl2Fwg4lBrQflN8neQ68IzR10PvsjYhOUdfk4Ev1bfAyYNHIb2qf_qSpWWcjguDTPesiZKgEzw"
                    />
                </div>
                <p class="font-label font-bold text-primary mb-1">Rp 85.000</p>
                <h3 class="font-headline font-black text-xl md:text-2xl text-primary mb-2 md:mb-4">The Classic Nastar</h3>
                <div class="flex justify-between items-center">
                    <p class="font-body text-sm text-primary/70">Signature pineapple fill</p>
                    <a href="/catalog" class="w-10 h-10 bg-primary text-on-primary flex items-center justify-center neo-shadow active:translate-y-[2px] active:shadow-none shrink-0">
                        <span class="material-symbols-outlined">add</span>
                    </a>
                </div>
            </div>

            {{-- Card 2: Savory Kastangel --}}
            <div class="group bg-surface-container-lowest border-[3px] border-primary neo-shadow p-4 md:p-6 transition-all hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#012d1d]">
                <div class="aspect-square mb-4 md:mb-6 border-[3px] border-primary overflow-hidden">
                    <img
                        alt="Crunchy cheese stick cookies (Kastangel) in a glass jar, sprinkled with grated cheddar cheese"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCUSUeNuaClAZUAtCA1KgRiYSfxrQUEP3KgzA3pJJk6F1EvrLfvmrl085db2g-cG8Trcr0rewHS8_2_DaB_OJRH1f_Cg5QXI5PsOwjhQjY0inoPG_LIBKXhZ3pYljUxydAjggI3FImUD9UJkz8MIjEteiz6RGvWg3vQOR-wkLXaqPmI9AS-OQeEuaqLUcoEtxmkbfB0-DE-r7P1i2lfxqBbmRhG2iaKD2Y5jEiokWuerbwuKkXPTFOaO4nDzGsJhCkaj8IyLICo2PY"
                    />
                </div>
                <p class="font-label font-bold text-primary mb-1">Rp 92.000</p>
                <h3 class="font-headline font-black text-xl md:text-2xl text-primary mb-2 md:mb-4">Savory Kastangel</h3>
                <div class="flex justify-between items-center">
                    <p class="font-body text-sm text-primary/70">Triple cheese blend</p>
                    <a href="/catalog" class="w-10 h-10 bg-primary text-on-primary flex items-center justify-center neo-shadow active:translate-y-[2px] active:shadow-none shrink-0">
                        <span class="material-symbols-outlined">add</span>
                    </a>
                </div>
            </div>

            {{-- Card 3: Dark Choco Sea Salt --}}
            <div class="group bg-surface-container-lowest border-[3px] border-primary neo-shadow p-4 md:p-6 transition-all hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#012d1d]">
                <div class="aspect-square mb-4 md:mb-6 border-[3px] border-primary overflow-hidden">
                    <img
                        alt="Thick chewy chocolate chip cookies stacked on parchment paper with dark chocolate puddles"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuBhjjAzl3wHP3ETOlAyR3jGntRYPHCI7XWcYcfLt2iDtobOKQqp04cB1CIq2IUYrXO36uN3kD-G8xOvRJrxTAsXrKn4BqX20T5R-JrzXwLWxYRKwiSZjwKHwD_FS3lKO9d0dR8w51BjJzXQUggsHPfaWdpm9_Wx-tQUXJe5TAC7_Sel2BQhipRlnZxsmEjbROx_rqiVeuY9v0rhnXCeY9BEJ5HDpwuw0Udpxn-ohzDW7AzeMyB_SwstkxPnSIA3wWLKbPB4g7KFJnU"
                    />
                </div>
                <p class="font-label font-bold text-primary mb-1">Rp 78.000</p>
                <h3 class="font-headline font-black text-xl md:text-2xl text-primary mb-2 md:mb-4">Dark Choco Sea Salt</h3>
                <div class="flex justify-between items-center">
                    <p class="font-body text-sm text-primary/70">70% Cocoa organic</p>
                    <a href="/catalog" class="w-10 h-10 bg-primary text-on-primary flex items-center justify-center neo-shadow active:translate-y-[2px] active:shadow-none shrink-0">
                        <span class="material-symbols-outlined">add</span>
                    </a>
                </div>
            </div>

            {{-- Card 4: Matcha Zen White --}}
            <div class="group bg-surface-container-lowest border-[3px] border-primary neo-shadow p-4 md:p-6 transition-all hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#012d1d]">
                <div class="aspect-square mb-4 md:mb-6 border-[3px] border-primary overflow-hidden">
                    <img
                        alt="Matcha green tea cookies with white chocolate drizzle on a minimalist clean background"
                        class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAgv_RFHoJMnb_CU4eRcE6WGXbPmHXI1RBJSHsJD9KrdornUN2wYVpy0PVNCefKPQAP7tTX3uwibTe_DTXqVjBGAzpldyzDOPtwXcokSrj5wx2wLZaXLOO8C4_YPXX0dmUKuyCOKGyEcdy59GBiL0Xjib8pt-J24rIQyMkbccZfDMy4wl6BJvh_VFXBvq-5PTiMKnyaERbdHmNaP4R-dK-F-SJ1vnqpsy1fc8XBy4W79KBBKCNMSFCT39RuoESRYuyMEXGjASyilZk"
                    />
                </div>
                <p class="font-label font-bold text-primary mb-1">Rp 88.000</p>
                <h3 class="font-headline font-black text-xl md:text-2xl text-primary mb-2 md:mb-4">Matcha Zen White</h3>
                <div class="flex justify-between items-center">
                    <p class="font-body text-sm text-primary/70">Ceremonial grade tea</p>
                    <a href="/catalog" class="w-10 h-10 bg-primary text-on-primary flex items-center justify-center neo-shadow active:translate-y-[2px] active:shadow-none shrink-0">
                        <span class="material-symbols-outlined">add</span>
                    </a>
                </div>
            </div>

        @endif
    </div>
</section>
