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
        <div class="space-y-6" x-data="{ 
            email: '', 
            isSubmitting: false, 
            showToast: false,
            submit() {
                if(!this.email || this.isSubmitting) return;
                this.isSubmitting = true;
                setTimeout(() => {
                    this.isSubmitting = false;
                    this.email = '';
                    this.showToast = true;
                    setTimeout(() => this.showToast = false, 4000);
                }, 800);
            }
        }">
            <h4 class="font-label font-bold uppercase tracking-widest text-primary">Newsletter</h4>
            <div class="flex neo-shadow" :class="{ 'opacity-70': isSubmitting }">
                <input
                    x-model="email"
                    :disabled="isSubmitting"
                    class="bg-surface-container-lowest border-[3px] border-primary p-3 w-full font-body focus:outline-none focus:ring-0 focus:border-primary footer-input transition-colors duration-200"
                    placeholder="Your email" type="email" aria-label="Email for newsletter" />
                <button
                    @click="submit()"
                    :disabled="!email || isSubmitting"
                    class="bg-primary text-on-primary w-14 border-[3px] border-l-0 border-primary font-label uppercase tracking-widest font-bold transition-colors flex items-center justify-center disabled:bg-primary/60 disabled:cursor-not-allowed hover:bg-primary/90"
                    aria-label="Subscribe to newsletter">
                    <span x-show="!isSubmitting" class="material-symbols-outlined">arrow_forward</span>
                    <span x-show="isSubmitting" class="material-symbols-outlined animate-spin" style="display: none;">progress_activity</span>
                </button>
            </div>

            {{-- Toast Notification --}}
            <template x-teleport="body">
                <div 
                    x-show="showToast"
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-8"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-8"
                    style="display: none;"
                    class="fixed bottom-6 right-6 md:bottom-8 md:right-8 z-50 bg-[#D4EF70] border-[3px] border-[#012d1d] shadow-[4px_4px_0_0_#012d1d] p-4 flex items-start gap-4 max-w-sm"
                >
                    <span class="material-symbols-outlined text-[#012d1d] text-2xl animate-bounce">check_circle</span>
                    <div class="flex-1">
                        <p class="font-label font-bold text-[#012d1d] uppercase tracking-widest text-sm mb-1">Berhasil</p>
                        <p class="font-body text-[#012d1d] text-sm">Terima kasih! Kami akan segera menghubungi Anda.</p>
                    </div>
                    <button @click="showToast = false" class="text-[#012d1d] hover:opacity-70">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>
            </template>
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
