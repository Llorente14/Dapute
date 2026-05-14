<div
    class="p-6 md:p-10 lg:p-14 flex flex-col gap-10 animate-fade-up"
    x-data="{
        imagePreview: @js($existingImageUrl),
        fileName: null,
        fileSize: null,
        imageError: null,
        isUploading: false,
        confirmDelete: false,
        isActive: @js($is_active),
        handleImage(e) {
            this.imageError = null;
            const file = e.target.files[0];
            if (!file) { this.resetImage(); return; }
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                this.imageError = 'Format hanya JPG, PNG, atau WebP';
                this.resetPreviewState(); e.target.value = ''; return;
            }
            if (file.size > 2 * 1024 * 1024) {
                this.imageError = 'Ukuran gambar maksimal 2MB';
                this.resetPreviewState(); e.target.value = ''; return;
            }
            // Show local preview immediately
            this.fileName = file.name;
            const sizeKB = Math.round(file.size / 1024);
            this.fileSize = sizeKB >= 1024 ? (sizeKB / 1024).toFixed(1) + ' MB' : sizeKB + ' KB';
            const r = new FileReader();
            r.onload = ev => { this.imagePreview = ev.target.result; };
            r.readAsDataURL(file);
            // Upload to Livewire via JS API (bypasses wire:model conflict)
            this.isUploading = true;
            $wire.upload(
                'photo',
                file,
                () => { this.isUploading = false; },
                () => { this.isUploading = false; this.imageError = 'Upload gagal, coba lagi.'; this.resetPreviewState(); }
            );
        },
        resetPreviewState() { this.imagePreview = null; this.fileName = null; this.fileSize = null; },
        resetImage() {
            this.resetPreviewState(); this.imageError = null;
            if (this.$refs.imgInput) this.$refs.imgInput.value = '';
            $wire.set('photo', null);
        }
    }"
>

{{-- ══ FLASH MESSAGE ══════════════════════════════════════════════════════ --}}
@if (session()->has('success'))
<div class="border-[3px] border-[#012d1d] bg-[#d3ee6f] px-5 py-3 flex items-center gap-3 shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]">
    <span class="material-symbols-outlined text-[#012d1d]">check_circle</span>
    <p class="font-label font-bold text-sm text-[#012d1d] uppercase tracking-wide">{{ session('success') }}</p>
</div>
@endif

