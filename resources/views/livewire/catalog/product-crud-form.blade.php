<div
    class="p-6 md:p-10 lg:p-14 flex flex-col gap-10 animate-fade-up"
    x-data="{
        imagePreview: null,
        confirmDelete: false,
        isActive: true,
        handleImage(e) {
            const file = e.target.files[0];
            if (!file) return;
            const r = new FileReader();
            r.onload = ev => { this.imagePreview = ev.target.result; };
            r.readAsDataURL(file);
        }
    }"
>

{{-- ══ PAGE HEADER ══════════════════════════════════════════════════════ --}}
<header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b-4 border-[#012d1d] pb-8 opacity-0 animate-fade-up [animation-delay:100ms]">
    <div class="flex flex-col gap-2">
        <p class="font-label text-xs uppercase tracking-[0.2em] text-[#414844]">
            Edit Product — #1
        </p>
        <h2 class="font-headline font-black text-5xl md:text-6xl text-[#012d1d] leading-none tracking-tight">
            Edit Product
        </h2>
        <p class="font-body text-[#414844] max-w-lg">
            Update product information, pricing, photos, and availability status.
        </p>
    </div>
    <a
        href="#"
        id="btn-back"
        class="flex items-center gap-2 bg-[#f4fbf7] border-[3px] border-[#012d1d] text-[#012d1d]
               font-label font-bold text-xs uppercase tracking-wider px-5 py-3
               hover:-translate-y-0.5 hover:-translate-x-0.5 hover:shadow-[6px_6px_0px_0px_rgba(1,45,29,1)]
               transition-all shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]
               active:translate-y-0 active:translate-x-0 active:shadow-none"
    >
        <span class="material-symbols-outlined text-base">arrow_back</span>
        Back
    </a>
</header>

