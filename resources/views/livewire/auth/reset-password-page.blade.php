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

    <main class="w-full max-w-[1200px] grid md:grid-cols-2 gap-8 md:gap-0 bg-surface-container-lowest border-[3px] border-primary shadow-brutal m-auto" x-data="{
        token: null,
        email: null,
        hashError: null,
        init() {
            const hash = window.location.hash.substring(1);
            const params = new URLSearchParams(hash);
            
            if (params.has('error')) {
                const desc = params.get('error_description') || 'Unknown error occurred.';
                this.hashError = desc.replace(/\+/g, ' ');
            } else if (params.has('access_token') && params.get('type') === 'recovery') {
                this.token = params.get('access_token');
                try {
                    const base64Url = this.token.split('.')[1];
                    const base64 = base64Url.replace(/-/g, '+').replace(/_/g, '/');
                    const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                        return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
                    }).join(''));
                    const payload = JSON.parse(jsonPayload);
                    this.email = payload.email;
                } catch (e) {
                    console.error('Invalid token payload');
                }
            } else {
                this.hashError = 'Invalid or missing recovery token. Please use the link from your email.';
            }
        },
        submit() {
            if (!this.token) {
                this.hashError = 'Missing recovery token.';
                return;
            }
            this.$wire.updatePassword(this.token);
        }
    }">
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
                        <span class="material-symbols-outlined text-[40px]">check_circle</span>
                    </div>
                    <h2 class="font-headline font-black text-4xl sm:text-5xl text-primary tracking-tight mb-4 uppercase">Password Reset</h2>
                    <p class="font-body text-lg text-on-surface-variant mb-8">Your password has been successfully updated. You can now log in with your new credentials.</p>
                    <a href="{{ route('login') }}" class="inline-flex w-full bg-primary text-on-primary border-[3px] border-primary font-label font-bold text-xl uppercase tracking-wider py-5 shadow-brutal hover:shadow-brutal-hover hover:-translate-y-[2px] hover:-translate-x-[2px] transition-all active:translate-y-0 active:translate-x-0 active:shadow-none items-center justify-center gap-2">
                        Back to Login
                    </a>
                </div>
            @else
                <!-- Header -->
                <div class="mb-10">
                    <h2 class="font-headline font-black text-4xl sm:text-5xl text-primary tracking-tight mb-2 uppercase">
                        Reset Password</h2>
                    <p class="font-body text-lg text-on-surface-variant">
                        <template x-if="email">
                            <span>Updating password for <strong x-text="email" class="text-primary break-all"></strong>.</span>
                        </template>
                        <template x-if="!email">
                            <span>Enter your new password below.</span>
                        </template>
                    </p>
                </div>

                <!-- Hash Error -->
                <template x-if="hashError">
                    <div class="bg-error/10 border-[3px] border-error text-error p-4 mb-6 shadow-brutal font-body animate-slide-down">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <span class="material-symbols-outlined text-[20px]">error</span>
                            Error
                        </div>
                        <p x-text="hashError"></p>
                    </div>
                </template>

                <!-- Reset Form (Active) -->
                <form @submit.prevent="submit" class="space-y-6" x-show="!hashError">
                    {{-- Password Field --}}
                    <div class="relative pb-7">
                        <label class="block font-label font-bold text-base text-primary uppercase tracking-wider mb-2"
                            for="password">New Password</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-primary">
                                <span class="material-symbols-outlined text-[24px]" data-icon="lock">lock</span>
                            </span>
                            <input wire:model="password" wire:key="password-input"
                                class="w-full bg-surface-container-lowest border-[2px] @error('password') border-error focus:border-error focus:ring-[2px] focus:ring-inset focus:ring-error @else border-primary focus:border-primary focus:ring-[2px] focus:ring-inset focus:ring-primary @enderror text-primary font-body text-lg px-5 py-5 pl-14 focus:outline-none transition-all"
                                id="password" name="password" placeholder="••••••••" type="password" />
                            {{-- Eye toggle --}}
                            <button type="button" tabindex="-1" aria-label="Toggle password visibility"
                                onclick="togglePassword('password', 'eye-pw1')"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity duration-150 cursor-pointer">
                                <span id="eye-pw1"
                                    class="material-symbols-outlined text-outline hover:text-primary text-[24px] leading-none select-none">visibility_off</span>
                            </button>
                        </div>
                        @error('password')
                            <p class="absolute -bottom-1 left-0 flex items-center gap-1 font-body text-[10px] text-error animate-slide-down">
                                <span class="material-symbols-outlined text-[12px] leading-none">cancel</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password Confirmation Field --}}
                    <div class="relative pb-7">
                        <label class="block font-label font-bold text-base text-primary uppercase tracking-wider mb-2"
                            for="passwordConfirmation">Confirm Password</label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-primary">
                                <span class="material-symbols-outlined text-[24px]" data-icon="lock">lock</span>
                            </span>
                            <input wire:model="passwordConfirmation" wire:key="password-confirm-input"
                                class="w-full bg-surface-container-lowest border-[2px] border-primary focus:border-primary focus:ring-[2px] focus:ring-inset focus:ring-primary text-primary font-body text-lg px-5 py-5 pl-14 focus:outline-none transition-all"
                                id="passwordConfirmation" name="passwordConfirmation" placeholder="••••••••" type="password" />
                            {{-- Eye toggle --}}
                            <button type="button" tabindex="-1" aria-label="Toggle password visibility"
                                onclick="togglePassword('passwordConfirmation', 'eye-pw2')"
                                class="absolute inset-y-0 right-0 pr-4 flex items-center opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity duration-150 cursor-pointer">
                                <span id="eye-pw2"
                                    class="material-symbols-outlined text-outline hover:text-primary text-[24px] leading-none select-none">visibility_off</span>
                            </button>
                        </div>
                    </div>

                    <div class="pt-4">
                        <button
                            class="w-full bg-primary text-on-primary border-[3px] border-primary font-label font-bold text-xl uppercase tracking-wider py-5 shadow-brutal hover:shadow-brutal-hover hover:-translate-y-[2px] hover:-translate-x-[2px] transition-all active:translate-y-0 active:translate-x-0 active:shadow-none flex items-center justify-center gap-2"
                            type="submit">
                            <span wire:loading.remove>Update Password</span>
                            <span wire:loading>Updating...</span>
                        </button>
                    </div>
                </form>
            @endif
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
</div>
