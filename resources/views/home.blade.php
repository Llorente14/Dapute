@extends('layouts.app')

@push('head')
<title>Dapute — Artisan Cookies, Made to Order | Fresh Dry Cookies Delivered</title>
<meta name="description" content="Order handcrafted dry cookies from Dapute. Made fresh after payment, shipped to your door. Browse our cookie catalog and order online today."/>
<meta name="keywords" content="artisan cookies, dry cookies, kue kering, order online, fresh baked, Dapute"/>
<meta property="og:title" content="Dapute — Artisan Cookies, Made to Order"/>
<meta property="og:description" content="Order handcrafted dry cookies from Dapute. Made fresh after payment, shipped to your door."/>
<meta property="og:image" content="{{ asset('images/hero-cookies.webp') }}"/>
<meta property="og:type" content="website"/>
<meta property="og:url" content="{{ url('/') }}"/>
<style>
    html { scroll-behavior: smooth; }
    .material-symbols-outlined {
        font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
    }
</style>
@endpush

@section('content')
    @include('home.sections.hero')
    @livewire('landing-page')
    @include('home.sections.how-it-works')
    @include('home.sections.trust')
    @include('home.sections.cta-banner')
    @include('home.sections.testimonials')
    @include('home.sections.footer')
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/ScrollTrigger.min.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        gsap.registerPlugin(ScrollTrigger);
        let mm = gsap.matchMedia();

        mm.add("(min-width: 1024px)", () => {
            if (document.querySelector('.product-grid')) {
                gsap.from(".product-grid > div", {
                    scrollTrigger: { trigger: ".product-grid", start: "top 85%" },
                    y: 60, x: -40, opacity: 0, duration: 0.8, stagger: 0.15, ease: "power2.out"
                });
            }
            if (document.querySelector('.steps-grid')) {
                gsap.from(".steps-grid > div", {
                    scrollTrigger: { trigger: ".steps-grid", start: "top 85%" },
                    y: 60, x: -40, opacity: 0, duration: 0.8, stagger: 0.2, ease: "power2.out"
                });
            }
            if (document.querySelector('.badge-fresh-daily')) {
                gsap.set('.badge-fresh-daily', { rotation: -3 });
                gsap.to('.badge-fresh-daily', {
                    rotation: 3, yoyo: true, repeat: -1, duration: 2,
                    ease: "sine.inOut", transformOrigin: "center center"
                });
            }
            const footerInput = document.querySelector('.footer-input');
            if (footerInput) {
                footerInput.addEventListener('focus', () => {
                    gsap.to(footerInput, { scale: 1.02, y: -2, boxShadow: "4px 4px 0px 0px #012d1d", duration: 0.2, ease: "power2.out" });
                });
                footerInput.addEventListener('blur', () => {
                    gsap.to(footerInput, { scale: 1, y: 0, boxShadow: "none", duration: 0.2, ease: "power2.in" });
                });
            }
        });
    });
</script>
@endpush