{{-- ══ FORM ════════════════════════════════════════════════════════════ --}}
<form id="form-product" novalidate onsubmit="event.preventDefault(); alert('Form submitted! (Static View)');">
<div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-8 items-start">

    {{-- ── LEFT: Informasi Produk + Harga & Berat ──────────────────── --}}
    <div class="flex flex-col gap-6">

        {{-- SECTION: Informasi Produk --}}
        <section class="border-[3px] border-[#012d1d] bg-[#ffffff] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] opacity-0 animate-fade-up [animation-delay:200ms] hover:-translate-y-1 hover:shadow-[8px_8px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
            <div class="px-6 py-4 border-b-[3px] border-[#012d1d] bg-[#dde4e0]">
                <h3 class="font-label font-bold text-xs uppercase tracking-widest text-[#012d1d]">
                    Product Information
                </h3>
            </div>
            <div class="p-6 flex flex-col gap-5">

                {{-- Cake Name --}}
                <div class="group">
                    <label for="field-cake-name" class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2 group-focus-within:text-[#012d1d] transition-colors">
                        Cake Name <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <input
                        id="field-cake-name"
                        type="text"
                        value="Sea Salt Chocolate Chunk"
                        placeholder="Example: Premium Chocolate Brownie"
                        autocomplete="off"
                        class="w-full bg-[#eef5f1] border-[3px] border-[#012d1d] px-4 py-3
                               font-body text-sm text-[#414844] placeholder:text-[#717973]
                               focus:outline-none focus:bg-[#ffffff] focus:text-[#012d1d] focus:font-bold focus:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300"
                    >
                </div>

                {{-- Description --}}
                <div class="group">
                    <label for="field-description" class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2 group-focus-within:text-[#012d1d] transition-colors">
                        Description <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <textarea
                        id="field-description"
                        rows="4"
                        placeholder="Describe the ingredients, flavor, and what makes this product special..."
                        class="w-full bg-[#eef5f1] border-[3px] border-[#012d1d] px-4 py-3 resize-y
                               font-body text-sm text-[#414844] placeholder:text-[#717973]
                               focus:outline-none focus:bg-[#ffffff] focus:text-[#012d1d] focus:font-bold focus:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300"
                    >Signature dark chocolate chunk cookie with flaky Maldon sea salt. Made from 72% Ghana chocolate and premium butter.</textarea>
                </div>
            </div>
        </section>

        {{-- SECTION: Harga & Berat --}}
        <section class="border-[3px] border-[#012d1d] bg-[#ffffff] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] opacity-0 animate-fade-up [animation-delay:300ms] hover:-translate-y-1 hover:shadow-[8px_8px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
            <div class="px-6 py-4 border-b-[3px] border-[#012d1d] bg-[#dde4e0]">
                <h3 class="font-label font-bold text-xs uppercase tracking-widest text-[#012d1d]">
                    Pricing &amp; Weight
                </h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- Price --}}
                <div class="group">
                    <label for="field-price" class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2 group-focus-within:text-[#012d1d] transition-colors">
                        Price (Rp) <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <div class="flex border-[3px] border-[#012d1d] bg-[#eef5f1] focus-within:bg-[#ffffff] focus-within:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
                        <span class="flex items-center px-3 bg-[#012d1d] text-[#d3ee6f] font-label font-bold text-sm select-none shrink-0 border-r-[3px] border-[#012d1d]">
                            Rp
                        </span>
                        <input
                            id="field-price"
                            type="number"
                            value="85000"
                            placeholder="Example: 85000"
                            min="0"
                            step="500"
                            class="flex-1 min-w-0 bg-transparent border-0 px-4 py-3 font-label text-right
                                   text-sm text-[#414844] focus:text-[#012d1d] focus:font-bold placeholder:text-[#717973] placeholder:font-normal
                                   focus:outline-none transition-colors
                                   [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        >
                    </div>
                </div>

                {{-- Weight --}}
                <div class="group">
                    <label for="field-weight" class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2 group-focus-within:text-[#012d1d] transition-colors">
                        Weight <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <div class="flex border-[3px] border-[#012d1d] bg-[#eef5f1] focus-within:bg-[#ffffff] focus-within:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
                        <input
                            id="field-weight"
                            type="number"
                            value="120"
                            placeholder="Example: 250"
                            min="1"
                            step="1"
                            class="flex-1 min-w-0 bg-transparent border-0 px-4 py-3 font-label
                                   text-sm text-[#414844] focus:text-[#012d1d] focus:font-bold placeholder:text-[#717973] placeholder:font-normal
                                   focus:outline-none transition-colors
                                   [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        >
                        <span class="flex items-center px-3 bg-[#012d1d] text-[#d3ee6f] font-label font-bold text-sm select-none shrink-0 border-l-[3px] border-[#012d1d]">
                            grams
                        </span>
                    </div>
                </div>
            </div>
        </section>
    </div>

    {{-- ── RIGHT: Foto + Status + Actions ──────────────────────────── --}}
    <div class="flex flex-col gap-6">

        {{-- SECTION: Foto Produk --}}
        <section class="border-[3px] border-[#012d1d] bg-[#ffffff] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] opacity-0 animate-fade-up [animation-delay:400ms] hover:-translate-y-1 hover:shadow-[8px_8px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
            <div class="px-6 py-4 border-b-[3px] border-[#012d1d] bg-[#dde4e0]">
                <h3 class="font-label font-bold text-xs uppercase tracking-widest text-[#012d1d]">
                    Product Photo
                </h3>
            </div>
            <div class="p-5 flex flex-col gap-3">

                {{-- Preview zone — dashed border --}}
                <div
                    id="image-preview-zone"
                    class="relative w-full aspect-square overflow-hidden cursor-pointer
                           border-[3px] border-dashed border-[#012d1d] bg-[#eef5f1]
                           hover:border-[#012d1d] hover:border-solid hover:bg-[#ffffff] hover:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300"
                    @click="$refs.imgInput.click()"
                    title="Click to select image"
                >
                    {{-- Empty state --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-3 p-4"
                         x-show="!imagePreview">
                        <div class="w-14 h-14 border-[3px] border-[#717973] bg-[#ffffff] flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] group-hover:border-[#012d1d] group-hover:scale-110 transition-transform">
                            <span class="material-symbols-outlined text-2xl text-[#717973]">image</span>
                        </div>
                        <div class="text-center">
                            <p class="font-label font-bold text-[#012d1d] text-xs uppercase tracking-widest">Click to upload</p>
                            <p class="font-body text-[#414844] text-xs mt-0.5">JPG, PNG, WebP — max 2MB</p>
                        </div>
                    </div>

                    {{-- Preview --}}
                    <img
                        x-show="imagePreview"
                        :src="imagePreview"
                        alt="Product preview"
                        class="absolute inset-0 w-full h-full object-cover border-[3px] border-[#012d1d]"
                    >

                    {{-- Change overlay --}}
                    <div class="absolute inset-0 bg-[#012d1d]/80 flex flex-col items-center justify-center gap-2
                                opacity-0 hover:opacity-100 transition-opacity duration-200"
                         x-show="imagePreview">
                        <span class="material-symbols-outlined text-3xl text-white">photo_camera</span>
                        <span class="font-label font-bold text-white text-xs uppercase tracking-widest">Change Photo</span>
                    </div>
                </div>

                {{-- Hidden file input --}}
                <input
                    id="input-image"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    class="hidden"
                    x-ref="imgInput"
                    @change="handleImage($event)"
                >

                <button
                    id="btn-clear-image"
                    type="button"
                    x-show="imagePreview"
                    x-cloak
                    @click.stop="imagePreview = null; $refs.imgInput.value = ''"
                    class="w-full py-2.5 border-[3px] border-[#012d1d] bg-[#f4fbf7] text-[#012d1d]
                           font-label font-bold text-xs uppercase tracking-wider
                           hover:bg-[#dde4e0] transition-colors flex items-center justify-center gap-2"
                >
                    <span class="material-symbols-outlined text-sm">delete</span>
                    Remove Photo
                </button>
            </div>
        </section>

        {{-- SECTION: Status Produk --}}
        <section class="border-[3px] border-[#012d1d] bg-[#ffffff] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] opacity-0 animate-fade-up [animation-delay:500ms] hover:-translate-y-1 hover:shadow-[8px_8px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
            <div class="px-6 py-4 border-b-[3px] border-[#012d1d] bg-[#dde4e0]">
                <h3 class="font-label font-bold text-xs uppercase tracking-widest text-[#012d1d]">
                    Product Status
                </h3>
            </div>
            <div class="px-6 py-5 flex items-center justify-between gap-4">
                <div>
                    <p class="font-label font-bold text-sm text-[#012d1d] uppercase tracking-wide">
                        Show Product
                    </p>
                    <p class="font-body text-xs text-[#414844] mt-0.5">
                        <span x-show="isActive">Visible to customers</span>
                        <span x-show="!isActive" x-cloak>Hidden from catalog</span>
                    </p>
                </div>

                {{-- Brutalist toggle --}}
                <button
                    id="toggle-is-active"
                    type="button"
                    @click="isActive = !isActive"
                    role="switch"
                    :aria-checked="isActive.toString()"
                    class="relative shrink-0 w-14 h-7 border-[3px] border-[#012d1d] shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] transition-all duration-300 focus:outline-none cursor-pointer hover:scale-105"
                    :style="isActive ? 'background:#d3ee6f;' : 'background:#dde4e0;'"
                >
                    {{-- Thumb --}}
                    <span
                        class="absolute top-[2px] w-5 h-5 border-[3px] border-[#012d1d] transition-all duration-300 pointer-events-none"
                        :style="isActive ? 'left:calc(100% - 1.375rem); background:#012d1d;' : 'left:1px; background:#717973;'"
                    ></span>
                </button>
            </div>
        </section>

        {{-- ACTIONS --}}
        <div class="flex flex-col gap-3 opacity-0 animate-fade-up [animation-delay:600ms]">

            {{-- SAVE --}}
            <button
                id="btn-save-product"
                type="submit"
                form="form-product"
                class="w-full flex items-center justify-center gap-2
                       bg-[#012d1d] text-white border-[3px] border-[#012d1d]
                       font-label font-bold text-sm uppercase tracking-wider px-6 py-4
                       hover:-translate-y-1 hover:-translate-x-1 hover:shadow-[6px_6px_0px_0px_rgba(1,45,29,1)]
                       transition-all duration-200 shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]
                       active:translate-y-0 active:translate-x-0 active:shadow-none"
            >
                <span class="flex items-center gap-2">
                    <span class="material-symbols-outlined text-lg">save</span>
                    Save Changes
                </span>
            </button>

            {{-- DELETE --}}
            <button
                id="btn-delete-product"
                type="button"
                @click="confirmDelete = true"
                class="w-full flex items-center justify-center gap-2
                       bg-[#ffffff] text-[#ba1a1a] border-[3px] border-[#ba1a1a]
                       font-label font-bold text-sm uppercase tracking-wider px-6 py-3.5
                       hover:bg-[#ba1a1a] hover:text-white
                       transition-all duration-200 shadow-[4px_4px_0px_0px_#ba1a1a]
                       hover:-translate-y-1 hover:-translate-x-1 hover:shadow-[6px_6px_0px_0px_#ba1a1a]
                       active:translate-y-0 active:translate-x-0 active:shadow-none"
            >
                <span class="material-symbols-outlined text-base">delete</span>
                Delete Product
            </button>

        </div>
    </div>

</div>
</form>

{{-- ══ DELETE CONFIRMATION DIALOG ═══════════════════════════════════════ --}}
<div
    x-show="confirmDelete"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4 transition-opacity duration-300"
    style="background:rgba(1,45,29,0.82);"
    x-transition:enter="ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    @keydown.escape.window="confirmDelete = false"
>
    <div
        class="border-[3px] border-[#012d1d] bg-[#ffffff] w-full max-w-md p-8 shadow-[8px_8px_0px_0px_rgba(1,45,29,1)]"
        @click.stop
        x-transition:enter="transition ease-out duration-300 delay-100"
        x-transition:enter-start="opacity-0 scale-90 translate-y-8"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
    >
        <div class="w-12 h-12 border-[3px] border-[#ba1a1a] bg-[#ffdad6] flex items-center justify-center mb-5 shadow-[2px_2px_0px_0px_#ba1a1a] animate-pop-in">
            <span class="material-symbols-outlined text-[#ba1a1a] text-xl">warning</span>
        </div>

        <h3 class="font-headline font-black text-[#012d1d] text-2xl uppercase tracking-wide mb-2">
            Delete Product?
        </h3>
        <p class="font-body text-[#414844] text-sm leading-relaxed mb-6">
            This action <strong class="text-[#012d1d]">cannot be undone</strong>.
            The product will be permanently removed from all Dapute storefronts.
        </p>

        <div class="flex gap-3">
            <button
                id="btn-cancel-delete"
                type="button"
                @click="confirmDelete = false"
                class="flex-1 py-3 border-[3px] border-[#012d1d] bg-[#f4fbf7] text-[#012d1d]
                       font-label font-bold text-xs uppercase tracking-wider
                       hover:bg-[#dde4e0] hover:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] hover:-translate-y-0.5 transition-all duration-200 shadow-[2px_2px_0px_0px_rgba(1,45,29,1)]"
            >
                Cancel
            </button>
            <button
                id="btn-confirm-delete"
                type="button"
                @click="confirmDelete = false; alert('Delete action triggered! (Static View)');"
                class="flex-1 py-3 border-[3px] border-[#ba1a1a] bg-[#ba1a1a] text-white
                       font-label font-bold text-xs uppercase tracking-wider
                       hover:bg-[#93000a] hover:border-[#93000a] transition-all duration-200
                       hover:shadow-[4px_4px_0px_0px_#ba1a1a] hover:-translate-y-0.5
                       shadow-[2px_2px_0px_0px_#ba1a1a] flex items-center justify-center gap-1.5"
            >
                <span class="flex items-center gap-1.5">
                    <span class="material-symbols-outlined text-sm">delete_forever</span>
                    Yes, Delete
                </span>
            </button>
        </div>
    </div>
</div>

</div>
