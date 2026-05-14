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
    <link
        href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;700;800;900&family=Manrope:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">
    <style>
        .neo-border {
            border: 3px solid #012d1d;
        }

        .neo-shadow {
            box-shadow: 4px 4px 0px 0px #012d1d;
        }

        .neo-shadow-hover:hover {
            box-shadow: 6px 6px 0px 0px #012d1d;
            transform: translate(-2px, -2px);
        }

        .neo-input-focus:focus-within {
            background-color: #c1ecd4;
        }

        /* Nav link: hover = acid yellow lift, active = tertiary bg + bottom border */
        .nav-icon-btn-sm {
            transition: background-color 80ms, box-shadow 80ms, transform 80ms, border-color 80ms;
            padding: 0.375rem;
            border: 2px solid transparent;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-icon-btn-sm:hover {
            background-color: #D4EF70;
            border: 2px solid #012d1d;
            box-shadow: 2px 2px 0px 0px #012d1d;
            transform: translate(-1px, -1px);
        }

        .nav-icon-btn-sm:active {
            border: 2px solid #012d1d;
            box-shadow: 1px 1px 0px 0px #012d1d;
            transform: translate(1px, 1px);
        }

        .nav-icon-btn-sm:focus-visible {
            outline: none;
            background-color: #D4EF70;
            border: 2px solid #012d1d;
            box-shadow: 2px 2px 0px 0px #012d1d;
        }

        .nav-icon-btn-sm.active {
            background-color: #D4EF70;
            border: 2px solid #012d1d;
            box-shadow: 2px 2px 0px 0px #012d1d;
        }

        /* Nav text link: 2px border, 3px shadow (text lebih besar) */
        .nav-link {
            position: relative;
            transition: background-color 80ms, box-shadow 80ms, transform 80ms, border-color 80ms;
            padding: 0.25rem 0.5rem;
            outline-offset: 2px;
            border: 2px solid transparent;
        }

        .nav-link:hover {
            background-color: #D4EF70;
            color: #012d1d;
            border: 2px solid #012d1d;
            box-shadow: 2px 2px 0px 0px #012d1d;
            transform: translate(-1px, -1px);
        }

        .nav-link:focus-visible {
            outline: none;
            background-color: #D4EF70;
            border: 2px solid #012d1d;
            box-shadow: 2px 2px 0px 0px #012d1d;
        }

        .nav-link:active {
            background-color: #D4EF70;
            border: 2px solid #012d1d;
            box-shadow: 1px 1px 0px 0px #012d1d;
            transform: translate(1px, 1px);
        }

        .nav-link.active {
            background-color: #D4EF70;
            border: 2px solid #012d1d;
            box-shadow: 2px 2px 0px 0px #012d1d;
            color: #012d1d;
        }

        /* Logo button */
        .nav-logo {
            transition: background-color 80ms, box-shadow 80ms, transform 80ms, border-color 80ms;
            padding: 0.25rem 0.5rem;
            border: 2px solid transparent;
        }

        .nav-logo:hover {
            background-color: #D4EF70;
            border: 2px solid #012d1d;
            box-shadow: 2px 2px 0px 0px #012d1d;
            transform: translate(-1px, -1px);
        }

        .nav-logo:focus-visible {
            outline: none;
            background-color: #D4EF70;
            border: 2px solid #012d1d;
            box-shadow: 2px 2px 0px 0px #012d1d;
        }

        /* Mobile nav item */
        .mobile-nav-item {
            transition: background-color 80ms, transform 80ms;
        }

        .mobile-nav-item:hover {
            background-color: #D4EF70;
        }

        .mobile-nav-item:active {
            transform: scale(0.93);
            background-color: #D4EF70;
        }

        .mobile-nav-item.active {
            background-color: #D4EF70;
            border-top: 2px solid #012d1d;
            /* 4px → 2px */
            margin-top: -2px;
            color: #012d1d;
        }
    </style>
    @stack('head')
</head>

<body class="bg-[#f4fbf7] font-body antialiased min-h-screen flex flex-col pb-20 md:pb-0">
    @php
        $isLoggedIn = session()->has('supabase_token');
        $currentPath = request()->path();
    @endphp

    <nav
        class="hidden md:flex justify-between items-center w-full px-6 py-4 sticky top-0 z-50 backdrop-blur-md bg-[#f4fbf7]/95 border-b-4 border-[#012d1d] shadow-[4px_4px_0px_0px_rgba(1,45,29,1)]">
        <div class="flex items-center gap-8">
            <a class="text-2xl font-black tracking-tighter text-[#012d1d] font-headline uppercase nav-icon-btn"
                href="/">Dapute</a>
            <div class="flex gap-2">
                <a class="font-headline font-black tracking-tight uppercase text-[#012d1d] nav-link {{ $currentPath === '/' ? 'active' : '' }}"
                    href="/">Home</a>
                <a class="font-headline font-black tracking-tight uppercase text-[#012d1d] nav-link {{ str_starts_with($currentPath, 'catalog') ? 'active' : '' }}"
                    href="/catalog">Shop</a>
                @if ($isLoggedIn)
                    <a class="font-headline font-black tracking-tight uppercase text-[#012d1d] nav-link {{ str_starts_with($currentPath, 'orders') ? 'active' : '' }}"
                        href="/orders">Orders</a>
                @endif
                <a class="font-headline font-black tracking-tight uppercase text-[#012d1d] nav-link {{ str_starts_with($currentPath, 'about') ? 'active' : '' }}"
                    href="/about">About</a>
            </div>
        </div>

        <!-- Desktop nav section, ganti bagian icon-right: -->
        <div class="flex items-center gap-1 text-[#012d1d]">
            @if ($isLoggedIn)
                <button class="nav-icon-btn-sm" onclick="window.dispatchEvent(new CustomEvent('open-cart'))">
                    <span class="material-symbols-outlined">shopping_cart</span>
                </button>
                <a href="/profile"
                    class="nav-icon-btn-sm {{ str_starts_with($currentPath, 'profile') ? 'active' : '' }}">
                    <span class="material-symbols-outlined"
                        style="font-variation-settings: 'FILL' 1;">account_circle</span>
                </a>
            @else
                <a href="/login" class="nav-link font-label font-bold uppercase tracking-widest">Sign In</a>
            @endif
        </div>
    </nav>

    <main class="flex-1">
        {{ $slot ?? '' }}
        @yield('content')
    </main>

    {{-- Mobile Bottom Nav --}}
    <nav
        class="md:hidden fixed bottom-0 left-0 w-full z-50 grid h-16 bg-[#f4fbf7] border-t-4 border-[#012d1d] {{ $isLoggedIn ? 'grid-cols-5' : 'grid-cols-4' }}">
        <a class="mobile-nav-item flex flex-col items-center justify-center text-[#012d1d] py-2 h-full {{ $currentPath === '/' ? 'active' : 'opacity-60' }}"
            href="/">
            <span class="material-symbols-outlined mb-1 text-xl">home</span>
            <span class="font-label font-bold text-[10px] uppercase">Home</span>
        </a>
        <a class="mobile-nav-item flex flex-col items-center justify-center text-[#012d1d] py-2 h-full {{ str_starts_with($currentPath, 'catalog') ? 'active' : 'opacity-60' }}"
            href="/catalog">
            <span class="material-symbols-outlined mb-1 text-xl">bakery_dining</span>
            <span class="font-label font-bold text-[10px] uppercase">Shop</span>
        </a>

        @if ($isLoggedIn)
            <button class="mobile-nav-item flex flex-col items-center justify-center text-[#012d1d] py-2 h-full opacity-60"
                onclick="window.dispatchEvent(new CustomEvent('open-cart'))">
                <span class="material-symbols-outlined mb-1 text-xl">shopping_cart</span>
                <span class="font-label font-bold text-[10px] uppercase">Cart</span>
            </button>
            <a class="mobile-nav-item flex flex-col items-center justify-center text-[#012d1d] py-2 h-full {{ str_starts_with($currentPath, 'orders') ? 'active' : 'opacity-60' }}"
                href="/orders">
                <span class="material-symbols-outlined mb-1 text-xl">local_shipping</span>
                <span class="font-label font-bold text-[10px] uppercase">Orders</span>
            </a>
            <a class="mobile-nav-item flex flex-col items-center justify-center text-[#012d1d] py-2 h-full {{ str_starts_with($currentPath, 'profile') ? 'active' : 'opacity-60' }}"
                href="/profile">
                <span class="material-symbols-outlined mb-1 text-xl"
                    style="font-variation-settings: 'FILL' 1;">person</span>
                <span class="font-label font-bold text-[10px] uppercase">Profile</span>
            </a>
        @else
            <a class="mobile-nav-item flex flex-col items-center justify-center text-[#012d1d] py-2 h-full {{ str_starts_with($currentPath, 'login') ? 'active' : 'opacity-60' }}"
                href="/login">
                <span class="material-symbols-outlined mb-1 text-xl">login</span>
                <span class="font-label font-bold text-[10px] uppercase">Sign In</span>
            </a>
        @endif
    </nav>

    <x-ui.cart-drawer />
    <x-ui.toast />
    @stack('scripts')
    @livewireScripts
</body>

</html>
