<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Dapute') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;700;800;900&family=Manrope:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        .neo-border { border: 3px solid #012d1d; }
        .neo-shadow { box-shadow: 4px 4px 0px 0px #012d1d; }
        .neo-shadow-hover:hover { box-shadow: 6px 6px 0px 0px #012d1d; transform: translate(-2px, -2px); }
        .neo-input-focus:focus-within { background-color: #c1ecd4; }
    </style>
    @stack('head')
</head>
<body class="bg-[#f4fbf7] font-body antialiased min-h-screen flex flex-col pb-20 md:pb-0">
    @php $isLoggedIn = session()->has('supabase_token'); @endphp

    {{-- Top Navbar (Desktop) --}}
    <nav class="hidden md:flex justify-between items-center w-full px-6 py-4 sticky top-0 z-50 backdrop-blur-md bg-[#f4fbf7]/95 border-b-4 border-[#012d1d] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]">
        <div class="flex items-center gap-8">
            <a class="text-2xl font-black tracking-tighter text-[#012d1d] font-headline uppercase" href="/">Dapute</a>
            <div class="flex gap-6">
                <a class="font-headline font-black tracking-tight uppercase text-[#012d1d]/70 hover:text-[#012d1d] hover:bg-[#d8e2dc] transition-all duration-100 px-2 py-1" href="/catalog">Shop</a>
                <a class="font-headline font-black tracking-tight uppercase text-[#012d1d]/70 hover:text-[#012d1d] hover:bg-[#d8e2dc] transition-all duration-100 px-2 py-1" href="#how-it-works">About</a>
            </div>
        </div>
        <div class="flex items-center gap-4 text-[#012d1d]">
            @if ($isLoggedIn)
                <button class="hover:bg-[#d8e2dc] transition-all duration-100 p-2 active:translate-x-0.5 active:translate-y-0.5">
                    <span class="material-symbols-outlined">shopping_cart</span>
                </button>
                <a href="/profile" class="text-[#012d1d] border-b-4 border-[#012d1d] pb-1 p-2 active:translate-x-0.5 active:translate-y-0.5">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">account_circle</span>
                </a>
            @else
                <a href="/login" class="text-[#012d1d] font-label font-bold uppercase tracking-widest hover:bg-[#d8e2dc] px-4 py-2 transition-colors">Sign In</a>
            @endif
        </div>
    </nav>

    <main class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    {{-- Mobile Bottom Nav --}}
    <nav class="md:hidden fixed bottom-0 left-0 w-full z-50 grid h-16 bg-[#f4fbf7] border-t-4 border-[#012d1d] {{ $isLoggedIn ? 'grid-cols-4' : 'grid-cols-3' }}">
        <a class="flex flex-col items-center justify-center text-[#012d1d]/60 py-2 h-full hover:bg-[#d8e2dc]/50 active:scale-95 transition-transform" href="/catalog">
            <span class="material-symbols-outlined mb-1 text-xl">bakery_dining</span>
            <span class="font-label font-bold text-[10px] uppercase">Shop</span>
        </a>
        <a class="flex flex-col items-center justify-center text-[#012d1d]/60 py-2 h-full hover:bg-[#d8e2dc]/50 active:scale-95 transition-transform" href="#">
            <span class="material-symbols-outlined mb-1 text-xl">local_shipping</span>
            <span class="font-label font-bold text-[10px] uppercase">Orders</span>
        </a>
        @if ($isLoggedIn)
            <a class="flex flex-col items-center justify-center bg-[#d8e2dc] text-[#012d1d] py-2 h-full active:scale-95 transition-transform border-t-4 border-[#012d1d] -mt-1" href="/profile">
                <span class="material-symbols-outlined mb-1 text-xl" style="font-variation-settings: 'FILL' 1;">person</span>
                <span class="font-label font-bold text-[10px] uppercase">Profile</span>
            </a>
            <a class="flex flex-col items-center justify-center text-[#012d1d]/60 py-2 h-full hover:bg-[#d8e2dc]/50 active:scale-95 transition-transform" href="/admin/products">
                <span class="material-symbols-outlined mb-1 text-xl">dashboard</span>
                <span class="font-label font-bold text-[10px] uppercase">Admin</span>
            </a>
        @else
            <a class="flex flex-col items-center justify-center text-[#012d1d]/60 py-2 h-full hover:bg-[#d8e2dc]/50 active:scale-95 transition-transform" href="/login">
                <span class="material-symbols-outlined mb-1 text-xl">login</span>
                <span class="font-label font-bold text-[10px] uppercase">Sign In</span>
            </a>
        @endif
    </nav>

    <x-ui.toast />
    @stack('scripts')
    @livewireScripts
</body>
</html>
