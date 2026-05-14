<div x-data="{
        showAddProduct: false,
        imagePreview: null,
        fileName: null,
        fileSize: null,
        imageError: null,
        isActive: true,
        name: '',
        description: '',
        price: '',
        weight_grams: '',
        handleImage(e) {
            this.imageError = null;
            const file = e.target.files[0];
            if (!file) return;
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                this.imageError = 'Format hanya JPG, PNG, atau WebP';
                e.target.value = ''; return;
            }
            if (file.size > 2 * 1024 * 1024) {
                this.imageError = 'Ukuran gambar maksimal 2MB';
                e.target.value = ''; return;
            }
            this.fileName = file.name;
            const sizeKB = Math.round(file.size / 1024);
            this.fileSize = sizeKB >= 1024 ? (sizeKB / 1024).toFixed(1) + ' MB' : sizeKB + ' KB';
            const r = new FileReader();
            r.onload = ev => { this.imagePreview = ev.target.result; };
            r.readAsDataURL(file);
        }
    }"
    x-init="$watch('showAddProduct', val => document.body.style.overflow = val ? 'hidden' : '')">

    <div class="p-6 md:p-12 lg:p-16 flex flex-col gap-12 animate-fade-up">

    {{-- ══ FLASH MESSAGE ══════════════════════════════════════════════════════ --}}
    @if (session()->has('success'))
    <div class="border-[3px] border-[#012d1d] bg-[#d3ee6f] px-5 py-3 flex items-center gap-3 shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]">
        <span class="material-symbols-outlined text-[#012d1d]">check_circle</span>
        <p class="font-label font-bold text-sm text-[#012d1d] uppercase tracking-wide">{{ session('success') }}</p>
    </div>
    @endif

    {{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
    <header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b-4 border-[#012d1d] pb-8 opacity-0 animate-fade-up [animation-delay:100ms]">
        <div class="flex flex-col gap-2 max-w-2xl">
            <h2 class="font-headline text-5xl md:text-6xl text-[#012d1d] font-black tracking-tight leading-none">Product Inventory</h2>
            <p class="font-body text-lg text-[#414844] max-w-xl">Manage your bakery's active cookie selection, adjust pricing, and control availability across all Dapute storefronts.</p>
        </div>
        <a
            href="{{ route('admin.products.create') }}"
            class="bg-[#012d1d] text-[#ffffff] border-[3px] border-[#012d1d] font-label uppercase text-sm px-6 py-3 tracking-wider
                   hover:-translate-y-1 hover:-translate-x-1 hover:shadow-[6px_6px_0px_0px_rgba(1,45,29,1)] transition-all duration-300
                   shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] active:translate-y-0 active:translate-x-0 active:shadow-none
                   flex items-center gap-2"
        >
            <span class="material-symbols-outlined text-lg">add</span>
            Add New Product
        </a>
    </header>

    {{-- ══ TABLE ═══════════════════════════════════════════════════════════ --}}
    <section class="flex flex-col gap-8 md:gap-0 md:grid md:grid-cols-1 md:border-t-[3px] md:border-l-[3px] border-[#012d1d] bg-transparent md:bg-[#012d1d] opacity-0 animate-fade-up [animation-delay:200ms] shadow-none md:shadow-[8px_8px_0px_0px_rgba(1,45,29,1)]">

        {{-- Table Header (Desktop) --}}
        <div class="hidden md:grid grid-cols-12 gap-0 border-b-[3px] border-r-[3px] border-[#012d1d] bg-[#dde4e0]">
            <div class="col-span-1 p-4 border-r-[3px] border-[#012d1d] flex items-center justify-center">
                <span class="font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Img</span>
            </div>
            <div class="col-span-4 p-4 border-r-[3px] border-[#012d1d] flex items-center">
                <span class="font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Product Name &amp; Desc</span>
            </div>
            <div class="col-span-2 p-4 border-r-[3px] border-[#012d1d] flex items-center justify-end">
                <span class="font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Price</span>
            </div>
            <div class="col-span-2 p-4 border-r-[3px] border-[#012d1d] flex items-center justify-end">
                <span class="font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Weight</span>
            </div>
            <div class="col-span-1 p-4 border-r-[3px] border-[#012d1d] flex items-center justify-center">
                <span class="font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Status</span>
            </div>
            <div class="col-span-2 p-4 flex items-center justify-center">
                <span class="font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Actions</span>
            </div>
        </div>

        {{-- ══ Product Rows from Database ══ --}}
        @forelse ($products as $product)
        <div class="grid grid-cols-1 md:grid-cols-12 gap-0 border-[3px] md:border-t-0 md:border-l-0 border-[#012d1d]
                    {{ $product->is_active ? 'bg-[#f4fbf7] hover:bg-[#d8e2dc]' : 'bg-[#dde4e0] opacity-75 hover:opacity-100' }}
                    transition-colors duration-300 group shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] md:shadow-none">

            {{-- Image --}}
            <div class="md:col-span-1 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex items-center justify-center bg-[#eef5f1] group-hover:bg-transparent transition-colors">
                @if ($product->image_url)
                    <img
                        alt="{{ $product->name }}"
                        src="{{ $product->image_url }}"
                        class="w-16 h-16 object-cover border-[3px] border-[#012d1d] aspect-square shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] group-hover:scale-110 transition-transform duration-300 {{ !$product->is_active ? 'grayscale group-hover:grayscale-0' : '' }}"
                    >
                @else
                    <div class="w-16 h-16 bg-[#f4fbf7] border-[3px] border-[#012d1d] flex items-center justify-center aspect-square shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] {{ !$product->is_active ? 'grayscale group-hover:grayscale-0' : '' }} group-hover:scale-110 transition-all duration-300">
                        <span class="material-symbols-outlined text-[#717973]">image</span>
                    </div>
                @endif
            </div>

            {{-- Name & Description --}}
            <div class="md:col-span-4 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex flex-col justify-center gap-1 group-hover:pl-6 transition-all duration-300">
                <h3 class="font-headline font-black text-xl text-[#012d1d] leading-tight {{ !$product->is_active ? 'line-through decoration-2 decoration-[#717973]' : '' }}">
                    {{ $product->cake_name }}
                </h3>
                @if ($product->description)
                    <p class="font-body text-sm text-[#414844] line-clamp-2">{{ $product->description }}</p>
                @endif
            </div>

            {{-- Price --}}
            <div class="md:col-span-2 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex items-center md:justify-end">
                <div class="flex items-center gap-1">
                    <span class="font-label text-xs font-bold text-[#414844]">Rp</span>
                    <span class="font-label font-bold text-sm text-[#012d1d]">{{ number_format($product->price, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Weight --}}
            <div class="md:col-span-2 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex items-center md:justify-end">
                <span class="font-label font-bold text-sm text-[#012d1d]">{{ $product->weight_grams }} g</span>
            </div>

            {{-- Status --}}
            <div class="md:col-span-1 p-4 border-b-[3px] md:border-b-0 md:border-r-[3px] border-[#012d1d] flex items-center justify-between md:justify-center">
                <span class="md:hidden font-label text-xs uppercase tracking-widest text-[#161d1b] font-bold">Status</span>
                <span class="font-label font-bold text-xs uppercase tracking-wider {{ $product->is_active ? 'text-[#012d1d]' : 'text-[#717973]' }}">
                    {{ $product->is_active ? 'Active' : 'Draft' }}
                </span>
            </div>

            {{-- Actions --}}
            <div class="md:col-span-2 p-4 flex items-center justify-end md:justify-center gap-2">
                <a
                    href="{{ route('admin.products.edit', $product->id) }}"
                    aria-label="Edit"
                    class="bg-[#ffffff] border-[3px] border-[#012d1d] p-2 text-[#012d1d]
                           hover:bg-[#012d1d] hover:text-[#ffffff] transition-all duration-300
                           flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(1,45,29,1)]
                           hover:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] hover:-translate-y-0.5
                           active:translate-y-0 active:translate-x-0 active:shadow-none"
                >
                    <span class="material-symbols-outlined text-sm">edit</span>
                </a>
            </div>
        </div>
        @empty
        {{-- ══ EMPTY STATE ══ --}}
        <div class="border-[3px] md:border-t-0 md:border-l-0 border-[#012d1d] bg-[#f4fbf7] p-16 flex flex-col items-center justify-center gap-6 text-center">
            <div class="w-20 h-20 border-[3px] border-[#012d1d] bg-[#eef5f1] flex items-center justify-center shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]">
                <span class="material-symbols-outlined text-4xl text-[#717973]">inventory_2</span>
            </div>
            <div>
                <h3 class="font-headline font-black text-2xl text-[#012d1d] tracking-tight">No Products Yet</h3>
                <p class="font-body text-[#414844] mt-2 max-w-xs">
                    Your inventory is empty. Add your first product to get started.
                </p>
            </div>
            <a
                href="{{ route('admin.products.create') }}"
                class="flex items-center gap-2 bg-[#012d1d] text-white border-[3px] border-[#012d1d]
                       font-label font-bold text-xs uppercase tracking-wider px-6 py-3
                       hover:-translate-y-1 hover:-translate-x-1 hover:shadow-[6px_6px_0px_0px_rgba(1,45,29,1)]
                       transition-all duration-200 shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]"
            >
                <span class="material-symbols-outlined text-base">add</span>
                Add First Product
            </a>
        </div>
        @endforelse

        {{-- Footer / Count --}}
        @if ($products->isNotEmpty())
        <div class="flex items-center justify-between p-4 border-[3px] md:border-t-0 md:border-l-0 border-[#012d1d] bg-[#d8e2dc] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] md:shadow-none">
            <p class="font-label text-xs text-[#5b6560] uppercase tracking-widest font-bold w-full text-center">
                Showing {{ $products->count() }} {{ Str::plural('Product', $products->count()) }}
            </p>
        </div>
        @endif

    </section>
    </div>
</div>
