<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Dapute') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    {{-- Google Fonts: Epilogue, Manrope, Space Grotesk, Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;700;800;900&family=Manrope:wght@400;500;600;700&family=Space+Grotesk:wght@400;500;700&family=Plus+Jakarta+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    {{-- Material Symbols --}}
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0&display=swap" rel="stylesheet">
    <style>
        /* Neo-Brutalist Utilities */
        .neo-border { border: 3px solid #012d1d; }
        .neo-shadow { box-shadow: 4px 4px 0px 0px #012d1d; }
        .neo-shadow-hover:hover { box-shadow: 6px 6px 0px 0px #012d1d; transform: translate(-2px, -2px); }
        .neo-input-focus:focus-within { background-color: #c1ecd4; }
    </style>
</head>
<body class="bg-[#f4fbf7] font-body antialiased min-h-screen flex flex-col pb-20 md:pb-0">

    <x-navbar />

    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    {{-- Toast Notification Component --}}
    <x-ui.toast />

    @livewireScripts
</body>
</html>
