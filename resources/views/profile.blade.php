<div>

<style>
    /* ── Page Load: staggered fade-in slide-up ── */
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .anim-in {
        opacity: 0;
        animation: fadeUp 0.5s ease forwards;
    }
    .anim-in.d1 { animation-delay: 0.05s; }
    .anim-in.d2 { animation-delay: 0.15s; }
    .anim-in.d3 { animation-delay: 0.25s; }
    .anim-in.d4 { animation-delay: 0.35s; }

    /* ── Input focus: block lifts up ── */
    .input-wrap {
        transition: transform 0.2s ease;
    }
    .input-wrap:focus-within {
        transform: translate(-2px, -2px);
    }

    /* ── Input focus: label shifts right & darkens ── */
    .input-wrap label {
        transition: transform 0.2s ease, color 0.2s ease, letter-spacing 0.2s ease;
    }
    .input-wrap:focus-within label {
        transform: translateX(4px);
        color: #012d1d;
        letter-spacing: 0.15em;
    }

    /* ── Address cards: subtle lift on hover ── */
    .address-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .address-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 16px rgba(1, 45, 29, 0.12);
    }

    /* ── Order items: subtle scale on hover ── */
    .order-item {
        transition: transform 0.15s ease;
    }
    .order-item:hover {
        transform: scale(1.015);
    }

    /* ── Staggered order items on load ── */
    .order-item:nth-child(1) { animation-delay: 0.3s; }
    .order-item:nth-child(2) { animation-delay: 0.4s; }
    .order-item:nth-child(3) { animation-delay: 0.5s; }

    /* ── Modal transitions ── */
    .modal-backdrop { transition: opacity 0.2s ease; }
    .modal-panel { transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.2s ease; }
    .modal-panel.entering { transform: scale(0.95) translateY(10px); opacity: 0; }

    /* ── Error states ── */
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        20% { transform: translateX(-4px); }
        40% { transform: translateX(4px); }
        60% { transform: translateX(-3px); }
        80% { transform: translateX(2px); }
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    .input-error input {
        border-color: #ba1a1a !important;
        animation: shake 0.4s ease;
    }
    .input-error label {
        color: #ba1a1a !important;
    }
    .error-msg {
        animation: slideDown 0.3s ease forwards;
    }
</style>

{{-- ═══ Toast Notification ═══ --}}
@if($showToast)
<div
    wire:poll.3s="dismissToast"
    class="fixed top-6 right-6 z-[200] flex items-center gap-3 neo-border px-6 py-4 neo-shadow font-label font-bold uppercase tracking-wider
    {{ $toastType === 'success' ? 'bg-primary text-on-primary' : 'bg-error text-on-error' }}"
    style="animation: fadeUp 0.4s ease forwards;"
>
    <span class="material-symbols-outlined text-xl">
        {{ $toastType === 'success' ? 'check_circle' : 'error' }}
    </span>
    <span>{{ $toastMessage }}</span>
    <button wire:click="dismissToast" class="ml-4 hover:opacity-70 transition-opacity cursor-pointer">
        <span class="material-symbols-outlined text-lg">close</span>
    </button>
</div>
@endif

