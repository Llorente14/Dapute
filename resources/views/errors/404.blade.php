<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found - {{ config('app.name', 'Dapute') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link crossorigin href="https://fonts.gstatic.com" rel="preconnect">
    <link
        href="https://fonts.googleapis.com/css2?family=Epilogue:wght@700;800;900&family=Manrope:wght@400;600;700&family=Space+Grotesk:wght@500;700&display=swap"
        rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap"
        rel="stylesheet">

    <style>
        @keyframes fadeUp {
            from {
                opacity: 0;
                transform: translateY(24px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes gentleSway {

            0%,
            100% {
                transform: rotate(-2deg);
            }

            50% {
                transform: rotate(3deg);
            }
        }

        @keyframes spinSlow {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }

        .anim-fade {
            opacity: 0;
            animation: fadeUp .6s ease forwards;
        }

        .anim-d1 {
            animation-delay: .05s;
        }

        .anim-d2 {
            animation-delay: .15s;
        }

        .anim-d3 {
            animation-delay: .25s;
        }

        .anim-d4 {
            animation-delay: .35s;
        }

        .anim-d5 {
            animation-delay: .45s;
        }

        .anim-sway {
            animation: gentleSway 5s ease-in-out infinite;
        }

        .anim-spin {
            animation: spinSlow 20s linear infinite;
        }
    </style>
</head>

<body class="min-h-screen bg-[#f4fbf7] font-body text-[#012d1d] selection:bg-[#D4EF70] selection:text-[#012d1d]">
    <main class="min-h-screen px-5 py-8 md:px-10 md:py-12">
        <section
            class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-7xl flex-col border-[3px] border-[#012d1d] bg-white shadow-[8px_8px_0_0_#012d1d] md:min-h-[calc(100vh-6rem)]">

            {{-- ═══ Header Bar ═══ --}}
            <header
                class="flex items-center justify-between border-b-[3px] border-[#012d1d] bg-[#eef5f1] px-5 py-4 md:px-8 anim-fade anim-d1">
                <a href="/"
                    class="font-headline text-2xl font-black uppercase tracking-tighter text-[#012d1d] md:text-3xl transition-colors hover:text-[#D4EF70]">
                    Dapute
                </a>
                <span
                    class="border-[3px] border-[#012d1d] bg-[#D4EF70] px-3 py-2 font-label text-xs font-bold uppercase tracking-widest shadow-[3px_3px_0_0_#012d1d]">
                    404
                </span>
            </header>

            {{-- ═══ Content Area ═══ --}}
            <div class="relative flex flex-1 items-center overflow-hidden bg-white">

                {{-- Decorative background shapes (desktop only) --}}
                <div class="absolute right-6 top-6 hidden h-28 w-28 rotate-6 border-[3px] border-[#012d1d] bg-[#D4EF70]/40 shadow-[4px_4px_0_0_#012d1d] lg:block anim-spin"
                    style="border-radius: 30% 70% 70% 30% / 30% 30% 70% 70%;"></div>
                <div
                    class="absolute bottom-10 left-10 hidden h-16 w-16 -rotate-12 border-[3px] border-[#012d1d] bg-[#eef5f1] shadow-[3px_3px_0_0_#012d1d] xl:block">
                </div>

                {{-- Two-column grid: Text | Image --}}
                <div
                    class="relative z-10 grid w-full items-center gap-8 p-6 md:p-10 lg:grid-cols-2 lg:gap-12 lg:p-14 xl:p-16">

                    {{-- LEFT: Text & CTA --}}
                    <div class="anim-fade anim-d2">
                        <p class="mb-4 font-label text-xs font-bold uppercase tracking-[0.28em] text-[#414844]">
                            Missing Batch
                        </p>

                        <div class="grid items-center gap-4 sm:grid-cols-[minmax(0,1fr)_150px] lg:block">
                            <h1
                                class="font-headline text-5xl font-black uppercase leading-[0.9] tracking-tighter text-[#012d1d] sm:text-6xl lg:text-7xl xl:text-8xl anim-fade anim-d3">
                                Page Not Found
                            </h1>
                            {{-- Mobile-only image --}}
                            <img src="{{ asset('images/errors/not_found.webp') }}" alt="Confused customer illustration"
                                class="mx-auto aspect-square w-40 object-contain sm:w-full lg:hidden" width="1024"
                                height="1024">
                        </div>

                        <div class="mt-6 max-w-xl border-l-[6px] border-[#D4EF70] pl-4 anim-fade anim-d4">
                            <p class="font-label text-[11px] font-bold uppercase tracking-[0.22em] text-[#012d1d]">
                                No route, no crumbs.
                            </p>
                            <p class="mt-2 font-body text-base font-semibold leading-7 text-[#414844] md:text-lg">
                                This page is not on the shelf anymore. Head back home and continue browsing fresh Dapute
                                goods.
                            </p>
                        </div>

                        <div class="mt-8 flex flex-col gap-3 sm:flex-row anim-fade anim-d5">
                            <a href="/"
                                class="inline-flex items-center justify-center gap-2 border-[3px] border-[#012d1d] bg-[#012d1d] px-6 py-4 font-label text-xs font-bold uppercase tracking-widest text-white shadow-[4px_4px_0_0_#D4EF70] transition-all hover:-translate-x-1 hover:-translate-y-1 hover:shadow-[6px_6px_0_0_#D4EF70] focus:outline-none focus:ring-[3px] focus:ring-[#D4EF70]">
                                <span class="material-symbols-outlined text-base">home</span>
                                Back To Home
                            </a>
                            <a href="/catalog"
                                class="inline-flex items-center justify-center gap-2 border-[3px] border-[#012d1d] bg-white px-6 py-4 font-label text-xs font-bold uppercase tracking-widest text-[#012d1d] shadow-[4px_4px_0_0_#012d1d] transition-all hover:-translate-x-1 hover:-translate-y-1 hover:bg-[#D4EF70] hover:shadow-[6px_6px_0_0_#012d1d] focus:outline-none focus:ring-[3px] focus:ring-[#D4EF70]">
                                <span class="material-symbols-outlined text-base">bakery_dining</span>
                                Browse Catalog
                            </a>
                        </div>
                    </div>

                    {{-- RIGHT: Desktop illustration --}}
                    <div class="hidden lg:flex lg:items-center lg:justify-center">
                        <div class="relative flex items-center justify-center anim-fade anim-d3">
                            {{-- Decorative rectangle behind image --}}
                            <div
                                class="absolute h-64 w-64 rotate-3 border-[3px] border-[#012d1d] bg-[#D4EF70] shadow-[6px_6px_0_0_#012d1d] xl:h-80 xl:w-80">
                            </div>
                            {{-- Second smaller accent shape --}}
                            <div
                                class="absolute -bottom-4 -left-6 h-16 w-16 rotate-12 border-[3px] border-[#012d1d] bg-[#eef5f1] shadow-[3px_3px_0_0_#012d1d]">
                            </div>
                            {{-- The illustration --}}
                            <img src="{{ asset('images/errors/not_found.webp') }}"
                                alt="Confused customer illustration for missing page"
                                class="anim-sway relative z-10 aspect-square w-[280px] object-contain xl:w-[340px]"
                                width="1024" height="1024">
                        </div>
                    </div>

                </div>
            </div>

            {{-- ═══ Footer strip ═══ --}}
            <footer class="border-t-[3px] border-[#012d1d] bg-[#eef5f1] px-5 py-3 md:px-8 anim-fade anim-d5">
                <p class="font-label text-[10px] font-bold uppercase tracking-[0.2em] text-[#414844]">
                    &copy; {{ date('Y') }} Dapute &mdash; Error 404
                </p>
            </footer>
        </section>
    </main>
</body>

</html>
