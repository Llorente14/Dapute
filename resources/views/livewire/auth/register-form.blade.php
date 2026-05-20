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
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .animate-slide-down {
            animation: slideDown 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
    </style>

    {{-- Mobile: full-screen bg + centered card --}}
    <main class="min-h-screen flex items-center justify-center bg-surface-container-low p-4 sm:p-6 md:bg-[#f4fbf7] md:py-10">

        <div class="w-full max-w-[1200px] grid md:grid-cols-2 md:gap-0 bg-surface-container-lowest border-[3px] border-primary shadow-brutal md:m-auto">

            {{-- Left Panel (desktop only) --}}
            <div wire:ignore class="hidden md:block w-full h-full min-h-[600px] border-r-[3px] border-primary relative overflow-hidden bg-surface-container-low">
                <img
                    alt="Architectural Bakery Background"
                    class="w-full h-full object-cover grayscale opacity-80 mix-blend-multiply"
                    src="{{ asset('images/register-bg.webp') }}"
                />
                <div class="absolute inset-0 bg-gradient-to-t from-primary/90 to-primary-container/40 p-12 flex flex-col justify-end">
                    <h2 class="font-headline font-black text-7xl text-surface-container-lowest tracking-tighter leading-tight mb-4">
                        Dapute
                    </h2>
                    <p class="font-body text-surface-container-highest text-xl max-w-md">
                        Architectural baking. Raw ingredients. Structural honesty.
                    </p>
                </div>
            </div>

            {{-- Right Panel --}}
            <div class="w-full p-6 sm:p-8 lg:p-14 flex flex-col justify-center relative bg-surface-container-lowest">

                {{-- Mobile Brand Header --}}
                <div class="md:hidden mb-3">
                    <h1 class="font-headline font-black text-4xl text-primary tracking-tighter">Dapute</h1>
                </div>

                {{-- Header --}}
                <div class="mb-5">
                    <h2 class="font-headline font-black text-3xl sm:text-4xl text-primary tracking-tight mb-1 uppercase">
                        Create Account
                    </h2>
                    <p class="font-body text-sm text-on-surface-variant">
                        Join the Architectural Cookie Collection.
                    </p>
                </div>

                {{-- Action Error Banner --}}
                @if($actionError)
                    <div class="mb-4 p-3 bg-error-container border-[2px] border-error text-on-error-container font-body text-xs leading-snug flex items-start gap-2" role="alert">
                        <span class="material-symbols-outlined text-[16px] leading-none shrink-0 mt-0.5">error</span>
                        {{ $actionError }}
                    </div>
                @endif

                <form wire:submit.prevent="register" class="space-y-3">

                    {{-- Full Name --}}
                    <div class="relative pb-5">
                        <label class="block font-label font-bold text-xs text-primary uppercase tracking-wider mb-1.5" for="name">
                            Full Name
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-primary">
                                <span class="material-symbols-outlined text-[20px]">person</span>
                            </span>
                            <input
                                wire:model.live.debounce.2s="full_name"
                                class="w-full bg-surface-container-lowest border-[2px] @error('full_name') border-error focus:border-error focus:ring-[2px] focus:ring-inset focus:ring-error @else border-primary focus:border-primary focus:ring-[2px] focus:ring-inset focus:ring-primary @enderror text-primary font-body text-sm px-4 py-3 pl-10 focus:outline-none transition-all"
                                id="name"
                                name="full_name"
                                placeholder="Master Baker"
                                type="text"
                            />
                        </div>
                        @if($full_name !== '' && mb_strlen(trim($full_name)) < 2)
                            <p class="absolute -bottom-0 left-0 flex items-center gap-1 font-body text-[10px] text-error animate-slide-down">
                                <span class="material-symbols-outlined text-[11px] leading-none">cancel</span>
                                Minimum 2 characters
                            </p>
                        @else
                            @error('full_name')
                                <p class="absolute -bottom-0 left-0 flex items-center gap-1 font-body text-[10px] text-error animate-slide-down">
                                    <span class="material-symbols-outlined text-[11px] leading-none">cancel</span>
                                    {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>

                    {{-- Email Address --}}
                    <div class="relative pb-5">
                        <label class="block font-label font-bold text-xs text-primary uppercase tracking-wider mb-1.5" for="email">
                            Email Address
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-primary">
                                <span class="material-symbols-outlined text-[20px]">mail</span>
                            </span>
                            <input
                                wire:model.live.debounce.2s="email"
                                class="w-full bg-surface-container-lowest border-[2px] @error('email') border-error focus:border-error focus:ring-[2px] focus:ring-inset focus:ring-error @else border-primary focus:border-primary focus:ring-[2px] focus:ring-inset focus:ring-primary @enderror text-primary font-body text-sm px-4 py-3 pl-10 focus:outline-none transition-all"
                                id="email"
                                name="email"
                                placeholder="baker@greenhouse.com"
                                type="email"
                            />
                        </div>
                        @if($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
                            <p class="absolute -bottom-0 left-0 flex items-center gap-1 font-body text-[10px] text-error animate-slide-down">
                                <span class="material-symbols-outlined text-[11px] leading-none">cancel</span>
                                Invalid email format
                            </p>
                        @else
                            @error('email')
                                <p class="absolute -bottom-0 left-0 flex items-center gap-1 font-body text-[10px] text-error animate-slide-down">
                                    <span class="material-symbols-outlined text-[11px] leading-none">cancel</span>
                                    {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>

                    {{-- Phone Number --}}
                    <div class="relative pb-5">
                        <label class="block font-label font-bold text-xs text-primary uppercase tracking-wider mb-1.5" for="phone">
                            Phone Number
                            <span class="text-on-surface-variant font-normal lowercase tracking-normal normal-case">(optional)</span>
                        </label>
                        <div class="relative flex items-center border-[2px] @error('phone_number') border-error focus-within:border-error focus-within:ring-[2px] focus-within:ring-inset focus-within:ring-error @else border-primary focus-within:border-primary focus-within:ring-[2px] focus-within:ring-inset focus-within:ring-primary @enderror bg-surface-container-lowest transition-all">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-primary">
                                <span class="material-symbols-outlined text-[20px]">call</span>
                            </span>
                            <span class="pl-10 pr-2 font-label text-sm font-black text-primary">+62</span>
                            <input
                                wire:model.live.debounce.2s="phone_number"
                                class="w-full bg-transparent border-0 py-3 pr-4 pl-0 text-primary font-body text-sm focus:outline-none focus:ring-0"
                                id="phone"
                                name="phone_number"
                                placeholder="81234567890"
                                type="tel"
                                inputmode="numeric"
                                maxlength="11"
                                pattern="[0-9]{8,11}"
                            />
                        </div>
                        @error('phone_number')
                            <p class="absolute -bottom-0 left-0 flex items-center gap-1 font-body text-[10px] text-error animate-slide-down">
                                <span class="material-symbols-outlined text-[11px] leading-none">cancel</span>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="relative pb-5">
                        <label class="block font-label font-bold text-xs text-primary uppercase tracking-wider mb-1.5" for="password">
                            Password
                        </label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-primary">
                                <span class="material-symbols-outlined text-[20px]">lock</span>
                            </span>
                            <input
                                wire:model.live.debounce.2s="password"
                                class="w-full bg-surface-container-lowest border-[2px] @error('password') border-error focus:border-error focus:ring-[2px] focus:ring-inset focus:ring-error @else border-primary focus:border-primary focus:ring-[2px] focus:ring-inset focus:ring-primary @enderror text-primary font-body text-sm px-4 py-3 pl-10 focus:outline-none transition-all"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                type="password"
                            />
                            {{-- Eye toggle --}}
                            <button type="button" tabindex="-1" aria-label="Toggle password visibility"
                                onclick="togglePassword('password', 'eye-pw')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity duration-150 cursor-pointer">
                                <span id="eye-pw" class="material-symbols-outlined text-outline hover:text-primary text-[20px] leading-none select-none">visibility_off</span>
                            </button>
                        </div>
                        @if($password !== '')
                            @php
                                $pwShort = mb_strlen($password) < 8;
                                $pwWeak  = !$pwShort && !preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@!,\.\?\/]).+$/', $password);
                            @endphp
                            @if($pwShort)
                                <p class="absolute -bottom-0 left-0 flex items-center gap-1 font-body text-[10px] text-error animate-slide-down">
                                    <span class="material-symbols-outlined text-[11px] leading-none">cancel</span>
                                    Minimum 8 characters
                                </p>
                            @elseif($pwWeak)
                                <p class="absolute -bottom-0 left-0 flex items-center gap-1 font-body text-[10px] text-error animate-slide-down">
                                    <span class="material-symbols-outlined text-[11px] leading-none">cancel</span>
                                    Use letters, numbers &amp; symbols (@!,.?/).
                                </p>
                            @endif
                        @else
                            @error('password')
                                <p class="absolute -bottom-0 left-0 flex items-center gap-1 font-body text-[10px] text-error animate-slide-down">
                                    <span class="material-symbols-outlined text-[11px] leading-none">cancel</span>
                                    {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>

                    {{-- Confirm Password --}}
                    <div class="relative pb-5">
                        <label class="block font-label font-bold text-xs text-primary uppercase tracking-wider mb-1.5" for="password_confirmation">
                            Confirm Password
                        </label>
                        <div class="relative group">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-primary">
                                <span class="material-symbols-outlined text-[20px]">lock_reset</span>
                            </span>
                            <input
                                wire:model.live.debounce.2s="password_confirmation"
                                class="w-full bg-surface-container-lowest border-[2px] @error('password_confirmation') border-error focus:border-error focus:ring-[2px] focus:ring-inset focus:ring-error @else border-primary focus:border-primary focus:ring-[2px] focus:ring-inset focus:ring-primary @enderror text-primary font-body text-sm px-4 py-3 pl-10 focus:outline-none transition-all"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="••••••••"
                                type="password"
                            />
                            {{-- Eye toggle --}}
                            <button type="button" tabindex="-1" aria-label="Toggle confirm password visibility"
                                onclick="togglePassword('password_confirmation', 'eye-pc')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity duration-150 cursor-pointer">
                                <span id="eye-pc" class="material-symbols-outlined text-outline hover:text-primary text-[20px] leading-none select-none">visibility_off</span>
                            </button>
                        </div>
                        @if($password_confirmation !== '' && $password_confirmation !== $password)
                            <p class="absolute -bottom-0 left-0 flex items-center gap-1 font-body text-[10px] text-error animate-slide-down">
                                <span class="material-symbols-outlined text-[11px] leading-none">cancel</span>
                                Passwords do not match
                            </p>
                        @else
                            @error('password_confirmation')
                                <p class="absolute -bottom-0 left-0 flex items-center gap-1 font-body text-[10px] text-error animate-slide-down">
                                    <span class="material-symbols-outlined text-[11px] leading-none">cancel</span>
                                    Passwords do not match
                                </p>
                            @enderror
                        @endif
                    </div>

                    {{-- Submit --}}
                    <div class="pt-2">
                        <button
                            wire:loading.attr="disabled"
                            wire:loading.class="opacity-70 cursor-not-allowed shadow-none translate-x-0 translate-y-0"
                            wire:target="register"
                            class="w-full bg-primary text-on-primary border-[3px] border-primary font-label font-bold text-base uppercase tracking-wider py-4 shadow-brutal hover:shadow-brutal-hover hover:-translate-y-[2px] hover:-translate-x-[2px] transition-all active:translate-y-0 active:translate-x-0 active:shadow-none flex items-center justify-center gap-2 disabled:pointer-events-none"
                            type="submit"
                        >
                            <span wire:loading.remove wire:target="register">Sign Up</span>
                            <span wire:loading.inline-flex wire:target="register" class="hidden items-center gap-2">
                                <span class="material-symbols-outlined text-[20px] animate-spin">sync</span>
                                <span>Creating</span>
                            </span>
                        </button>
                    </div>

                    <div class="mt-4 pt-4 border-t-[3px] border-surface-dim text-center">
                        <p class="font-body text-sm text-on-surface-variant font-semibold">
                            Already have an account?
                            <a href="/login"
                                class="font-label font-bold text-primary underline decoration-[3px] underline-offset-[4px] hover:text-primary-container px-1 transition-colors ml-1 uppercase">Sign In</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            if (!input || !icon) return;

            const isHidden = input.type === 'password';
            input.type     = isHidden ? 'text' : 'password';
            icon.textContent = isHidden ? 'visibility' : 'visibility_off';
        }
    </script>
</div>
