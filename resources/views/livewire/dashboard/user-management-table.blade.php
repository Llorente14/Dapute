<div
    x-data="{
        showCreate: false,
        showEdit:   false,
        showDelete: false,
        showConfirmStatus: false,
        confirmStatusUser: null,
        confirmStatusValue: null,
        openConfirmStatus(id, value) { this.confirmStatusUser = id; this.confirmStatusValue = value; this.showConfirmStatus = true; },
        showConfirmRole: false,
        confirmRoleUser: null,
        confirmRoleValue: null,
        currentRoleSelect: null,
        openConfirmRole(id, value, selectEl) { 
            this.confirmRoleUser = id; 
            this.confirmRoleValue = value; 
            this.currentRoleSelect = selectEl;
            this.showConfirmRole = true; 
        },
        deleteName: '',
        deleteId: null,
        openDelete(id, name) { this.deleteId = id; this.deleteName = name; this.showDelete = true; }
    }"
    class="p-6 md:p-10 flex flex-col gap-8"
>

{{-- ══ HEADER ══════════════════════════════════════════════════════════ --}}
<header class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 border-b-4 border-[#012d1d] pb-8 opacity-0 animate-[fadeUp_0.4s_ease_forwards]">
    <div>
        <p class="font-label text-xs uppercase tracking-[0.2em] text-[#414844]">Admin Panel &mdash; Account Management</p>
        <h2 class="font-headline font-black text-5xl md:text-6xl text-[#012d1d] leading-none tracking-tighter mt-1">User Management</h2>
        <p class="font-body text-[#414844] mt-2 max-w-lg">Manage and monitor all Admin, Employee, and Customer accounts on the Dapute platform.</p>
    </div>
    <button @click="showCreate = true"
        class="flex items-center gap-2 bg-[#012d1d] text-white border-[3px] border-[#012d1d] font-label font-bold text-xs uppercase tracking-wider px-6 py-4 shadow-[4px_4px_0_0_#012d1d] hover:-translate-y-1 hover:-translate-x-1 hover:shadow-[6px_6px_0_0_#012d1d] transition-all duration-200">
        <span class="material-symbols-outlined text-base">person_add</span>
        Add Employee
    </button>
</header>

{{-- ══ STAT CARDS ════════════════════════════════════════════════════ --}}
@php
    $totalUsers = $users->count();
    $adminStaffCount = $users->whereIn('role', ['admin', 'staff', 'owner'])->count();
    $customerCount = $users->where('role', 'customer')->count();
    $inactiveCount = $users->where('is_active', false)->count();
@endphp
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 opacity-0 animate-[fadeUp_0.5s_ease_0.1s_forwards]">
    @foreach([
        ['label'=>'Total Users','value'=>$totalUsers,'icon'=>'group','color'=>'bg-[#012d1d] text-white','shadow'=>'shadow-[4px_4px_0_0_#012d1d]'],
        ['label'=>'Admin & Employee','value'=>$adminStaffCount,'icon'=>'badge','color'=>'bg-[#d3ee6f] text-[#212a00]','shadow'=>'shadow-[4px_4px_0_0_#012d1d]'],
        ['label'=>'Customer','value'=>$customerCount,'icon'=>'person','color'=>'bg-[#dde4e0] text-[#012d1d]','shadow'=>'shadow-[4px_4px_0_0_#012d1d]'],
        ['label'=>'Inactive Accounts','value'=>$inactiveCount,'icon'=>'block','color'=>'bg-[#ffdad6] text-[#ba1a1a]','shadow'=>'shadow-[4px_4px_0_0_#ba1a1a] border-[#ba1a1a]'],
    ] as $stat)
    <div class="border-[3px] border-[#012d1d] {{ $stat['color'] }} p-5 {{ $stat['shadow'] }} hover:-translate-y-1 hover:shadow-[6px_6px_0_0_#012d1d] transition-all duration-200 cursor-default group">
        <span class="material-symbols-outlined text-2xl mb-3 block opacity-60 group-hover:scale-125 group-hover:opacity-100 transition-all duration-300">{{ $stat['icon'] }}</span>
        <p class="font-headline font-black text-4xl leading-none stat-num">{{ $stat['value'] }}</p>
        <p class="font-label font-bold text-[11px] uppercase tracking-widest mt-2 opacity-60">{{ $stat['label'] }}</p>
    </div>
    @endforeach
</div>

@push('head')
<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @keyframes rowIn {
        from { opacity: 0; transform: translateX(-12px); }
        to   { opacity: 1; transform: translateX(0); }
    }
    @keyframes countUp {
        from { opacity: 0; transform: scale(0.7); }
        to   { opacity: 1; transform: scale(1); }
    }
    .row-anim { opacity: 0; animation: rowIn 0.35s ease forwards; }
    .row-anim:nth-child(1) { animation-delay: 0.25s; }
    .row-anim:nth-child(2) { animation-delay: 0.32s; }
    .row-anim:nth-child(3) { animation-delay: 0.39s; }
    .row-anim:nth-child(4) { animation-delay: 0.46s; }
    .row-anim:nth-child(5) { animation-delay: 0.53s; }
    .stat-num { animation: countUp 0.5s cubic-bezier(0.34,1.56,0.64,1) 0.3s both; }
    .dropdown-panel { animation: slideDown 0.18s ease; }
</style>
@endpush

{{-- ══ SEARCH & FILTER ════════════════════════════════════════════ --}}
<div class="flex flex-col sm:flex-row gap-4 opacity-0 animate-[fadeUp_0.5s_ease_0.15s_forwards] relative z-20">
    <div class="flex-1 flex border-[3px] border-[#012d1d] bg-white focus-within:shadow-[4px_4px_0_0_#012d1d] transition-all">
        <span class="flex items-center px-4 text-[#012d1d]"><span class="material-symbols-outlined">search</span></span>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name, email, or phone number..."
            class="flex-1 bg-transparent px-2 py-3 font-body text-sm text-[#012d1d] focus:outline-none placeholder:text-[#717973]">
    </div>
    <div class="relative z-10"
         x-data="{ open: false, options: ['All Roles','Admin','Employee','Customer'] }">
        <button @click="open = !open" @click.outside="open = false" type="button"
            class="z-10 bg-white border-[3px] border-[#012d1d] px-4 py-3
                   font-label font-bold text-xs text-[#012d1d] uppercase tracking-wider
                   focus:outline-none focus:shadow-[4px_4px_0_0_#012d1d]
                   shadow-[4px_4px_0_0_#012d1d] hover:shadow-[6px_6px_0_0_#012d1d]
                   transition-all duration-150 cursor-pointer
                   flex items-center justify-between gap-6 w-full sm:w-52">
            <span x-text="$wire.filterRole"></span>
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20" stroke="#012d1d" stroke-width="2"
                 class="w-4 h-4 shrink-0 transition-transform duration-200" :class="open ? 'rotate-180' : ''">
                <path stroke-linecap="square" stroke-linejoin="miter" d="M6 8l4 4 4-4"/>
            </svg>
        </button>

        <div x-show="open" style="display:none;"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="absolute right-0 z-30 w-full mt-1 bg-white border-[3px] border-[#012d1d] shadow-[4px_4px_0_0_#012d1d]">
            <template x-for="option in options" :key="option">
                <div @click="$wire.set('filterRole', option); open = false"
                     class="px-4 py-3 font-label font-bold text-xs uppercase tracking-wider cursor-pointer transition-colors"
                     :class="$wire.filterRole === option
                        ? 'bg-[#012d1d] text-white'
                        : 'text-[#012d1d] hover:bg-[#012d1d] hover:text-white'">
                    <span x-text="option"></span>
                </div>
            </template>
        </div>
    </div>
</div>

{{-- ══ TABLE ═══════════════════════════════════════════════════════ --}}
<section class="border-[3px] border-[#012d1d] bg-white shadow-[4px_4px_0_0_#012d1d] overflow-hidden opacity-0 animate-[fadeUp_0.5s_ease_0.2s_forwards]">
    <div class="px-6 py-4 border-b-[3px] border-[#012d1d] bg-[#dde4e0] flex items-center justify-between">
        <div>
            <h3 class="font-label font-bold text-xs uppercase tracking-widest text-[#012d1d]">User List</h3>
            <p class="font-body text-[11px] text-[#414844] mt-0.5">All registered accounts on the platform</p>
        </div>
        <span class="font-label font-bold text-xs text-[#012d1d] bg-[#d3ee6f] px-3 py-1 border-[2px] border-[#012d1d]">{{ $totalUsers }} accounts</span>
    </div>
    <div class="overflow-x-auto">
    <table class="w-full min-w-[750px] text-left border-collapse">
        <thead class="bg-[#eef5f1] border-b-[3px] border-[#012d1d]">
            <tr>
                <th class="px-6 py-4 font-label font-bold text-[11px] uppercase tracking-[0.15em] text-[#012d1d] w-4"></th>
                <th class="px-6 py-4 font-label font-bold text-[11px] uppercase tracking-[0.15em] text-[#012d1d]">User</th>
                <th class="px-6 py-4 font-label font-bold text-[11px] uppercase tracking-[0.15em] text-[#012d1d]">Contact</th>
                <th class="px-6 py-4 font-label font-bold text-[11px] uppercase tracking-[0.15em] text-[#012d1d] text-center">Role</th>
                <th class="px-6 py-4 font-label font-bold text-[11px] uppercase tracking-[0.15em] text-[#012d1d]">Joined</th>
                <th class="px-6 py-4 font-label font-bold text-[11px] uppercase tracking-[0.15em] text-[#012d1d] text-center">Status</th>
                <th class="px-6 py-4 font-label font-bold text-[11px] uppercase tracking-[0.15em] text-[#012d1d]">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-[#012d1d]/10">
            @foreach($users as $i => $user)
            <tr class="row-anim hover:bg-[#f4fbf7] transition-colors group relative" wire:key="user-{{ $user->id }}">
                <td class="w-1 p-0"><span class="absolute left-0 top-0 h-full w-0 bg-[#d3ee6f] group-hover:w-1 transition-all duration-200"></span></td>
                {{-- Pengguna --}}
                <td class="px-6 py-4">
                    <p class="font-headline font-bold text-base text-[#012d1d] leading-tight">{{ $user->full_name }}</p>
                </td>
                {{-- Kontak --}}
                <td class="px-6 py-4">
                    <p class="font-body text-sm text-[#012d1d]">{{ $user->email }}</p>
                    <p class="font-body text-xs text-[#717973] mt-0.5">{{ $user->phone_number }}</p>
                </td>
                {{-- Role --}}
                <td class="px-6 py-4 text-center">
                    <div class="relative inline-block w-full max-w-[120px] text-left" x-data="{ open: false }">
                        @php
                            $roleOptions = [
                                'owner' => 'Owner',
                                'admin' => 'Admin',
                                'staff' => 'Employee',
                                'customer' => 'Customer'
                            ];
                            $currentLabel = $roleOptions[$user->role] ?? ucfirst($user->role);
                        @endphp
                        
                        <button @click="open = !open" @click.outside="open = false" type="button"
                            wire:loading.attr="disabled"
                            class="w-full bg-[#eef5f1] border-[2px] border-[#012d1d] px-2 py-1 font-label font-bold text-[10px] uppercase tracking-widest text-[#012d1d] focus:outline-none focus:bg-white focus:shadow-[2px_2px_0_0_#012d1d] transition-all cursor-pointer flex items-center justify-between gap-1 disabled:opacity-50">
                            <span>{{ $currentLabel }}</span>
                            <span class="material-symbols-outlined text-[14px] transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                        </button>

                        <div x-show="open" style="display:none;"
                             x-transition.opacity.duration.150ms
                             class="absolute left-0 z-30 w-full mt-1 bg-white border-[2px] border-[#012d1d] shadow-[2px_2px_0_0_#012d1d]">
                            @foreach($roleOptions as $val => $label)
                                <div @click="openConfirmRole('{{ $user->id }}', '{{ $val }}', null); open = false"
                                     class="px-2 py-1.5 font-label font-bold text-[10px] uppercase tracking-widest cursor-pointer transition-colors {{ $user->role === $val ? 'bg-[#012d1d] text-white' : 'text-[#012d1d] hover:bg-[#012d1d] hover:text-white' }}">
                                    <span>{{ $label }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </td>
                {{-- Joined --}}
                <td class="px-6 py-4">
                    <p class="font-body text-sm text-[#012d1d]">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</p>
                    <p class="font-body text-[10px] text-[#717973] mt-0.5 uppercase tracking-wide">Joined</p>
                </td>
                {{-- Status --}}
                <td class="px-6 py-4 text-center flex flex-col items-center justify-center gap-2">
                    @if($user->is_active)
                        <span class="inline-block px-3 py-0.5 font-label font-bold text-[10px] uppercase tracking-widest border-[2px] bg-[#d3ee6f] text-[#212a00] border-[#012d1d]">Active</span>
                        <button type="button" @click="openConfirmStatus('{{ $user->id }}', false)" wire:loading.attr="disabled" class="text-[10px] border-[2px] border-[#012d1d] px-2 py-0.5 bg-white text-[#012d1d] hover:bg-gray-100 uppercase font-bold transition-colors">Suspend</button>
                    @else
                        <span class="inline-block px-3 py-0.5 font-label font-bold text-[10px] uppercase tracking-widest border-[2px] bg-[#ffdad6] text-[#ba1a1a] border-[#ba1a1a]">Suspended</span>
                        <button type="button" @click="openConfirmStatus('{{ $user->id }}', true)" wire:loading.attr="disabled" class="text-[10px] border-[2px] border-[#012d1d] px-2 py-0.5 bg-white text-[#012d1d] hover:bg-gray-100 uppercase font-bold transition-colors">Aktifkan</button>
                    @endif
                </td>
                {{-- Aksi --}}
                <td class="px-6 py-4">
                    <div class="flex items-center justify-start gap-1.5">
                        <button @click="$wire.editUser('{{ $user->id }}').then(() => showEdit = true)" title="Edit"
                            class="w-9 h-9 flex items-center justify-center border-[3px] border-[#012d1d] bg-white text-[#012d1d] hover:bg-[#012d1d] hover:text-white transition-all shadow-[2px_2px_0_0_#012d1d] hover:-translate-y-0.5 hover:shadow-[3px_3px_0_0_#012d1d] active:translate-y-0 active:shadow-none">
                            <span class="material-symbols-outlined" style="font-size:18px">edit</span>
                        </button>
                        <button wire:click="resetUserPassword('{{ $user->id }}')" wire:loading.attr="disabled" title="Reset Password"
                            class="w-9 h-9 flex items-center justify-center border-[3px] border-[#012d1d] bg-white text-[#012d1d] hover:bg-[#414844] hover:text-white hover:border-[#414844] transition-all shadow-[2px_2px_0_0_#012d1d] hover:-translate-y-0.5 active:translate-y-0 active:shadow-none disabled:opacity-50">
                            <span class="material-symbols-outlined" style="font-size:18px">lock_reset</span>
                        </button>
                        <button @click="openDelete('{{ $user->id }}', '{{ $user->full_name }}')" title="Hapus"
                            class="w-9 h-9 flex items-center justify-center border-[3px] border-[#ba1a1a] bg-white text-[#ba1a1a] hover:bg-[#ba1a1a] hover:text-white transition-all shadow-[2px_2px_0_0_#ba1a1a] hover:-translate-y-0.5 active:translate-y-0 active:shadow-none">
                            <span class="material-symbols-outlined" style="font-size:18px">delete</span>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
</section>

{{-- ══ MODAL: CREATE ══════════════════════════════════════════════ --}}
<div x-show="showCreate" x-cloak @keydown.escape.window="showCreate=false" x-on:close-modal-create.window="showCreate=false"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm" style="background:rgba(55,65,81,0.60);">
    <div class="border-[3px] border-[#012d1d] bg-white w-full max-w-md shadow-[8px_8px_0_0_#012d1d]"
         x-transition:enter="transition ease-out duration-200 delay-75" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
         @click.stop>
        <div class="px-8 py-5 border-b-[3px] border-[#012d1d] bg-[#dde4e0] flex justify-between items-center">
            <h3 class="font-headline font-black text-2xl text-[#012d1d] uppercase tracking-tighter">Add Employee</h3>
            <button @click="showCreate=false" class="text-[#012d1d] hover:bg-[#012d1d] hover:text-white border-[2px] border-transparent hover:border-[#012d1d] p-1 transition-all"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="p-8 flex flex-col gap-5">
            <div>
                <label class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2">Full Name<span class="text-[#ba1a1a]"> *</span></label>
                <input type="text" wire:model="create_full_name" placeholder="John Doe"
                    class="w-full bg-[#eef5f1] border-[3px] border-[#012d1d] px-4 py-3 font-body text-sm text-[#012d1d] placeholder:text-[#717973] focus:outline-none focus:bg-white focus:shadow-[4px_4px_0_0_#012d1d] transition-all">
                @error('create_full_name') <span class="text-[#ba1a1a] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2">Email<span class="text-[#ba1a1a]"> *</span></label>
                <input type="email" wire:model="create_email" placeholder="john@dapute.com"
                    class="w-full bg-[#eef5f1] border-[3px] border-[#012d1d] px-4 py-3 font-body text-sm text-[#012d1d] placeholder:text-[#717973] focus:outline-none focus:bg-white focus:shadow-[4px_4px_0_0_#012d1d] transition-all">
                @error('create_email') <span class="text-[#ba1a1a] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2">Phone</label>
                <input type="text" wire:model="create_phone_number" placeholder="+62 8123456789"
                    class="w-full bg-[#eef5f1] border-[3px] border-[#012d1d] px-4 py-3 font-body text-sm text-[#012d1d] placeholder:text-[#717973] focus:outline-none focus:bg-white focus:shadow-[4px_4px_0_0_#012d1d] transition-all">
                @error('create_phone_number') <span class="text-[#ba1a1a] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2">Initial Password<span class="text-[#ba1a1a]"> *</span></label>
                <input type="password" wire:model="create_password" placeholder="Min. 8 characters"
                    class="w-full bg-[#eef5f1] border-[3px] border-[#012d1d] px-4 py-3 font-body text-sm text-[#012d1d] placeholder:text-[#717973] focus:outline-none focus:bg-white focus:shadow-[4px_4px_0_0_#012d1d] transition-all">
                @error('create_password') <span class="text-[#ba1a1a] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="px-8 pb-8 flex gap-3">
            <button @click="showCreate=false" class="flex-1 py-3 border-[3px] border-[#012d1d] bg-[#f4fbf7] text-[#012d1d] font-label font-bold text-xs uppercase tracking-wider hover:bg-[#dde4e0] transition-all shadow-[2px_2px_0_0_#012d1d]">Cancel</button>
            <button wire:click="createUser" wire:loading.attr="disabled" class="flex-1 py-3 border-[3px] border-[#012d1d] bg-[#012d1d] text-white font-label font-bold text-xs uppercase tracking-wider hover:bg-[#1b4332] transition-all shadow-[2px_2px_0_0_#012d1d] flex items-center justify-center gap-2 disabled:opacity-50">
                <span class="material-symbols-outlined text-sm">person_add</span>Save
            </button>
        </div>
    </div>
</div>

{{-- ══ MODAL: EDIT ════════════════════════════════════════════════ --}}
<div x-show="showEdit" x-cloak @keydown.escape.window="showEdit=false" x-on:close-modal-edit.window="showEdit=false"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm" style="background:rgba(55,65,81,0.60);">
    <div class="border-[3px] border-[#012d1d] bg-white w-full max-w-md shadow-[8px_8px_0_0_#012d1d]"
         x-transition:enter="transition ease-out duration-200 delay-75" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
         @click.stop>
        <div class="px-8 py-5 border-b-[3px] border-[#012d1d] bg-[#dde4e0] flex justify-between items-center">
            <h3 class="font-headline font-black text-2xl text-[#012d1d] uppercase tracking-tighter">Edit User</h3>
            <button @click="showEdit=false" class="text-[#012d1d] hover:bg-[#012d1d] hover:text-white border-[2px] border-transparent hover:border-[#012d1d] p-1 transition-all"><span class="material-symbols-outlined">close</span></button>
        </div>
        <div class="p-8 flex flex-col gap-5">
            <div>
                <label class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2">Full Name <span class="text-[#ba1a1a]">*</span></label>
                <input type="text" wire:model="edit_full_name" class="w-full bg-[#eef5f1] border-[3px] border-[#012d1d] px-4 py-3 font-body text-sm text-[#012d1d] focus:outline-none focus:bg-white focus:shadow-[4px_4px_0_0_#012d1d] transition-all">
                @error('edit_full_name') <span class="text-[#ba1a1a] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2">Email <span class="text-[#ba1a1a]">*</span></label>
                <input type="email" wire:model="edit_email" class="w-full bg-[#eef5f1] border-[3px] border-[#012d1d] px-4 py-3 font-body text-sm text-[#012d1d] focus:outline-none focus:bg-white focus:shadow-[4px_4px_0_0_#012d1d] transition-all">
                @error('edit_email') <span class="text-[#ba1a1a] text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>
            <div>
                <label class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2">Phone Number</label>
                <input type="text" wire:model="edit_phone_number" class="w-full bg-[#eef5f1] border-[3px] border-[#012d1d] px-4 py-3 font-body text-sm text-[#012d1d] focus:outline-none focus:bg-white focus:shadow-[4px_4px_0_0_#012d1d] transition-all">
            </div>
            <div>
                <label class="block font-label font-bold text-[10px] uppercase tracking-[0.2em] text-[#012d1d] mb-2">Role <span class="text-[#ba1a1a]">*</span></label>
                <div class="relative">
                    <select wire:model="edit_role" class="w-full appearance-none bg-[#eef5f1] border-[3px] border-[#012d1d] px-4 py-3 pr-10 font-label font-bold text-sm text-[#012d1d] focus:outline-none focus:bg-white focus:shadow-[4px_4px_0_0_#012d1d] transition-all cursor-pointer">
                        <option value="owner">Owner</option>
                        <option value="admin">Admin</option>
                        <option value="staff">Employee</option>
                        <option value="customer">Customer</option>
                    </select>
                    <span class="material-symbols-outlined absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-[#012d1d]">expand_more</span>
                </div>
            </div>
            <div class="flex items-center justify-between border-[3px] border-[#012d1d] bg-[#eef5f1] px-4 py-4">
                <div>
                    <p class="font-label font-bold text-sm text-[#012d1d] uppercase tracking-wide">Active Status</p>
                    <p class="font-body text-xs text-[#414844] mt-0.5">Active — user can log in</p>
                </div>
                <div>
                    <button type="button" wire:click="$set('edit_is_active', !edit_is_active)"
                        class="relative shrink-0 w-14 h-7 border-[3px] border-[#012d1d] shadow-[2px_2px_0_0_#012d1d] transition-all cursor-pointer focus:outline-none"
                        :style="$wire.edit_is_active ? 'background:#d3ee6f' : 'background:#dde4e0'">
                        <span class="absolute top-[2px] w-5 h-5 border-[3px] border-[#012d1d] transition-all pointer-events-none"
                            :style="$wire.edit_is_active ? 'left:calc(100% - 1.375rem); background:#012d1d' : 'left:1px; background:#717973'"></span>
                    </button>
                </div>
            </div>
        </div>
        <div class="px-8 pb-8 flex gap-3">
            <button @click="showEdit=false" class="flex-1 py-3 border-[3px] border-[#012d1d] bg-[#f4fbf7] text-[#012d1d] font-label font-bold text-xs uppercase tracking-wider hover:bg-[#dde4e0] transition-all shadow-[2px_2px_0_0_#012d1d]">Cancel</button>
            <button wire:click="updateUser" wire:loading.attr="disabled" class="flex-1 py-3 border-[3px] border-[#012d1d] bg-[#012d1d] text-white font-label font-bold text-xs uppercase tracking-wider hover:bg-[#1b4332] transition-all shadow-[2px_2px_0_0_#012d1d] flex items-center justify-center gap-2 disabled:opacity-50">
                <span class="material-symbols-outlined text-sm">save</span>Save Changes
            </button>
        </div>
    </div>
</div>

{{-- ══ MODAL: DELETE ══════════════════════════════════════════════ --}}
<div x-show="showDelete" x-cloak @keydown.escape.window="showDelete=false"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm" style="background:rgba(55,65,81,0.60);">
    <div class="border-[3px] border-[#012d1d] bg-white w-full max-w-md p-8 shadow-[8px_8px_0_0_#012d1d]"
         x-transition:enter="transition ease-out duration-200 delay-75" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
         @click.stop>
        <div class="w-12 h-12 border-[3px] border-[#ba1a1a] bg-[#ffdad6] flex items-center justify-center mb-5 shadow-[2px_2px_0_0_#ba1a1a]">
            <span class="material-symbols-outlined text-[#ba1a1a] text-xl">warning</span>
        </div>
        <h3 class="font-headline font-black text-[#012d1d] text-2xl uppercase tracking-tighter mb-2">Delete Account?</h3>
        <p class="font-body text-[#414844] text-sm mb-6">Account <strong class="text-[#012d1d]" x-text="deleteName"></strong> will be permanently deleted and cannot be recovered.</p>
        <div class="flex gap-3">
            <button @click="showDelete=false" class="flex-1 py-3 border-[3px] border-[#012d1d] bg-[#f4fbf7] text-[#012d1d] font-label font-bold text-xs uppercase tracking-wider hover:bg-[#dde4e0] transition-all shadow-[2px_2px_0_0_#012d1d]">Cancel</button>
            <button wire:click="deleteUser(deleteId)" @click="showDelete=false" wire:loading.attr="disabled" class="flex-1 py-3 border-[3px] border-[#ba1a1a] bg-[#ba1a1a] text-white font-label font-bold text-xs uppercase tracking-wider hover:bg-[#93000a] hover:border-[#93000a] transition-all shadow-[2px_2px_0_0_#ba1a1a] flex items-center justify-center gap-1.5 disabled:opacity-50">
                <span class="material-symbols-outlined text-sm">delete_forever</span>Yes, Delete
            </button>
        </div>
    </div>
</div>

{{-- ══ MODAL: CONFIRM STATUS ══════════════════════════════════════════════ --}}
<div x-show="showConfirmStatus" x-cloak @keydown.escape.window="showConfirmStatus=false"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm" style="background:rgba(55,65,81,0.60);">
    <div class="border-[3px] border-[#012d1d] bg-white w-full max-w-md p-8 shadow-[8px_8px_0_0_#012d1d]"
         x-transition:enter="transition ease-out duration-200 delay-75" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
         @click.stop>
        <div class="w-12 h-12 border-[3px] border-[#012d1d] bg-[#dde4e0] flex items-center justify-center mb-5 shadow-[2px_2px_0_0_#012d1d]">
            <span class="material-symbols-outlined text-[#012d1d] text-xl">info</span>
        </div>
        <h3 class="font-headline font-black text-[#012d1d] text-2xl uppercase tracking-tighter mb-2">Konfirmasi Status</h3>
        <p class="font-body text-[#414844] text-sm mb-6">Apakah Anda yakin ingin <span x-text="confirmStatusValue ? 'mengaktifkan' : 'menskrors (suspend)'"></span> pengguna ini?</p>
        <div class="flex gap-3">
            <button @click="showConfirmStatus=false" class="flex-1 py-3 border-[3px] border-[#012d1d] bg-[#f4fbf7] text-[#012d1d] font-label font-bold text-xs uppercase tracking-wider hover:bg-[#dde4e0] transition-all shadow-[2px_2px_0_0_#012d1d]">Batal</button>
            <button wire:loading.attr="disabled" @click="$wire.toggleUserStatus(confirmStatusUser, confirmStatusValue); showConfirmStatus=false" class="flex-1 py-3 border-[3px] border-[#012d1d] bg-[#012d1d] text-white font-label font-bold text-xs uppercase tracking-wider hover:bg-[#1b4332] transition-all shadow-[2px_2px_0_0_#012d1d] flex items-center justify-center gap-1.5 disabled:opacity-50">
                <span class="material-symbols-outlined text-sm">check_circle</span>Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>

{{-- ══ MODAL: CONFIRM ROLE ══════════════════════════════════════════════ --}}
<div x-show="showConfirmRole" x-cloak @keydown.escape.window="showConfirmRole=false"
     x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 backdrop-blur-sm" style="background:rgba(55,65,81,0.60);">
    <div class="border-[3px] border-[#012d1d] bg-white w-full max-w-md p-8 shadow-[8px_8px_0_0_#012d1d]"
         x-transition:enter="transition ease-out duration-200 delay-75" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
         @click.stop>
        <div class="w-12 h-12 border-[3px] border-[#012d1d] bg-[#dde4e0] flex items-center justify-center mb-5 shadow-[2px_2px_0_0_#012d1d]">
            <span class="material-symbols-outlined text-[#012d1d] text-xl">manage_accounts</span>
        </div>
        <h3 class="font-headline font-black text-[#012d1d] text-2xl uppercase tracking-tighter mb-2">Konfirmasi Role</h3>
        <p class="font-body text-[#414844] text-sm mb-6">Apakah Anda yakin ingin mengubah hak akses pengguna ini menjadi <strong class="text-[#012d1d] uppercase" x-text="confirmRoleValue"></strong>?</p>
        <div class="flex gap-3">
            <button @click="showConfirmRole=false" class="flex-1 py-3 border-[3px] border-[#012d1d] bg-[#f4fbf7] text-[#012d1d] font-label font-bold text-xs uppercase tracking-wider hover:bg-[#dde4e0] transition-all shadow-[2px_2px_0_0_#012d1d]">Batal</button>
            <button wire:loading.attr="disabled" @click="$wire.updateUserRole(confirmRoleUser, confirmRoleValue); showConfirmRole=false" class="flex-1 py-3 border-[3px] border-[#012d1d] bg-[#012d1d] text-white font-label font-bold text-xs uppercase tracking-wider hover:bg-[#1b4332] transition-all shadow-[2px_2px_0_0_#012d1d] flex items-center justify-center gap-1.5 disabled:opacity-50">
                <span class="material-symbols-outlined text-sm">check_circle</span>Ya, Ubah
            </button>
        </div>
    </div>
</div>

@push('head')
<style>
    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

</div>
