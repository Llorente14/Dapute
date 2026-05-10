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

    {{-- 
        ====================================================================
        DYNAMIC NAVBAR COMPONENT
        ====================================================================
        Navbar ini sudah menggunakan Livewire. Secara default akan menampilkan:
        - Home
        - Shop (Catalog)
        - Track (Tracking Order)
        
        Jika ingin mengganti link secara dinamis (misalnya di page tertentu), 
        Anda bisa memanggil komponen ini di layout/page Anda dengan mengirimkan
        array links. Contoh penggunaannya:
        
        <livewire:navbar :links="[
            ['name' => 'Menu 1', 'url' => '/menu1', 'icon' => 'home'],
            ['name' => 'Menu 2', 'url' => '/menu2', 'icon' => 'shopping_cart']
        ]" />
    --}}
    <livewire:navbar />
    <!-- Main Content -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