<!-- Main Content Canvas -->
<div class="flex-1 w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

    <!-- Header Section -->
    <header class="anim-in d1 mb-12 border-b-[3px] border-primary pb-6 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="font-headline text-5xl md:text-7xl font-black tracking-tighter text-primary uppercase leading-none">Profile</h1>
            <p class="font-body text-xl text-on-surface-variant mt-4 max-w-2xl">Manage your greenhouse details, structural addresses, and review recent botanical acquisitions.</p>
        </div>
        <form action="/logout" method="POST" class="self-start md:self-auto">
            @csrf
            <button type="submit" class="neo-border bg-tertiary-fixed text-primary font-label font-bold uppercase px-6 py-3 neo-shadow neo-shadow-hover transition-all flex items-center gap-2">
                <span class="material-symbols-outlined">logout</span>
                Sign Out
            </button>
        </form>
    </header>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">

        <!-- Left Column: Personal Info & Addresses -->
        <div class="lg:col-span-7 flex flex-col gap-12">

            <!-- Personal Information -->
            <section class="anim-in d2">
                <div class="flex items-center gap-4 mb-6">
                    <span class="material-symbols-outlined text-3xl text-primary">person</span>
                    <h2 class="font-headline text-3xl font-bold tracking-tight text-primary uppercase">Personal Info</h2>
                </div>
                <div class="bg-surface-container-lowest neo-border p-6 md:p-8 neo-shadow flex flex-col gap-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- First Name --}}
                        <div class="flex flex-col gap-2 input-wrap transition-colors p-2 -m-2 relative pb-6 {{ $errors->has('first_name') ? 'input-error' : '' }}">
                            <label class="font-label text-sm font-bold text-on-surface-variant uppercase tracking-wider">First Name</label>
                            <input wire:model="first_name" name="first_name" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-lg font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold transition-colors" type="text" placeholder="First name"/>
                            @error('first_name')
                                <p class="error-msg absolute bottom-0 left-0 flex items-center gap-1 font-body text-[12px] text-error font-semibold">
                                    <span class="material-symbols-outlined text-[14px]">cancel</span> {{ $message }}
                                </p>
                            @enderror
                        </div>
                        {{-- Last Name --}}
                        <div class="flex flex-col gap-2 input-wrap transition-colors p-2 -m-2 relative pb-6 {{ $errors->has('last_name') ? 'input-error' : '' }}">
                            <label class="font-label text-sm font-bold text-on-surface-variant uppercase tracking-wider">Last Name</label>
                            <input wire:model="last_name" name="last_name" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-lg font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold transition-colors" type="text" placeholder="Last name"/>
                            @error('last_name')
                                <p class="error-msg absolute bottom-0 left-0 flex items-center gap-1 font-body text-[12px] text-error font-semibold">
                                    <span class="material-symbols-outlined text-[14px]">cancel</span> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                    {{-- Email Address (read-only) --}}
                    <div class="flex flex-col gap-2 p-2 -m-2">
                        <label class="font-label text-sm font-bold text-on-surface-variant uppercase tracking-wider">Email Address</label>
                        <input wire:model="email" class="w-full bg-transparent border-0 border-b-[3px] border-outline pb-2 text-lg font-body focus:outline-none focus:ring-0 text-on-surface-variant font-semibold cursor-not-allowed" type="email" readonly disabled/>
                    </div>
                    {{-- Phone Number --}}
                    <div class="flex flex-col gap-2 input-wrap transition-colors p-2 -m-2 relative pb-6 {{ $errors->has('phone_number') ? 'input-error' : '' }}">
                        <label class="font-label text-sm font-bold text-on-surface-variant uppercase tracking-wider">Phone Number</label>
                        <input wire:model="phone_number" name="phone_number" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-lg font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold transition-colors" type="tel" placeholder="+62 8..."/>
                        @error('phone_number')
                            <p class="error-msg absolute bottom-0 left-0 flex items-center gap-1 font-body text-[12px] text-error font-semibold">
                                <span class="material-symbols-outlined text-[14px]">cancel</span> {{ $message }}
                            </p>
                        @enderror
                    </div>
                    {{-- Address (Alamat lengkap untuk Biteship) --}}
                    <div class="flex flex-col gap-2 input-wrap transition-colors p-2 -m-2 relative pb-6 {{ $errors->has('address') ? 'input-error' : '' }}">
                        <label class="font-label text-sm font-bold text-on-surface-variant uppercase tracking-wider">Alamat Lengkap</label>
                        <textarea wire:model="address" name="address" rows="3" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-lg font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold transition-colors resize-none" placeholder="Alamat lengkap untuk pengiriman..."></textarea>
                        @error('address')
                            <p class="error-msg absolute bottom-0 left-0 flex items-center gap-1 font-body text-[12px] text-error font-semibold">
                                <span class="material-symbols-outlined text-[14px]">cancel</span> {{ $message }}
                            </p>
                        @enderror
                    </div>
                    <button wire:click.prevent="updateProfile" type="button" class="mt-4 neo-border bg-primary text-on-primary font-label font-bold uppercase px-6 py-4 neo-shadow neo-shadow-hover transition-all w-full md:w-auto self-start">
                        <span wire:loading.remove wire:target="updateProfile">Update Details</span>
                        <span wire:loading wire:target="updateProfile" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Menyimpan…
                        </span>
                    </button>
                </div>
            </section>

            <!-- Saved Addresses -->
            <section class="anim-in d3">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <span class="material-symbols-outlined text-3xl text-primary">location_on</span>
                        <h2 class="font-headline text-3xl font-bold tracking-tight text-primary uppercase">Addresses</h2>
                    </div>
                    <button onclick="openAddressModal('new')" class="text-primary font-label font-bold uppercase hover:bg-secondary-container px-4 py-2 transition-colors border-[3px] border-transparent hover:border-primary flex items-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined">add</span>
                        New
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Address Card 1 -->
                    <div class="address-card bg-surface-container-lowest neo-border p-6 relative group hover:bg-surface-container-low">
                        <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="openAddressModal('edit', {label:'Home',name:'Jane Doe',address:'123 Conservatory Lane',city:'Portland',postal:'97204',phone:'+1 (555) 123-4567'})" class="p-1 hover:bg-primary hover:text-on-primary transition-colors border-[3px] border-transparent hover:border-primary rounded-none cursor-pointer"><span class="material-symbols-outlined text-sm">edit</span></button>
                            <button class="p-1 hover:bg-error hover:text-on-error transition-colors border-[3px] border-transparent hover:border-error rounded-none text-error cursor-pointer"><span class="material-symbols-outlined text-sm">delete</span></button>
                        </div>
                        <span class="inline-block bg-primary text-on-primary font-label text-xs font-bold uppercase px-2 py-1 mb-4">Default Home</span>
                        <p class="font-body text-primary font-bold text-lg mb-1">Jane Doe</p>
                        <p class="font-body text-on-surface-variant">123 Conservatory Lane<br/>Apt 4B<br/>Portland, OR 97204</p>
                    </div>
                    <!-- Address Card 2 -->
                    <div class="address-card bg-surface-container-lowest neo-border p-6 relative group hover:bg-surface-container-low">
                        <div class="absolute top-4 right-4 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button onclick="openAddressModal('edit', {label:'Work',name:'Jane Doe',address:'450 Structural Ave, Suite 200',city:'Seattle',postal:'98101',phone:'+1 (555) 987-6543'})" class="p-1 hover:bg-primary hover:text-on-primary transition-colors border-[3px] border-transparent hover:border-primary rounded-none cursor-pointer"><span class="material-symbols-outlined text-sm">edit</span></button>
                            <button class="p-1 hover:bg-error hover:text-on-error transition-colors border-[3px] border-transparent hover:border-error rounded-none text-error cursor-pointer"><span class="material-symbols-outlined text-sm">delete</span></button>
                        </div>
                        <span class="inline-block border-[3px] border-primary text-primary font-label text-xs font-bold uppercase px-2 py-1 mb-4">Work</span>
                        <p class="font-body text-primary font-bold text-lg mb-1">Jane Doe</p>
                        <p class="font-body text-on-surface-variant">450 Structural Ave<br/>Suite 200<br/>Seattle, WA 98101</p>
                    </div>
                </div>
            </section>

        </div>

        <!-- Right Column: Recent Orders Summary -->
        <div class="lg:col-span-5 anim-in d4">
            <section class="bg-surface-container-highest neo-border p-6 md:p-8 neo-shadow h-full flex flex-col">
                <div class="flex items-center gap-4 mb-8 border-b-[3px] border-primary pb-4">
                    <span class="material-symbols-outlined text-3xl text-primary">receipt_long</span>
                    <h2 class="font-headline text-3xl font-bold tracking-tight text-primary uppercase">Recent Orders</h2>
                </div>
                <div class="flex flex-col gap-6 flex-1">
                    <!-- Order Item 1 -->
                    <div class="order-item anim-in bg-surface-container-lowest neo-border p-4 flex gap-4 items-center">
                        <div class="w-20 h-20 bg-primary-container shrink-0 border-[3px] border-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-on-primary-container text-3xl">local_florist</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="font-headline font-bold text-primary truncate pr-2">Monstera Deliciosa</h3>
                                <span class="font-label font-bold text-primary">$45</span>
                            </div>
                            <p class="font-label text-xs text-on-surface-variant uppercase mb-2">Order #8892 • Oct 12</p>
                            <span class="inline-block bg-tertiary-fixed text-tertiary font-label text-[10px] font-bold uppercase px-2 py-1">Delivered</span>
                        </div>
                    </div>
                    <!-- Order Item 2 -->
                    <div class="order-item anim-in bg-surface-container-lowest neo-border p-4 flex gap-4 items-center">
                        <div class="w-20 h-20 bg-primary-container shrink-0 border-[3px] border-primary flex items-center justify-center">
                            <span class="material-symbols-outlined text-on-primary-container text-3xl">potted_plant</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="font-headline font-bold text-primary truncate pr-2">Organic Soil Mix</h3>
                                <span class="font-label font-bold text-primary">$18</span>
                            </div>
                            <p class="font-label text-xs text-on-surface-variant uppercase mb-2">Order #8841 • Sep 28</p>
                            <span class="inline-block bg-secondary-container text-primary font-label text-[10px] font-bold uppercase px-2 py-1 border-[3px] border-primary">Shipped</span>
                        </div>
                    </div>
                    <!-- Order Item 3 -->
                    <div class="order-item anim-in bg-surface-container-lowest neo-border p-4 flex gap-4 items-center opacity-70">
                        <div class="w-20 h-20 bg-surface-variant shrink-0 border-[3px] border-outline flex items-center justify-center">
                            <span class="material-symbols-outlined text-outline text-3xl">yard</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex justify-between items-start mb-1">
                                <h3 class="font-headline font-bold text-on-surface-variant truncate pr-2">Terracotta Pots (x3)</h3>
                                <span class="font-label font-bold text-on-surface-variant">$32</span>
                            </div>
                            <p class="font-label text-xs text-outline-variant uppercase mb-2">Order #8710 • Sep 05</p>
                            <span class="inline-block border-[3px] border-outline text-outline font-label text-[10px] font-bold uppercase px-2 py-1">Delivered</span>
                        </div>
                    </div>
                </div>
                <button class="mt-8 border-[3px] border-primary bg-transparent text-primary font-label font-bold uppercase px-6 py-4 hover:bg-primary hover:text-on-primary transition-colors w-full text-center">
                    View All History
                </button>
            </section>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════
    ADDRESS MODAL (shared for New & Edit)