{{-- ══ PAGE HEADER ══════════════════════════════════════════════════════ --}}
<header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b-4 border-[#012d1d] pb-8 opacity-0 animate-fade-up [animation-delay:100ms]">
    <div class="flex flex-col gap-2">
        <p class="font-label text-xs uppercase tracking-[0.2em] text-[#414844]">
            {{ $isEditMode ? 'Edit Product' : 'Add Product' }}
        </p>
        <h2 class="font-headline font-black text-5xl md:text-6xl text-[#012d1d] leading-none tracking-tight">
            {{ $isEditMode ? 'Edit Product' : 'Add Product' }}
        </h2>
        <p class="font-body text-[#414844] max-w-lg">
            {{ $isEditMode ? 'Update product information, pricing, photos, and availability status.' : 'Fill in the details to add a new product to the catalog.' }}
        </p>
    </div>
    <a
        href="{{ route('admin.products.index') }}"
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
<form wire:submit="save" id="form-product" novalidate>
<div class="grid grid-cols-1 lg:grid-cols-[1fr_300px] gap-8 items-start">

    {{-- ── LEFT: Informasi Produk + Harga & Berat ──────────────────── --}}
    <div class="flex flex-col gap-6">

        {{-- SECTION: Informasi Produk --}}
        <section class="border-[3px] border-[#012d1d] bg-[#ffffff] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] opacity-0 animate-fade-up [animation-delay:200ms] hover:-translate-y-1 hover:shadow-[8px_8px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
            <div class="px-6 py-4 border-b-[3px] border-[#012d1d] bg-[#dde4e0]">
                <h3 class="font-label font-bold text-xs uppercase tracking-widest text-[#012d1d]">Product Information</h3>
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
                        wire:model="cake_name"
                        placeholder="Example: Premium Chocolate Brownie"
                        autocomplete="off"
                        class="w-full bg-[#eef5f1] border-[3px] border-[#012d1d] px-4 py-3
                               font-body text-sm text-[#414844] placeholder:text-[#717973]
                               focus:outline-none focus:bg-[#ffffff] focus:text-[#012d1d] focus:font-bold focus:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300
                               @error('cake_name') border-[#ba1a1a] @enderror"
                    >
                    @error('cake_name')
                        <p class="mt-1 font-label text-xs text-[#ba1a1a] font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description --}}
                <div class="group">
                    <label for="field-description" class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2 group-focus-within:text-[#012d1d] transition-colors">
                        Description
                    </label>
                    <textarea
                        id="field-description"
                        wire:model="description"
                        rows="4"
                        placeholder="Describe the ingredients, flavor, and what makes this product special..."
                        class="w-full bg-[#eef5f1] border-[3px] border-[#012d1d] px-4 py-3 resize-y
                               font-body text-sm text-[#414844] placeholder:text-[#717973]
                               focus:outline-none focus:bg-[#ffffff] focus:text-[#012d1d] focus:font-bold focus:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300
                               @error('description') border-[#ba1a1a] @enderror"
                    ></textarea>
                    @error('description')
                        <p class="mt-1 font-label text-xs text-[#ba1a1a] font-bold">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        {{-- SECTION: Harga & Berat --}}
        <section class="border-[3px] border-[#012d1d] bg-[#ffffff] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] opacity-0 animate-fade-up [animation-delay:300ms] hover:-translate-y-1 hover:shadow-[8px_8px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
            <div class="px-6 py-4 border-b-[3px] border-[#012d1d] bg-[#dde4e0]">
                <h3 class="font-label font-bold text-xs uppercase tracking-widest text-[#012d1d]">Pricing &amp; Weight</h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-5">

                {{-- Price --}}
                <div class="group">
                    <label for="field-price" class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2 group-focus-within:text-[#012d1d] transition-colors">
                        Price (Rp) <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <div class="flex border-[3px] border-[#012d1d] bg-[#eef5f1] focus-within:bg-[#ffffff] focus-within:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300 @error('price') border-[#ba1a1a] @enderror">
                        <span class="flex items-center px-3 bg-[#012d1d] text-[#d3ee6f] font-label font-bold text-sm select-none shrink-0 border-r-[3px] border-[#012d1d]">Rp</span>
                        <input
                            id="field-price"
                            type="number"
                            wire:model="price"
                            placeholder="Example: 85000"
                            min="0"
                            step="500"
                            class="flex-1 min-w-0 bg-transparent border-0 px-4 py-3 font-label text-right
                                   text-sm text-[#414844] focus:text-[#012d1d] focus:font-bold placeholder:text-[#717973] placeholder:font-normal
                                   focus:outline-none transition-colors
                                   [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        >
                    </div>
                    @error('price')
                        <p class="mt-1 font-label text-xs text-[#ba1a1a] font-bold">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Weight --}}
                <div class="group">
                    <label for="field-weight" class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2 group-focus-within:text-[#012d1d] transition-colors">
                        Weight <span class="text-[#ba1a1a]">*</span>
                    </label>
                    <div class="flex border-[3px] border-[#012d1d] bg-[#eef5f1] focus-within:bg-[#ffffff] focus-within:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300 @error('weight_grams') border-[#ba1a1a] @enderror">
                        <input
                            id="field-weight"
                            type="number"
                            wire:model="weight_grams"
                            placeholder="Example: 250"
                            min="1"
                            step="1"
                            class="flex-1 min-w-0 bg-transparent border-0 px-4 py-3 font-label
                                   text-sm text-[#414844] focus:text-[#012d1d] focus:font-bold placeholder:text-[#717973] placeholder:font-normal
                                   focus:outline-none transition-colors
                                   [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                        >
                        <span class="flex items-center px-3 bg-[#012d1d] text-[#d3ee6f] font-label font-bold text-sm select-none shrink-0 border-l-[3px] border-[#012d1d]">grams</span>
                    </div>
                    @error('weight_grams')
                        <p class="mt-1 font-label text-xs text-[#ba1a1a] font-bold">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>
    </div>

    {{-- ── RIGHT: Foto + Status + Actions ──────────────────────────── --}}
    <div class="flex flex-col gap-6">

        {{-- SECTION: Foto Produk --}}
        <section class="border-[3px] border-[#012d1d] bg-[#ffffff] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] opacity-0 animate-fade-up [animation-delay:400ms] hover:-translate-y-1 hover:shadow-[8px_8px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
            <div class="px-6 py-4 border-b-[3px] border-[#012d1d] bg-[#dde4e0]">
                <h3 class="font-label font-bold text-xs uppercase tracking-widest text-[#012d1d]">Product Photo</h3>
            </div>
            <div class="p-5 flex flex-col gap-3">

                {{-- Preview zone --}}
                <div
                    id="image-preview-zone"
                    class="relative w-full aspect-square overflow-hidden cursor-pointer
                           border-[3px] bg-[#eef5f1]
                           hover:border-[#012d1d] hover:bg-[#ffffff] hover:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] transition-all duration-300"
                    :class="imagePreview ? 'border-solid border-[#012d1d]' : 'border-dashed border-[#012d1d]'"
                    @click="$refs.imgInput.click()"
                    title="Click to select image"
                >
                    {{-- Empty state --}}
                    <div class="absolute inset-0 flex flex-col items-center justify-center gap-3 p-4" x-show="!imagePreview">
                        <div class="w-14 h-14 border-[3px] border-[#717973] bg-[#ffffff] flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] transition-transform">
                            <span class="material-symbols-outlined text-2xl text-[#717973]">image</span>
                        </div>
                        <div class="text-center">
                            <p class="font-label font-bold text-[#012d1d] text-xs uppercase tracking-widest">Click to upload image</p>
                            <p class="font-body text-[#414844] text-xs mt-0.5">JPG, PNG, WebP — max 2MB</p>
                        </div>
                    </div>

                    {{-- Preview --}}
                    <img
                        x-show="imagePreview"
                        :src="imagePreview"
                        alt="Product preview"
                        class="absolute inset-0 w-full h-full object-cover"
                    >

                    {{-- Remove Button (X) --}}
                    <button
                        type="button"
                        x-show="imagePreview"
                        x-cloak
                        @click.stop="resetImage()"
                        class="absolute top-2 right-2 w-8 h-8 bg-[#ffffff] border-[3px] border-[#012d1d] flex items-center justify-center text-[#012d1d] hover:bg-[#ba1a1a] hover:text-white hover:border-[#ba1a1a] transition-colors shadow-[2px_2px_0px_0px_rgba(1,45,29,1)]"
                        title="Remove Image"
                    >
                        <span class="material-symbols-outlined text-sm font-bold">close</span>
                    </button>
                </div>

                {{-- File Info and Errors below preview --}}
                <div class="mt-1" x-show="fileName || imageError || isUploading" x-cloak>
                    <p x-show="isUploading" class="font-label text-[#012d1d] text-xs font-bold flex items-center gap-1">
                        <svg class="animate-spin h-3 w-3 inline" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                        Uploading...
                    </p>
                    <p x-show="imageError" class="font-label text-[#ba1a1a] text-sm font-bold" x-text="imageError"></p>
                    <p x-show="fileName && !imageError && !isUploading" class="font-body text-[#414844] text-xs font-bold">
                        <span x-text="fileName"></span> &middot; <span x-text="fileSize"></span>
                    </p>
                </div>
                @error('photo')
                    <p class="font-label text-xs text-[#ba1a1a] font-bold">{{ $message }}</p>
                @enderror

                {{-- Hidden file input — NO wire:model, upload handled by $wire.upload() in handleImage() --}}
                <input
                    id="input-image"
                    type="file"
                    accept="image/jpeg,image/png,image/webp"
                    class="hidden"
                    x-ref="imgInput"
                    @change="handleImage($event)"
                >
            </div>
        </section>

        {{-- SECTION: Status Produk --}}
        <section class="border-[3px] border-[#012d1d] bg-[#ffffff] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)] opacity-0 animate-fade-up [animation-delay:500ms] hover:-translate-y-1 hover:shadow-[8px_8px_0px_0px_rgba(1,45,29,1)] transition-all duration-300">
            <div class="px-6 py-4 border-b-[3px] border-[#012d1d] bg-[#dde4e0]">
                <h3 class="font-label font-bold text-xs uppercase tracking-widest text-[#012d1d]">Product Status</h3>
            </div>
            <div class="px-6 py-5 flex items-center justify-between gap-4">
                <div>
                    <p class="font-label font-bold text-sm text-[#012d1d] uppercase tracking-wide">Show Product</p>
                    <p class="font-body text-xs text-[#414844] mt-0.5">
                        <span x-show="isActive">Visible to customers</span>
                        <span x-show="!isActive" x-cloak>Hidden from catalog</span>
                    </p>
                </div>

                {{-- Hidden checkbox: syncs Alpine isActive <-> Livewire is_active --}}
                <input
                    type="checkbox"
                    wire:model="is_active"
                    x-model="isActive"
                    class="hidden"
                >

                <button
                    id="toggle-is-active"
                    type="button"
                    @click="isActive = !isActive"
                    role="switch"
                    :aria-checked="isActive.toString()"
                    class="relative shrink-0 w-14 h-7 border-[3px] border-[#012d1d] shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] transition-all duration-300 focus:outline-none cursor-pointer hover:scale-105"
                    :style="isActive ? 'background:#d3ee6f;' : 'background:#dde4e0;'"
                >
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
                wire:loading.attr="disabled"
                wire:target="save"
                class="w-full flex items-center justify-center gap-2
                       bg-[#012d1d] text-white border-[3px] border-[#012d1d]
                       font-label font-bold text-sm uppercase tracking-wider px-6 py-4
                       hover:-translate-y-1 hover:-translate-x-1 hover:shadow-[6px_6px_0px_0px_rgba(1,45,29,1)]
                       transition-all duration-200 shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]
                       active:translate-y-0 active:translate-x-0 active:shadow-none
                       disabled:opacity-60 disabled:cursor-not-allowed disabled:translate-y-0 disabled:translate-x-0 disabled:shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]"
            >
                <span class="flex items-center gap-2" wire:loading.remove wire:target="save">
                    <span class="material-symbols-outlined text-lg">save</span>
                    {{ $isEditMode ? 'Save Changes' : 'Save Product' }}
                </span>
                <span class="flex items-center gap-2" wire:loading wire:target="save">
                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    Saving...
                </span>
            </button>

            {{-- DELETE (only in edit mode) --}}
            @if ($isEditMode)
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
            @endif

        </div>
    </div>

</div>
</form>

{{-- ══ DELETE CONFIRMATION DIALOG ═══════════════════════════════════════ --}}
@if ($isEditMode)
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
        <div class="w-12 h-12 border-[3px] border-[#ba1a1a] bg-[#ffdad6] flex items-center justify-center mb-5 shadow-[2px_2px_0px_0px_#ba1a1a]">
            <span class="material-symbols-outlined text-[#ba1a1a] text-xl">warning</span>
        </div>

        <h3 class="font-headline font-black text-[#012d1d] text-2xl uppercase tracking-wide mb-2">Delete Product?</h3>
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
                wire:click="delete"
                wire:loading.attr="disabled"
                @click="confirmDelete = false"
                class="flex-1 py-3 border-[3px] border-[#ba1a1a] bg-[#ba1a1a] text-white
                       font-label font-bold text-xs uppercase tracking-wider
                       hover:bg-[#93000a] hover:border-[#93000a] transition-all duration-200
                       hover:shadow-[4px_4px_0px_0px_#ba1a1a] hover:-translate-y-0.5
                       shadow-[2px_2px_0px_0px_#ba1a1a] flex items-center justify-center gap-1.5"
            >
                <span class="material-symbols-outlined text-sm">delete_forever</span>
                Yes, Delete
            </button>
        </div>
    </div>
</div>
@endif

</div>
