<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin — {{ config('app.name', 'Dapute') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@700;900&family=Manrope:wght@400;500;600&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet">
    <style>
        [x-cloak] { display: none !important; }
        .material-symbols-outlined {
            font-family: 'Material Symbols Outlined';
            font-weight: normal;
            font-style: normal;
            font-size: 24px;
            line-height: 1;
            letter-spacing: normal;
            text-transform: none;
            display: inline-block;
            white-space: nowrap;
            word-wrap: normal;
            direction: ltr;
            -webkit-font-feature-settings: 'liga';
            font-feature-settings: 'liga';
            -webkit-font-smoothing: antialiased;
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .material-symbols-filled {
            font-variation-settings: 'FILL' 1, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }

    </style>
</head>
@php
    $adminRole = strtolower((string) auth()->user()?->role);
    $roleLabel = match ($adminRole) {
        'owner' => 'Owner Workspace',
        'admin' => 'Admin Workspace',
        default => 'Restricted Workspace',
    };

    $menuItems = [
        [
            'label' => 'Orders',
            'route' => 'admin.orders.index',
            'pattern' => 'admin.orders*',
            'icon' => 'pending_actions',
            'roles' => ['owner', 'admin'],
        ],
        [
            'label' => 'Products',
            'route' => 'admin.products.index',
            'pattern' => 'admin.products*',
            'icon' => 'inventory_2',
            'roles' => ['owner'],
            'filled' => true,
        ],
        [
            'label' => 'Reports',
            'route' => 'admin.reports.index',
            'pattern' => 'admin.reports*',
            'icon' => 'analytics',
            'roles' => ['owner'],
        ],
        [
            'label' => 'Users',
            'route' => 'admin.users.index',
            'pattern' => 'admin.users*',
            'icon' => 'group',
            'roles' => ['owner'],
        ],
    ];

    $visibleMenuItems = collect($menuItems)
        ->filter(fn (array $item) => in_array($adminRole, $item['roles'], true))
        ->values();
@endphp

<body class="font-body bg-[#f4fbf7] text-[#161d1b] antialiased min-h-screen flex flex-col md:flex-row selection:bg-[#d3ee6f] selection:text-[#212a00]" x-data="{ sidebarOpen: window.innerWidth >= 768 }">


    {{-- ── MOBILE SIDEBAR OVERLAY ── --}}
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="md:hidden fixed inset-0 z-[55] bg-[#012d1d]/40 backdrop-blur-sm transition-opacity duration-300"
         x-transition:enter="ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

    {{-- ── SIDEBAR ──────────────────────────────────────────── --}}
    <nav class="flex flex-col h-[100dvh] fixed left-0 top-0 z-[60] bg-[#eef5f1] border-r-4 border-[#012d1d] transition-all duration-300 overflow-hidden"
         :class="sidebarOpen ? 'w-64 translate-x-0' : 'w-64 -translate-x-full md:translate-x-0 md:w-20'">
        <div class="border-b-4 border-[#012d1d] flex items-center transition-all duration-300" :class="sidebarOpen ? 'p-6 justify-between' : 'py-6 px-0 justify-center'">
            <div class="transition-all duration-300" :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0 overflow-hidden'">
                <h1 class="font-headline font-black text-xl text-[#012d1d] leading-none whitespace-nowrap">Dapute Admin</h1>
                <p class="text-[10px] font-label text-[#414844] uppercase tracking-wider mt-1.5 whitespace-nowrap">{{ $roleLabel }}</p>
            </div>
            <!-- Desktop Toggle -->
            <button @click="sidebarOpen = !sidebarOpen" class="w-10 h-10 shrink-0 hidden md:flex items-center justify-center border-[3px] border-[#012d1d] bg-[#ffffff] hover:bg-[#012d1d] hover:text-[#ffffff] transition-all shadow-[2px_2px_0px_0px_rgba(1,45,29,1)] active:translate-y-0.5 active:translate-x-0.5 active:shadow-none text-[#012d1d]">
                <span class="material-symbols-outlined text-lg">menu</span>
            </button>
            <!-- Mobile Close -->
            <button @click="sidebarOpen = false" class="w-10 h-10 shrink-0 md:hidden flex items-center justify-center border-[3px] border-[#ba1a1a] text-[#ba1a1a] bg-[#ffffff] hover:bg-[#ba1a1a] hover:text-[#ffffff] transition-all shadow-[2px_2px_0px_0px_#ba1a1a] active:translate-y-0.5 active:translate-x-0.5 active:shadow-none">
                <span class="material-symbols-outlined text-sm">close</span>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto py-4 flex flex-col gap-0.5 font-body font-semibold overflow-x-hidden">
            @forelse ($visibleMenuItems as $item)
                @php
                    $isActive = request()->routeIs($item['pattern']);
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="{{ $isActive ? 'bg-[#012d1d] text-white border-y-2 border-[#012d1d]' : 'text-[#012d1d] hover:bg-[#d8e2dc]' }} flex items-center gap-3 py-3.5 transition-all duration-300"
                   :class="sidebarOpen ? 'px-6 hover:translate-x-1' : 'px-0 justify-center hover:translate-x-0'"
                   title="{{ $item['label'] }}">
                    <span class="material-symbols-outlined text-xl {{ ($item['filled'] ?? false) ? 'material-symbols-filled' : '' }} shrink-0">{{ $item['icon'] }}</span>
                    <span class="whitespace-nowrap transition-all duration-300" :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 w-0'">{{ $item['label'] }}</span>
                </a>
            @empty
                <div class="mx-4 border-[3px] border-[#012d1d] bg-white p-4 text-center text-[#012d1d] shadow-[4px_4px_0_0_#012d1d]"
                     :class="sidebarOpen ? 'block' : 'hidden'">
                    <p class="font-label text-[10px] font-bold uppercase tracking-widest">No admin access</p>
                </div>
            @endforelse
        </div>
    </nav>

    {{-- ── TOPBAR (mobile) ─────────────────────────────────────────────── --}}
    <header class="md:hidden flex justify-between items-center w-full px-6 py-4 sticky top-0 z-50 bg-[#f4fbf7] border-b-4 border-[#012d1d] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]">
        <span class="font-headline font-black text-xl text-[#012d1d]">Dapute</span>
        <button @click="sidebarOpen = true" class="material-symbols-outlined text-[#012d1d] active:scale-95 transition-transform">menu</button>
    </header>

    {{-- ── MAIN ────────────────────────────────────────────────────────── --}}
    <main class="flex-1 min-h-screen bg-[linear-gradient(180deg,#f4fbf7_0%,#eef5f1_100%)] pb-20 md:pb-0 transition-all duration-300"
          :class="sidebarOpen ? 'md:ml-64' : 'md:ml-20'">
        {{ $slot }}
    </main>



    <x-ui.toast />
    @livewireScripts
</body>
</html>
