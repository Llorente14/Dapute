<div>
    <style>
        /* Hide native browser password reveal button (Edge, IE, Chrome) */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }

        input[type="password"]::-webkit-credentials-auto-fill-button {
            display: none;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-slide-down {
            animation: slideDown 0.3s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>

    <main
        class="w-full max-w-[1000px] grid md:grid-cols-2 gap-8 md:gap-0 bg-surface-container-lowest border-[3px] border-primary shadow-brutal m-auto">
        <!-- Left Side: Image / Brand Anchor -->
        <div
            class="hidden md:block w-full h-full min-h-[600px] border-r-[3px] border-primary relative overflow-hidden bg-surface-container-low">
            <img alt="" class="w-full h-full object-cover grayscale opacity-80 mix-blend-multiply"
                data-alt="dramatic architectural shot of artisan sourdough bread loaves stacked on metal shelving in a greenhouse setting with natural light and deep shadows"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDHuuuy-Pt9CE6I4b6Tb0ybNcf359KPkxGBqO8TBMq_n8iGT3hKSUL1xmTtfXY0J_K18ceVmNMEapcnctBjK18Rsoy83D1TNgSNBKh-VyNMfYkaLoB97-0YGl-S3GT1gckL2iJRKocR4oSEha0OxQrwG2MBnYXzUw3Rd4ySMeJRyQxFPllYDozkhQ0OijTHskpAzNryk6xCAvgW0NqU-i9jORla2o9SB4o-MueGoxd6uGzNvXBBiMUBu8kcb_dwYkJFs1kBvh-LDW89" />
            <div
                class="absolute inset-0 bg-gradient-to-t from-primary/90 to-primary-container/40 p-12 flex flex-col justify-end">
                <h2
                    class="font-headline font-black text-5xl text-surface-container-lowest tracking-tighter leading-tight mb-4">
                    Dapute</h2>
                <p class="font-body text-surface-container-highest text-lg max-w-sm">Architectural baking. Raw
                    ingredients. Structural honesty.</p>
            </div>
        </div>
        <!-- Right Side: Forms -->
        <div class="w-full p-8 sm:p-12 lg:p-16 flex flex-col justify-center relative bg-surface-container-lowest">
            <!-- Mobile Brand Header -->
            <div class="md:hidden mb-4">
                <h1 class="font-headline font-black text-4xl text-primary tracking-tighter">Dapute</h1>
            </div>
            <!-- Header -->
            <div class="mb-10">
                <h2 class="font-headline font-black text-3xl sm:text-4xl text-primary tracking-tight mb-2 uppercase">
                    Access</h2>
                <p class="font-body text-on-surface-variant">Enter your credentials to continue.</p>
            </div>
            <!-- Login Form (Active) -->
            <form wire:submit.prevent="login" class="space-y-6">
                {{-- Email Field --}}
                <div class="relative pb-7">
                    <label class="block font-label font-bold text-sm text-primary uppercase tracking-wider mb-2"
                        for="email">Email Address</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-primary">
                            <span class="material-symbols-outlined" data-icon="mail">mail</span>
                        </span>
                        <input wire:model="email" wire:key="email-input"
                            class="w-full bg-surface-container-lowest border-[2px] @error('email') border-error focus:border-error focus:ring-[2px] focus:ring-inset focus:ring-error @else border-primary focus:border-primary focus:ring-[2px] focus:ring-inset focus:ring-primary @enderror text-primary font-body px-4 py-4 pl-12 focus:outline-none transition-all"
                            id="email" name="email" placeholder="*****@gmail.com" type="email" />
                    </div>
                    @error('email')
                        <p
                            class="absolute -bottom-1 left-0 flex items-center gap-1 font-body text-[10px] text-error animate-slide-down">
                            <span class="material-symbols-outlined text-[12px] leading-none">cancel</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password Field --}}
                <div class="relative pb-7">
                    <div class="flex justify-between items-baseline mb-2">
                        <label class="block font-label font-bold text-sm text-primary uppercase tracking-wider"
                            for="password">Password</label>
                        <a class="font-label font-bold text-xs text-primary underline decoration-2 underline-offset-4 hover:text-primary-container"
                            href="#">Forgot Password?</a>
                    </div>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-primary">
                            <span class="material-symbols-outlined" data-icon="lock">lock</span>
                        </span>
                        <input wire:model="password" wire:key="password-input"
                            class="w-full bg-surface-container-lowest border-[2px] @error('password') border-error focus:border-error focus:ring-[2px] focus:ring-inset focus:ring-error @else border-primary focus:border-primary focus:ring-[2px] focus:ring-inset focus:ring-primary @enderror text-primary font-body px-4 py-4 pl-12 focus:outline-none transition-all"
                            id="password" name="password" placeholder="••••••••" type="password" />
                        {{-- Eye toggle: only visible on hover --}}
                        <button type="button" tabindex="-1" aria-label="Toggle password visibility"
                            onclick="togglePassword('password', 'eye-pw')"
                            class="absolute inset-y-0 right-0 pr-4 flex items-center opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity duration-150 cursor-pointer">
                            <span id="eye-pw"
                                class="material-symbols-outlined text-outline hover:text-primary text-[20px] leading-none select-none">visibility_off</span>
                        </button>
                    </div>
                    @error('password')
                        <p
                            class="absolute -bottom-1 left-0 flex items-center gap-1 font-body text-[10px] text-error animate-slide-down">
                            <span class="material-symbols-outlined text-[12px] leading-none">cancel</span>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="pt-4">
                    <button
                        class="w-full bg-primary text-on-primary border-[3px] border-primary font-label font-bold text-lg uppercase tracking-wider py-4 shadow-brutal hover:shadow-brutal-hover hover:-translate-y-[2px] hover:-translate-x-[2px] transition-all active:translate-y-0 active:translate-x-0 active:shadow-none flex items-center justify-center gap-2"
                        type="submit">
                        <span>Sign In</span>
                    </button>
                </div>
                <div class="mt-8 pt-8 border-t-[3px] border-surface-dim text-center">
                    <p class="font-body text-on-surface-variant font-semibold">
                        Don't have an account?
                        <a href="/register"
                            class="font-label font-bold text-primary underline decoration-[3px] underline-offset-[6px] hover:text-primary-container px-1 transition-colors ml-2 uppercase">Sign
                            Up</a>
                    </p>
                </div>
            </form>
        </div>
    </main>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (!input || !icon) return;

            const isHidden = input.type === 'password';
            input.type = isHidden ? 'text' : 'password';
            icon.textContent = isHidden ? 'visibility' : 'visibility_off';
        }
    </script>
