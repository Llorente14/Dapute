<div>
    <main class="w-full max-w-[1200px] grid md:grid-cols-2 gap-8 md:gap-0 bg-surface-container-lowest border-[3px] border-primary shadow-brutal m-auto">
        <!-- Left Side: Image / Brand Anchor -->
        <div class="hidden md:block w-full h-full min-h-[600px] border-r-[3px] border-primary relative overflow-hidden bg-surface-container-low">
            <img alt="" class="w-full h-full object-cover grayscale opacity-80 mix-blend-multiply"
                data-alt="dramatic architectural shot of artisan sourdough bread loaves stacked on metal shelving in a greenhouse setting with natural light and deep shadows"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDHuuuy-Pt9CE6I4b6Tb0ybNcf359KPkxGBqO8TBMq_n8iGT3hKSUL1xmTtfXY0J_K18ceVmNMEapcnctBjK18Rsoy83D1TNgSNBKh-VyNMfYkaLoB97-0YGl-S3GT1gckL2iJRKocR4oSEha0OxQrwG2MBnYXzUw3Rd4ySMeJRyQxFPllYDozkhQ0OijTHskpAzNryk6xCAvgW0NqU-i9jORla2o9SB4o-MueGoxd6uGzNvXBBiMUBu8kcb_dwYkJFs1kBvh-LDW89" />
            <div class="absolute inset-0 bg-gradient-to-t from-primary/90 to-primary-container/40 p-12 flex flex-col justify-end">
                <h2 class="font-headline font-black text-7xl text-surface-container-lowest tracking-tighter leading-tight mb-4">
                    Dapute</h2>
                <p class="font-body text-surface-container-highest text-xl max-w-md">Architectural baking. Raw
                    ingredients. Structural honesty.</p>
            </div>
        </div>
        <!-- Right Side: Forms -->
        <div class="w-full p-8 sm:p-14 lg:p-20 flex flex-col justify-center relative bg-surface-container-lowest">
            <!-- Mobile Brand Header -->
            <div class="md:hidden mb-4">
                <h1 class="font-headline font-black text-5xl text-primary tracking-tighter">Dapute</h1>
            </div>

            @if($isSuccess)
                <!-- Success State -->
                <div class="text-center animate-slide-down">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-primary text-surface-container-lowest border-[3px] border-primary shadow-brutal mb-8">
                        <span class="material-symbols-outlined text-[40px]">mail</span>
                    </div>
                    <h2 class="font-headline font-black text-4xl sm:text-5xl text-primary tracking-tight mb-4 uppercase">Check Your Email</h2>
                    <p class="font-body text-lg text-on-surface-variant mb-8">We've sent a password reset link to <strong class="text-primary">{{ $email }}</strong>. Please check your inbox and follow the link to reset your password.</p>
                    <a href="{{ route('login') }}" class="inline-flex w-full bg-primary text-on-primary border-[3px] border-primary font-label font-bold text-xl uppercase tracking-wider py-5 shadow-brutal hover:shadow-brutal-hover hover:-translate-y-[2px] hover:-translate-x-[2px] transition-all active:translate-y-0 active:translate-x-0 active:shadow-none items-center justify-center gap-2">
                        Back to Login
                    </a>
                </div>
            @else
                <!-- Header -->
                <div class="mb-10">
                    <h2 class="font-headline font-black text-4xl sm:text-5xl text-primary tracking-tight mb-2 uppercase">
                        Forgot Password</h2>
                    <p class="font-body text-lg text-on-surface-variant">Enter your email address and we will send you a link to reset your password.</p>
                </div>

                <!-- Form -->
                <form wire:submit.prevent="sendResetLink" class="space-y-6">
                    {{-- Email Field --}}
                    <div class="relative pb-7">
                        <label class="block font-label font-bold text-base text-primary uppercase tracking-wider mb-2"
                            for="email">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-primary">
                                <span class="material-symbols-outlined text-[24px]" data-icon="mail">mail</span>
                            </span>
                            <input wire:model="email" wire:key="email-input"
                                class="w-full bg-surface-container-lowest border-[2px] @error('email') border-error focus:border-error focus:ring-[2px] focus:ring-inset focus:ring-error @else border-primary focus:border-primary focus:ring-[2px] focus:ring-inset focus:ring-primary @enderror text-primary font-body text-lg px-5 py-5 pl-14 focus:outline-none transition-all"
                                id="email" name="email" placeholder="*****@gmail.com" type="email" />
                        </div>
                        @error('email')
                            <p class="absolute -bottom-1 left-0 flex items-center gap-1 font-body text-[10px] text-error animate-slide-down">
                                <span class="material-symbols-outlined text-[12px] leading-none">cancel</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="pt-4">
                        <button
                            class="w-full bg-primary text-on-primary border-[3px] border-primary font-label font-bold text-xl uppercase tracking-wider py-5 shadow-brutal hover:shadow-brutal-hover hover:-translate-y-[2px] hover:-translate-x-[2px] transition-all active:translate-y-0 active:translate-x-0 active:shadow-none flex items-center justify-center gap-2"
                            type="submit" wire:loading.attr="disabled">
                            <span wire:loading.remove>Send Reset Link</span>
                            <span wire:loading>Sending...</span>
                        </button>
                    </div>

                    <div class="mt-8 pt-8 border-t-[3px] border-surface-dim text-center">
                        <a href="{{ route('login') }}"
                            class="font-label font-bold text-primary underline decoration-[3px] underline-offset-[6px] hover:text-primary-container px-1 transition-colors uppercase">
                            Back to Login
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </main>
</div>
