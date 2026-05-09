<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Dapute') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    {{-- Google Fonts: Epilogue, Manrope, Space Grotesk --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;700;800;900&family=Manrope:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    {{-- Material Symbols --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        /* Neo-Brutalist Utilities */
        .neo-border { border: 3px solid #012d1d; }
        .neo-shadow { box-shadow: 4px 4px 0px 0px #012d1d; }
        .neo-shadow-hover:hover { box-shadow: 6px 6px 0px 0px #012d1d; transform: translate(-2px, -2px); }
        .neo-input-focus:focus-within { background-color: #c1ecd4; }
    </style>
</head>
<body class="bg-[#f4fbf7] font-body antialiased min-h-screen flex flex-col pb-20 md:pb-0">

    <!-- TopNavBar (Hidden on Mobile, Visible on Web) -->
    <nav class="hidden md:flex justify-between items-center w-full px-6 py-4 sticky top-0 z-50 backdrop-blur-md bg-[#f4fbf7]/95 border-b-4 border-[#012d1d] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]">
        <div class="flex items-center gap-8">
            <a class="text-2xl font-black tracking-tighter text-[#012d1d] font-headline uppercase hover:translate-x-[-2px] hover:translate-y-[-2px] transition-all duration-150" href="/">Dapute</a>
            <div class="flex gap-3">
                <a class="font-headline font-black tracking-tight uppercase text-[#012d1d]/70 hover:text-[#012d1d] px-3 py-1.5 border-[3px] border-transparent hover:border-[#012d1d] hover:bg-[#d8e2dc] hover:shadow-[3px_3px_0px_0px_#012d1d] hover:translate-x-[-2px] hover:translate-y-[-2px] active:translate-x-0 active:translate-y-0 active:shadow-none transition-all duration-150" href="#">Shop</a>
                <a class="font-headline font-black tracking-tight uppercase text-[#012d1d]/70 hover:text-[#012d1d] px-3 py-1.5 border-[3px] border-transparent hover:border-[#012d1d] hover:bg-[#d8e2dc] hover:shadow-[3px_3px_0px_0px_#012d1d] hover:translate-x-[-2px] hover:translate-y-[-2px] active:translate-x-0 active:translate-y-0 active:shadow-none transition-all duration-150" href="#">About</a>
                <a class="font-headline font-black tracking-tight uppercase text-[#012d1d]/70 hover:text-[#012d1d] px-3 py-1.5 border-[3px] border-transparent hover:border-[#012d1d] hover:bg-[#d8e2dc] hover:shadow-[3px_3px_0px_0px_#012d1d] hover:translate-x-[-2px] hover:translate-y-[-2px] active:translate-x-0 active:translate-y-0 active:shadow-none transition-all duration-150" href="#">Support</a>
            </div>
        </div>
        <div class="flex items-center gap-3 text-[#012d1d]">
            <button class="p-2 border-[3px] border-transparent hover:border-[#012d1d] hover:bg-[#d8e2dc] hover:shadow-[3px_3px_0px_0px_#012d1d] hover:translate-x-[-2px] hover:translate-y-[-2px] active:translate-x-0 active:translate-y-0 active:shadow-none transition-all duration-150">
                <span class="material-symbols-outlined">shopping_cart</span>
            </button>
            <a href="/profile" class="p-2 border-[3px] border-[#012d1d] bg-[#d8e2dc] shadow-[3px_3px_0px_0px_#012d1d] translate-x-[-2px] translate-y-[-2px] hover:shadow-[5px_5px_0px_0px_#012d1d] hover:translate-x-[-3px] hover:translate-y-[-3px] active:translate-x-0 active:translate-y-0 active:shadow-none transition-all duration-150">
                <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">account_circle</span>
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- BottomNavBar (Visible on Mobile, Hidden on Web) -->
    <nav class="md:hidden fixed bottom-0 left-0 w-full z-50 grid grid-cols-4 h-[68px] bg-[#f4fbf7] border-t-4 border-[#012d1d]">
        <a class="flex flex-col items-center justify-center text-[#012d1d]/60 py-2 h-full border-r-[2px] border-[#012d1d]/10 hover:bg-[#d8e2dc] hover:text-[#012d1d] active:bg-[#012d1d] active:text-[#f4fbf7] transition-all duration-150" href="#">
            <span class="material-symbols-outlined mb-0.5 text-xl">bakery_dining</span>
            <span class="font-label font-bold text-[10px] uppercase">Shop</span>
        </a>
        <a class="flex flex-col items-center justify-center text-[#012d1d]/60 py-2 h-full border-r-[2px] border-[#012d1d]/10 hover:bg-[#d8e2dc] hover:text-[#012d1d] active:bg-[#012d1d] active:text-[#f4fbf7] transition-all duration-150" href="#">
            <span class="material-symbols-outlined mb-0.5 text-xl">local_shipping</span>
            <span class="font-label font-bold text-[10px] uppercase">Orders</span>
        </a>
        <a class="flex flex-col items-center justify-center bg-[#012d1d] text-[#f4fbf7] py-2 h-full border-r-[2px] border-[#012d1d]/10 transition-all duration-150" href="/profile">
            <span class="material-symbols-outlined mb-0.5 text-xl" style="font-variation-settings: 'FILL' 1;">person</span>
            <span class="font-label font-bold text-[10px] uppercase">Profile</span>
        </a>
        <a class="flex flex-col items-center justify-center text-[#012d1d]/60 py-2 h-full hover:bg-[#d8e2dc] hover:text-[#012d1d] active:bg-[#012d1d] active:text-[#f4fbf7] transition-all duration-150" href="#">
            <span class="material-symbols-outlined mb-0.5 text-xl">dashboard</span>
            <span class="font-label font-bold text-[10px] uppercase">Admin</span>
        </a>
    </nav>

    @livewireScripts
</body>
</html>
