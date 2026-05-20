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
<div class="flex-1 w-full max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">

    <!-- Header Section -->
    <header class="anim-in d1 mb-12 border-b-[3px] border-primary pb-6 flex flex-col md:flex-row md:items-end justify-between gap-6">
        <div>
            <h1 class="font-headline text-4xl md:text-6xl font-black tracking-tighter text-primary uppercase leading-none">Profile</h1>
            <p class="font-body text-lg text-on-surface-variant mt-4 max-w-2xl">Manage your greenhouse details, structural addresses, and review recent botanical acquisitions.</p>
        </div>
        <form action="/logout" method="POST" class="self-start md:self-auto">
            @csrf
            <button type="submit" class="neo-border bg-tertiary-fixed text-primary font-label font-bold uppercase px-6 py-3 neo-shadow neo-shadow-hover transition-all flex items-center gap-2 cursor-pointer hover:-translate-y-1">
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
                    <span class="material-symbols-outlined text-2xl text-primary">person</span>
                    <h2 class="font-headline text-2xl font-bold tracking-tight text-primary uppercase">Personal Info</h2>
                </div>
                <div class="bg-surface-container-lowest neo-border p-6 md:p-8 neo-shadow flex flex-col gap-6">
                    <div class="grid grid-cols-1 gap-6">
                        {{-- Full Name --}}
                        <div class="flex flex-col gap-2 input-wrap transition-colors p-2 -m-2 relative pb-6 {{ $errors->has('full_name') ? 'input-error' : '' }}">
                            <label class="font-label text-sm font-bold text-on-surface-variant uppercase tracking-wider">Full Name</label>
                            <input wire:model="full_name" name="full_name" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-lg font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold transition-colors" type="text" placeholder="Your full name"/>
                            @error('full_name')
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
                        <div class="flex items-center gap-2 border-b-[3px] border-primary pb-2">
                            <span class="font-label text-lg font-black text-primary">+62</span>
                            <input wire:model.live.debounce.300ms="phone_number" name="phone_number" class="w-full bg-transparent border-0 p-0 text-lg font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold transition-colors" type="tel" inputmode="numeric" maxlength="11" pattern="[0-9]{8,11}" placeholder="81234567890"/>
                        </div>
                        @error('phone_number')
                            <p class="error-msg absolute bottom-0 left-0 flex items-center gap-1 font-body text-[12px] text-error font-semibold">
                                <span class="material-symbols-outlined text-[14px]">cancel</span> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <button wire:click.prevent="updateProfile" type="button" class="mt-4 neo-border bg-primary text-on-primary font-label font-bold uppercase px-6 py-4 neo-shadow neo-shadow-hover transition-all w-full md:w-auto self-start">
                        <span wire:loading.remove wire:target="updateProfile">Update Details</span>
                        <span wire:loading wire:target="updateProfile" class="flex items-center gap-2">
                            <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            Saving…
                        </span>
                    </button>
                </div>
            </section>

            <!-- Saved Addresses -->
            <section class="anim-in d3" x-data="profileAddressManager(@js((string) auth()->id()))">
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center gap-4">
                        <span class="material-symbols-outlined text-2xl text-primary">location_on</span>
                        <h2 class="font-headline text-2xl font-bold tracking-tight text-primary uppercase">Addresses</h2>
                    </div>
                    <button x-show="!isFull" x-on:click="openCreate()" type="button" class="text-primary font-label font-bold uppercase hover:bg-secondary-container px-4 py-2 transition-colors border-[3px] border-transparent hover:border-primary flex items-center gap-2 cursor-pointer">
                        <span class="material-symbols-outlined">add</span>
                        New
                    </button>
                </div>

                <div x-show="formOpen" x-collapse class="bg-surface-container-lowest neo-border p-6 md:p-8 neo-shadow mb-6">
                    <div class="flex items-center justify-between gap-4 mb-6 border-b-[3px] border-primary pb-4">
                        <h3 class="font-headline text-xl font-black text-primary uppercase tracking-tight" x-text="editingId ? 'Edit Address' : 'New Address'"></h3>
                        <button x-on:click="cancelForm()" type="button" class="p-1 hover:bg-surface-dim transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-primary">close</span>
                        </button>
                    </div>
                    <form x-on:submit.prevent="save()" class="flex flex-col gap-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-1.5 input-wrap">
                                <label class="font-label text-xs font-bold text-on-surface-variant uppercase tracking-wider">Label</label>
                                <input x-model="form.label" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-base font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold" type="text" placeholder="Home, Work, Kos"/>
                                <p x-show="errors.label" x-text="errors.label" class="error-msg font-body text-[12px] text-error font-semibold"></p>
                            </div>
                            <div class="flex flex-col gap-1.5 input-wrap">
                                <label class="font-label text-xs font-bold text-on-surface-variant uppercase tracking-wider">Recipient Phone</label>
                                <div class="flex items-center gap-2 border-b-[3px] border-primary pb-2">
                                    <span class="font-label text-base font-black text-primary">+62</span>
                                    <input x-model="form.recipient_phone" x-on:input="cleanPhoneNumber()" class="w-full bg-transparent border-0 p-0 text-base font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold" type="tel" inputmode="numeric" maxlength="11" pattern="[0-9]{8,11}" placeholder="81234567890"/>
                                </div>
                                <p x-show="errors.recipient_phone" x-text="errors.recipient_phone" class="error-msg font-body text-[12px] text-error font-semibold"></p>
                            </div>
                        </div>
                        <div class="flex flex-col gap-1.5 input-wrap">
                            <label class="font-label text-xs font-bold text-on-surface-variant uppercase tracking-wider">Recipient Name</label>
                            <input x-model="form.recipient_name" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-base font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold" type="text" placeholder="Full name"/>
                            <p x-show="errors.recipient_name" x-text="errors.recipient_name" class="error-msg font-body text-[12px] text-error font-semibold"></p>
                        </div>
                        <div class="flex flex-col gap-1.5 input-wrap">
                            <label class="font-label text-xs font-bold text-on-surface-variant uppercase tracking-wider">Full Address</label>
                            <textarea x-model="form.address" rows="2" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-base font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold resize-none" placeholder="Street, building, apartment..."></textarea>
                            <p x-show="errors.address" x-text="errors.address" class="error-msg font-body text-[12px] text-error font-semibold"></p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="flex flex-col gap-1.5 input-wrap">
                                <label class="font-label text-xs font-bold text-on-surface-variant uppercase tracking-wider">City</label>
                                <input x-model="form.city" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-base font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold" type="text" placeholder="City"/>
                                <p x-show="errors.city" x-text="errors.city" class="error-msg font-body text-[12px] text-error font-semibold"></p>
                            </div>
                            <div class="flex flex-col gap-1.5 input-wrap">
                                <label class="font-label text-xs font-bold text-on-surface-variant uppercase tracking-wider">Postal Code</label>
                                <input x-model="form.postal_code" x-on:input="cleanPostalCode()" class="w-full bg-transparent border-0 border-b-[3px] border-primary pb-2 text-base font-body focus:outline-none focus:ring-0 placeholder:text-outline-variant text-primary font-semibold" type="text" inputmode="numeric" maxlength="5" pattern="[0-9]{5}" placeholder="12345"/>
                                <p x-show="errors.postal_code" x-text="errors.postal_code" class="error-msg font-body text-[12px] text-error font-semibold"></p>
                            </div>
                        </div>
                        <label class="flex items-center gap-3 font-label text-sm font-bold uppercase text-primary cursor-pointer">
                            <input x-model="form.is_default" type="checkbox" class="h-5 w-5 border-[3px] border-primary text-primary focus:ring-0"/>
                            Set As Default
                        </label>
                        <button type="submit" class="mt-2 neo-border bg-primary text-on-primary font-label font-bold uppercase px-6 py-4 neo-shadow neo-shadow-hover transition-all w-full">
                            Save Address
                        </button>
                    </form>
                </div>

                <div x-show="addresses.length === 0 && !formOpen" class="bg-surface-container-lowest neo-border p-6 neo-shadow">
                    <p class="font-body text-on-surface-variant font-semibold">No address saved yet.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <template x-for="address in addresses" :key="address.id">
                        <div class="address-card bg-surface-container-lowest border-[3px] border-primary p-6 relative group hover:bg-surface-container-low" style="box-shadow: 4px 4px 0px 0px #012d1d;">
                            <div class="absolute top-4 right-4 flex gap-2 opacity-100 md:opacity-0 md:group-hover:opacity-100 transition-opacity">
                                <button x-on:click="openEdit(address)" type="button" aria-label="Edit address" class="p-2 bg-surface-container-lowest md:bg-transparent hover:bg-primary hover:text-on-primary transition-colors border-[3px] border-primary md:border-transparent hover:border-primary cursor-pointer"><span class="material-symbols-outlined text-sm">edit</span></button>
                                <button x-on:click="remove(address.id)" type="button" aria-label="Delete address" class="p-2 bg-surface-container-lowest md:bg-transparent hover:bg-error hover:text-on-error transition-colors border-[3px] border-error md:border-transparent hover:border-error text-error cursor-pointer"><span class="material-symbols-outlined text-sm">delete</span></button>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 mb-4 pr-20">
                                <span class="inline-block bg-[#D4EF70] text-primary font-label text-xs font-bold uppercase px-2 py-1" x-text="address.is_default ? 'Default' : address.label"></span>
                                <button x-show="!address.is_default" x-on:click="setDefault(address.id)" type="button" class="border-[3px] border-primary px-2 py-1 font-label text-[10px] font-bold uppercase text-primary hover:bg-primary hover:text-on-primary transition-colors">
                                    Set Default
                                </button>
                            </div>
                            <p class="font-body text-primary font-bold text-lg mb-1" x-text="address.recipient_name"></p>
                            <p class="font-body text-on-surface-variant" x-text="address.recipient_phone"></p>
                            <p class="font-body text-on-surface-variant mt-3">
                                <span x-text="address.address"></span><br/>
                                <span x-text="address.city"></span>,
                                <span x-text="address.postal_code"></span>
                            </p>
                        </div>
                    </template>
                    <div x-show="isFull" class="bg-tertiary-fixed border-[3px] border-primary p-4 font-label font-bold uppercase text-primary">
                        Address limit reached (5/5)
                    </div>
                </div>
            </section>

        </div>

        <!-- Right Column: Recent Orders Summary -->
        <div class="lg:col-span-5 anim-in d4">
            <livewire:profile.profile-order-history />
        </div>

    </div>
</div>

</div>
