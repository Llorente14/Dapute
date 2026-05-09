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
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@700;900&family=Manrope:wght@400;500&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
</head>
<body class="bg-[#f4fbf7] font-manrope antialiased">

    {{-- Navbar slot or component here --}}

    <main>
        {{ $slot }}
    </main>

    @livewireScripts
</body>
</html>
