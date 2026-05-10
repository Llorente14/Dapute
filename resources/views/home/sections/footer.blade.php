{{-- ═══ FOOTER ════════════════════════════════════════════════════════════════════ --}}
<footer class="bg-surface-container-lowest text-primary border-t-[3px] border-primary w-full">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-12 px-8 py-16 max-w-[1200px] mx-auto">
        {{-- Brand --}}
        <div class="space-y-6">
            <div class="text-3xl font-headline font-black tracking-tighter text-primary">DAPUTE</div>
            <p class="font-body text-primary/80">Handcrafted Cookies · Made to Order. Structure and flavor in every bite.
            </p>
        </div>

        {{-- Navigation --}}
        <div class="space-y-4">
            <h4 class="font-label font-bold uppercase tracking-widest text-primary">Navigate</h4>
            <ul class="space-y-2">
                <li><a class="font-body hover:underline decoration-2 underline-offset-4 transition-all"
                        href="/">Home</a></li>
                <li><a class="font-body hover:underline decoration-2 underline-offset-4 transition-all"
                        href="/catalog">Catalog</a></li>
                <li><a class="font-body hover:underline decoration-2 underline-offset-4 transition-all"
                        href="#how-it-works">How It Works</a></li>
            </ul>
        </div>

        {{-- Support --}}
        <div class="space-y-4">
            <h4 class="font-label font-bold uppercase tracking-widest text-primary">Support</h4>
            <ul class="space-y-2">
                <li><a class="font-body hover:underline decoration-2 underline-offset-4 transition-all"
                        href="#">Shipping</a></li>
                <li><a class="font-body hover:underline decoration-2 underline-offset-4 transition-all"
                        href="#">Terms</a></li>
                <li><a class="font-body hover:underline decoration-2 underline-offset-4 transition-all"
                        href="#">FAQ</a></li>
            </ul>
        </div>

        {{-- Newsletter --}}
        <div class="space-y-6">
            <h4 class="font-label font-bold uppercase tracking-widest text-primary">Newsletter</h4>
            <div class="flex neo-shadow">
                <input
                    class="bg-surface-container-lowest border-[3px] border-primary p-3 w-full font-body focus:outline-none focus:ring-0 focus:border-primary footer-input transition-colors duration-200"
                    placeholder="Your email" type="email" aria-label="Email for newsletter" />
                <button
                    class="bg-primary text-on-primary px-4 border-[3px] border-l-0 border-primary font-label uppercase tracking-widest font-bold hover:bg-primary/90 transition-colors"
                    aria-label="Subscribe to newsletter">
                    <span class="material-symbols-outlined">arrow_forward</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Bottom bar --}}
    <div
        class="px-8 py-8 border-t-[3px] border-primary flex flex-col md:flex-row justify-between items-center gap-4 bg-primary text-on-primary">
        <p class="font-label text-xs uppercase tracking-widest font-bold text-on-primary/80">© 2026 Dapute. All rights
            reserved.</p>
        <div class="flex gap-6">
            {{-- Instagram placeholder --}}
            <a class="material-symbols-outlined hover:text-tertiary-fixed transition-colors" href="#"
                aria-label="Instagram">share</a>
            <a class="material-symbols-outlined hover:text-tertiary-fixed transition-colors" href="#"
                aria-label="Website">language</a>
            <a class="material-symbols-outlined hover:text-tertiary-fixed transition-colors" href="#"
                aria-label="Email">mail</a>
        </div>
    </div>
</footer>
