<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>

    {{-- SEO --}}
    <title>Dapute — Artisan Cookies, Made to Order | Fresh Dry Cookies Delivered</title>
    <meta name="description" content="Order handcrafted dry cookies from Dapute. Made fresh after payment, shipped to your door. Browse our cookie catalog and order online today."/>
    <meta name="keywords" content="artisan cookies, dry cookies, kue kering, order online, fresh baked, Dapute"/>

    {{-- Open Graph --}}
    <meta property="og:title" content="Dapute — Artisan Cookies, Made to Order"/>
    <meta property="og:description" content="Order handcrafted dry cookies from Dapute. Made fresh after payment, shipped to your door. Browse our cookie catalog and order online today."/>
    <meta property="og:image" content="{{ asset('images/hero-cookies.webp') }}"/>
    <meta property="og:type" content="website"/>
    <meta property="og:url" content="{{ url('/') }}"/>

    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image"/>
    <meta name="twitter:title" content="Dapute — Artisan Cookies, Made to Order"/>
    <meta name="twitter:description" content="Order handcrafted dry cookies from Dapute. Made fresh after payment, shipped to your door."/>
    <meta name="twitter:image" content="{{ asset('images/hero-cookies.webp') }}"/>

    {{-- Fonts & Icons --}}
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@700;900&family=Manrope:wght@400;500;700&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    {{-- Tailwind CDN + Config (homepage standalone) --}}
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    colors: {
                        "surface-dim": "#d5dcd8",
                        "inverse-on-surface": "#ebf2ee",
                        "on-secondary-fixed-variant": "#3f4945",
                        "inverse-primary": "#a5d0b9",
                        "secondary-container": "#d8e2dc",
                        "tertiary-container": "#354100",
                        "on-secondary-container": "#5b6560",
                        "secondary-fixed": "#dbe5df",
                        "primary-container": "#1b4332",
                        "on-error": "#ffffff",
                        "on-tertiary-container": "#98b139",
                        "error": "#ba1a1a",
                        "on-background": "#161d1b",
                        "primary-fixed": "#c1ecd4",
                        "tertiary": "#212a00",
                        "on-primary": "#ffffff",
                        "tertiary-fixed": "#d3ee6f",
                        "on-primary-container": "#86af99",
                        "on-secondary": "#ffffff",
                        "on-secondary-fixed": "#151d1a",
                        "on-primary-fixed": "#002114",
                        "surface-bright": "#f4fbf7",
                        "surface-variant": "#dde4e0",
                        "secondary-fixed-dim": "#bfc9c3",
                        "primary": "#012d1d",
                        "outline": "#717973",
                        "error-container": "#ffdad6",
                        "surface-container-high": "#e3eae6",
                        "on-tertiary-fixed": "#171e00",
                        "surface-container-lowest": "#ffffff",
                        "surface-tint": "#3f6653",
                        "primary-fixed-dim": "#a5d0b9",
                        "on-error-container": "#93000a",
                        "secondary": "#57615c",
                        "surface-container-low": "#eef5f1",
                        "background": "#f4fbf7",
                        "outline-variant": "#c1c8c2",
                        "on-tertiary": "#ffffff",
                        "surface-container": "#e8efec",
                        "on-surface": "#161d1b",
                        "surface": "#f4fbf7",
                        "on-surface-variant": "#414844",
                        "tertiary-fixed-dim": "#b8d256",
                        "surface-container-highest": "#dde4e0",
                        "inverse-surface": "#2b322f",
                        "on-primary-fixed-variant": "#274e3d",
                        "on-tertiary-fixed-variant": "#3e4c00"
                    },
                    fontFamily: {
                        headline: ["Epilogue", "sans-serif"],
                        body: ["Manrope", "sans-serif"],
                        label: ["Space Grotesk", "sans-serif"]
                    }
                }
            }
        }
    </script>

    {{-- Homepage-specific styles --}}
    <style>
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .neo-shadow {
            box-shadow: 4px 4px 0px 0px #012d1d;
        }
        .neo-shadow-hover:hover {
            box-shadow: 6px 6px 0px 0px #012d1d;
        }

        /* Smooth scroll for anchor links */
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="bg-background text-on-background font-body selection:bg-tertiary-fixed selection:text-on-tertiary-fixed antialiased">

    {{-- ═══ NAVBAR SLOT ═══════════════════════════════════════════════════════
         Komponen navbar dikerjakan terpisah.
         Sematkan di sini nanti: @include('components.navbar') atau <x-navbar />
    ═══════════════════════════════════════════════════════════════════════════ --}}
    <header class="fixed top-0 w-full bg-[#f4fbf7]/80 backdrop-blur-md z-50 border-b-[3px] border-primary flex justify-center items-center h-20 shadow-[4px_4px_0px_0px_#012d1d]">
        <div class="flex justify-between items-center w-full max-w-[1200px] px-4 md:px-8">
            {{-- Navbar placeholder — will be replaced by navbar component --}}
            <div class="text-3xl font-headline font-black tracking-tighter text-primary">DAPUTE</div>
            <nav class="hidden md:flex gap-8 items-center">
                <a class="text-primary border-b-[3px] border-primary pb-1 font-bold font-label uppercase tracking-widest" href="/">Home</a>
                <a class="text-primary/70 font-medium font-label uppercase tracking-widest hover:bg-secondary-container transition-transform active:translate-y-[-2px]" href="/catalog">Catalog</a>
                <a class="text-primary/70 font-medium font-label uppercase tracking-widest hover:bg-secondary-container transition-transform active:translate-y-[-2px]" href="#how-it-works">How It Works</a>
            </nav>
            <div class="flex gap-6 items-center">
                <a href="/login" class="text-primary font-label font-bold uppercase tracking-widest hover:bg-secondary-container px-4 py-2 transition-colors">Sign In</a>
            </div>
        </div>
    </header>

    <main class="pt-20">
        @include('home.sections.hero')
        @include('home.sections.products')
        @include('home.sections.how-it-works')
        @include('home.sections.trust')
        @include('home.sections.cta-banner')
        @include('home.sections.testimonials')
    </main>

    @include('home.sections.footer')

    {{-- GSAP Desktop Animations --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            gsap.registerPlugin(ScrollTrigger);
            let mm = gsap.matchMedia();

            // Desktop only animations (1024px and up)
            mm.add("(min-width: 1024px)", () => {
                
                // 1. Scroll Animation: Our Collection (Products)
                if (document.querySelector('.product-grid')) {
                    gsap.from(".product-grid > div", {
                        scrollTrigger: {
                            trigger: ".product-grid",
                            start: "top 85%", // Starts animation when the top of the grid hits 85% down the viewport
                        },
                        y: 60,
                        x: -40,
                        opacity: 0,
                        duration: 0.8,
                        stagger: 0.15, // Elements appear one after another
                        ease: "power2.out"
                    });
                }

                // 2. Scroll Animation: How It Works
                if (document.querySelector('.steps-grid')) {
                    gsap.from(".steps-grid > div", {
                        scrollTrigger: {
                            trigger: ".steps-grid",
                            start: "top 85%",
                        },
                        y: 60,
                        x: -40,
                        opacity: 0,
                        duration: 0.8,
                        stagger: 0.2, // Slightly slower stagger for these wider cards
                        ease: "power2.out"
                    });
                }

                // 3. FRESH DAILY Badge continuous wobble
                if (document.querySelector('.badge-fresh-daily')) {
                    gsap.set('.badge-fresh-daily', { rotation: -3 });
                    gsap.to('.badge-fresh-daily', {
                        rotation: 3,
                        yoyo: true,
                        repeat: -1,
                        duration: 2,
                        ease: "sine.inOut",
                        transformOrigin: "center center"
                    });
                }

                // 4. Footer Input Focus Animation
                const footerInput = document.querySelector('.footer-input');
                if (footerInput) {
                    footerInput.addEventListener('focus', () => {
                        gsap.to(footerInput, {
                            scale: 1.02,
                            y: -2,
                            boxShadow: "4px 4px 0px 0px #012d1d",
                            duration: 0.2,
                            ease: "power2.out"
                        });
                    });
                    
                    footerInput.addEventListener('blur', () => {
                        gsap.to(footerInput, {
                            scale: 1,
                            y: 0,
                            boxShadow: "none",
                            duration: 0.2,
                            ease: "power2.in"
                        });
                    });
                }
            });
        });
    </script>
</body>
</html>
