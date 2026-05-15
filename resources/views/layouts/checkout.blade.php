<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Checkout - {{ config('app.name', 'Dapute') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;700;800;900&family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
</head>
<body class="bg-surface text-on-surface font-body antialiased min-h-screen flex flex-col selection:bg-tertiary-fixed selection:text-primary">
    <!-- Simplified Checkout Header -->
    <header class="w-full border-b-[3px] border-primary bg-surface py-4 px-6 flex justify-between items-center z-50 sticky top-0">
        <a href="/" class="font-headline font-black text-3xl uppercase tracking-tighter text-primary">
            Dapute
        </a>
        <div class="flex items-center gap-2 text-primary font-label font-bold text-xs uppercase tracking-wider">
            <span class="material-symbols-outlined text-[18px]" style="font-variation-settings: 'FILL' 1;">lock</span>
            Secure Checkout
        </div>
    </header>

    <main class="flex-grow w-full max-w-[1440px] mx-auto px-4 md:px-8 py-8 md:py-16">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