═══════════════════════════════════════════════════════════════ --}}
<div id="addressModal" class="fixed inset-0 z-[100] hidden" aria-modal="true">
    <!-- Backdrop -->
    <div class="modal-backdrop absolute inset-0 bg-black/50" onclick="closeAddressModal()"></div>
    <!-- Panel -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div id="addressPanel" class="modal-panel bg-surface-container-lowest neo-border neo-shadow w-full max-w-lg relative">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b-[3px] border-primary">
                <div class="flex items-center gap-3">
                    <span class="material-symbols-outlined text-2xl text-primary" id="modalIcon">add_location</span>
                    <h3 class="font-headline text-2xl font-black text-primary uppercase tracking-tight" id="modalTitle">New Address</h3>
                </div>
                <button onclick="closeAddressModal()" class="p-1 hover:bg-surface-dim transition-colors cursor-pointer">
                    <span class="material-symbols-outlined text-primary">close</span>
                </button>
            </div>
            <!-- Modal Body -->
            <form class="p-6 flex flex-col gap-5">
                {{-- Address Label --}}
                <div class="flex flex-col gap-1.5 input-wrap" id="wrap_label">
                    <label class="font-label text-xs font-bold text-on-surface-variant uppercase tracking-wider">Address Label</label>
                    <input id="modal_label" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-base font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold" type="text" placeholder="e.g. Home, Work, Warehouse"/>
                </div>
                {{-- Receiver Name (DEMO ERROR) --}}
                <div class="flex flex-col gap-1.5 input-wrap input-error" id="wrap_name">
                    <label class="font-label text-xs font-bold text-on-surface-variant uppercase tracking-wider">Receiver Name</label>
                    <input id="modal_name" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-base font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold" type="text" placeholder="Full name"/>
                </div>
                {{-- Receiver Phone --}}
                <div class="flex flex-col gap-1.5 input-wrap" id="wrap_phone">
                    <label class="font-label text-xs font-bold text-on-surface-variant uppercase tracking-wider">Receiver Phone</label>
                    <input id="modal_phone" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-base font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold" type="tel" placeholder="+62 8..."/>
                </div>
                {{-- Full Address --}}
                <div class="flex flex-col gap-1.5 input-wrap" id="wrap_address">
                    <label class="font-label text-xs font-bold text-on-surface-variant uppercase tracking-wider">Full Address</label>
                    <textarea id="modal_address" rows="2" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-base font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold resize-none" placeholder="Street, building, apartment..."></textarea>
                </div>
                <div class="grid grid-cols-2 gap-5">
                    {{-- City --}}
                    <div class="flex flex-col gap-1.5 input-wrap" id="wrap_city">
                        <label class="font-label text-xs font-bold text-on-surface-variant uppercase tracking-wider">City</label>
                        <input id="modal_city" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-base font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold" type="text" placeholder="City"/>
                    </div>
                    {{-- Postal Code --}}
                    <div class="flex flex-col gap-1.5 input-wrap" id="wrap_postal">
                        <label class="font-label text-xs font-bold text-on-surface-variant uppercase tracking-wider">Postal Code</label>
                        <input id="modal_postal" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-base font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold" type="text" placeholder="12345"/>
                    </div>
                </div>
                {{-- Submit --}}
                <button type="button" id="modalSubmitBtn" onclick="closeAddressModal()" class="mt-2 neo-border bg-primary text-on-primary font-label font-bold uppercase px-6 py-4 neo-shadow neo-shadow-hover transition-all w-full">
                    Save Address
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    function openAddressModal(mode, data = {}) {
        const modal   = document.getElementById('addressModal');
        const panel   = document.getElementById('addressPanel');
        const title   = document.getElementById('modalTitle');
        const icon    = document.getElementById('modalIcon');
        const btn     = document.getElementById('modalSubmitBtn');

        // Set mode
        if (mode === 'edit') {
            title.textContent = 'Edit Address';
            icon.textContent  = 'edit_location';
            btn.textContent   = 'Update Address';
            // Populate fields
            document.getElementById('modal_label').value   = data.label   || '';
            document.getElementById('modal_name').value    = data.name    || '';
            document.getElementById('modal_phone').value   = data.phone   || '';
            document.getElementById('modal_address').value = data.address || '';
            document.getElementById('modal_city').value    = data.city    || '';
            document.getElementById('modal_postal').value  = data.postal  || '';
        } else {
            title.textContent = 'New Address';
            icon.textContent  = 'add_location';
            btn.textContent   = 'Save Address';
            // Clear fields
            document.getElementById('modal_label').value   = '';
            document.getElementById('modal_name').value    = '';
            document.getElementById('modal_phone').value   = '';
            document.getElementById('modal_address').value = '';
            document.getElementById('modal_city').value    = '';
            document.getElementById('modal_postal').value  = '';
        }

        // Show with animation
        modal.classList.remove('hidden');
        panel.classList.add('entering');
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                panel.classList.remove('entering');
            });
        });
    }

    function closeAddressModal() {
        const modal = document.getElementById('addressModal');
        const panel = document.getElementById('addressPanel');
        panel.classList.add('entering');
        setTimeout(() => {
            modal.classList.add('hidden');
            panel.classList.remove('entering');
            document.body.style.overflow = '';
        }, 200);
    }

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeAddressModal();
    });
</script>

</div>
