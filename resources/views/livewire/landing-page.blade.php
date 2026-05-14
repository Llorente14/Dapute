<section class="py-12 md:py-20 px-4 md:px-8 max-w-[1200px] mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 md:mb-16 gap-4 md:gap-8">
        <div>
            <span class="font-label text-tertiary font-bold tracking-widest uppercase text-xs md:text-base">Our Collection</span>
            <h2 class="font-headline font-black text-3xl md:text-5xl text-primary mt-2">Dapute Signature</h2>
        </div>
        <a href="/catalog" class="bg-surface text-primary font-label px-6 py-3 border-[3px] border-primary font-bold hover:bg-secondary-container transition-all inline-block">
            VIEW FULL CATALOG →
        </a>
    </div>

    {{-- Product Filter Tabs Section --}}
    <div class="flex flex-wrap gap-4 mb-10">
        @foreach($filterOptions as $key => $label)
            <button 
                wire:click="setFilter('{{ $key }}')"
                wire:loading.attr="disabled"
                class="px-6 py-2 font-label font-bold transition-all duration-300 disabled:opacity-50 disabled:cursor-not-allowed {{ $activeFilter === $key ? 'bg-primary text-on-primary border-[3px] border-primary neo-shadow' : 'bg-transparent text-primary border-[3px] border-primary hover:bg-secondary-container' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Product Cards --}}
    <div class="relative min-h-[400px]">
        {{-- Loading Spinner Overlay --}}
        <div wire:loading.flex wire:target="setFilter" class="absolute inset-0 bg-[#f4fbf7]/80 z-10 items-center justify-center">
            <div class="animate-spin rounded-full h-12 w-12 border-4 border-[#012d1d] border-t-transparent"></div>
        </div>

        @if(count($this->filteredProducts) > 0)
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-8 product-grid">
                @foreach($this->filteredProducts as $product)
                    <div>
                        <div class="group bg-surface-container-lowest border-[3px] border-primary neo-shadow p-4 md:p-6 transition-all hover:translate-x-[-2px] hover:translate-y-[-2px] hover:shadow-[6px_6px_0px_0px_#012d1d] h-full flex flex-col relative">
                            
                            {{-- Badge --}}
                            @if($product['is_active'])
                                <div class="absolute top-6 left-6 z-10 px-2 py-1 text-[10px] font-label font-bold border-[3px] border-primary bg-[#D4EF70]">
                                    AKTIF
                                </div>
                            @endif
                            
                            <a href="/catalog/{{ $product['id'] }}" class="aspect-square mb-4 md:mb-6 border-[3px] border-primary overflow-hidden block">
                                <img
                                    alt="{{ $product['cake_name'] }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                                    src="{{ $product['image_url'] }}"
                                    onerror="this.src='https://placehold.co/400x400/012d1d/D4EF70?text=DAPUTE'"
                                />
                            </a>
                            <p class="font-label font-bold text-primary mb-1">{{ $product['price'] }}</p>
                            <a href="/catalog/{{ $product['id'] }}">
                                <h3 class="font-headline font-black text-xl md:text-2xl text-primary mb-2 md:mb-4 hover:text-secondary-container transition-colors">{{ $product['cake_name'] }}</h3>
                            </a>
                            <div class="flex justify-between items-center mt-auto gap-2">
                                <p class="font-body text-sm text-primary/70 line-clamp-2">{{ $product['description'] }}</p>
                                
                                {{-- Add to Cart Button --}}
                                <button 
                                    wire:click="addToCart('{{ $product['id'] }}')"
                                    wire:loading.attr="disabled"
                                    title="Tambah ke Keranjang"
                                    class="w-10 h-10 bg-primary text-on-primary flex items-center justify-center neo-shadow shrink-0 transition-all hover:bg-[#023d28] active:translate-x-[2px] active:translate-y-[2px] active:shadow-none relative disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span wire:loading.remove wire:target="addToCart('{{ $product['id'] }}')" class="material-symbols-outlined">add</span>
                                    <svg wire:loading wire:target="addToCart('{{ $product['id'] }}')" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-20 text-primary/60 font-body">
                Tidak ada produk untuk filter ini.
            </div>
        @endif
    </div>
</section>
