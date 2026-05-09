<div>
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Epilogue:wght@800;900&amp;family=Manrope:wght@400;500;600&amp;family=Space+Grotesk:wght@500;700&amp;display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    
    <!-- Script Tailwind & Config dari layout referensi -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "surface-variant": "#dde4e0",
                        "secondary": "#57615c",
                        "on-primary-container": "#86af99",
                        "on-error-container": "#93000a",
                        "primary-fixed-dim": "#a5d0b9",
                        "on-surface-variant": "#414844",
                        "on-background": "#161d1b",
                        "on-primary": "#ffffff",
                        "tertiary-container": "#354100",
                        "on-surface": "#161d1b",
                        "error": "#ba1a1a",
                        "error-container": "#ffdad6",
                        "secondary-fixed-dim": "#bfc9c3",
                        "surface-bright": "#f4fbf7",
                        "surface-container": "#e8efec",
                        "primary": "#012d1d",
                        "secondary-container": "#d8e2dc",
                        "inverse-surface": "#2b322f",
                        "tertiary-fixed": "#d3ee6f",
                        "surface-container-high": "#e3eae6",
                        "primary-container": "#1b4332",
                        "on-secondary-fixed": "#151d1a",
                        "on-tertiary-fixed": "#171e00",
                        "on-error": "#ffffff",
                        "on-primary-fixed": "#002114",
                        "outline-variant": "#c1c8c2",
                        "on-tertiary-container": "#98b139",
                        "surface": "#f4fbf7",
                        "tertiary-fixed-dim": "#b8d256",
                        "surface-dim": "#d5dcd8",
                        "on-secondary-container": "#5b6560",
                        "inverse-on-surface": "#ebf2ee",
                        "inverse-primary": "#a5d0b9",
                        "surface-container-highest": "#dde4e0",
                        "surface-container-lowest": "#ffffff",
                        "surface-tint": "#3f6653",
                        "background": "#f4fbf7",
                        "secondary-fixed": "#dbe5df",
                        "outline": "#717973",
                        "tertiary": "#212a00",
                        "primary-fixed": "#c1ecd4",
                        "on-tertiary-fixed-variant": "#3e4c00",
                        "on-secondary-fixed-variant": "#3f4945",
                        "surface-container-low": "#eef5f1",
                        "on-secondary": "#ffffff",
                        "on-primary-fixed-variant": "#274e3d",
                        "on-tertiary": "#ffffff"
                    },
                    "borderRadius": {
                        "DEFAULT": "0px",
                        "lg": "0px",
                        "xl": "0px",
                        "full": "0px"
                    },
                    "fontFamily": {
                        "headline": ["Epilogue", "sans-serif"],
                        "body": ["Manrope", "sans-serif"],
                        "label": ["Space Grotesk", "sans-serif"]
                    }
                },
            },
        }
    </script>
    <style>
        .hard-shadow {
            box-shadow: 4px 4px 0px 0px #012d1d;
        }
        .hard-shadow-hover:hover {
            transform: translate(-2px, -2px);
            box-shadow: 6px 6px 0px 0px #012d1d;
            transition: all 0.2s ease-in-out;
        }
        .input-border {
            border: 3px solid #012d1d;
        }
        .input-focus:focus {
            background-color: #c1ecd4;
            outline: none;
        }
        /* Hide native browser password reveal button (Edge, IE, Chrome) */
        input[type="password"]::-ms-reveal,
        input[type="password"]::-ms-clear {
            display: none;
        }
        input[type="password"]::-webkit-credentials-auto-fill-button {
            display: none;
        }
    </style>

    <main class="flex-grow flex items-center justify-center p-4 sm:p-8 bg-[#f4fbf7] text-on-surface font-body min-h-screen antialiased selection:bg-tertiary-fixed selection:text-on-tertiary-fixed">
        <div class="w-full max-w-5xl grid grid-cols-1 md:grid-cols-2 bg-[#f4fbf7] input-border hard-shadow">
            
            {{-- Left Panel – wire:ignore prevents re-render on every keystroke --}}
            <div wire:ignore class="hidden md:block relative h-full min-h-[600px] border-r-[3px] border-[#012d1d]">
                <img alt="Architectural Bakery Background" class="absolute inset-0 w-full h-full object-cover filter contrast-125 saturate-50 grayscale-[20%]" data-alt="dramatic architectural shot of artisan bread loaves on steel shelves inside a modern brutalist greenhouse bakery setting with high contrast natural lighting" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAb_4O3tFMwJSj6NEZIDzsZpm8CnWGVobChhlxJYRTnV7YhihEXT-1QaDJYUMYLdFV7xTLyURhw4YJWtaeIei9_HjEinBUsfjgzG2Hk40w4NY32r5r1yRFEbfvG4oRrcaSeKFDcB8tbkplNAxgOhLg2g6f6FGjWpt5jxLqu7xSmivzjFMy3YcdfV-dV80KpxDjudlWIHIQT5lgqyjJUFlCvP7PEvVXflhNixXSGj_LG3pXy51qX9NF7NGTRmzlxxBQ6gaOy8XTYdbfe"/>
                <div class="absolute inset-0 bg-gradient-to-t from-[#012d1d] via-[#012d1d]/80 via-0% to-transparent to-50%"></div>
                
                <div class="absolute bottom-10 left-8 pr-8 z-10">
                    <h2 class="font-headline font-black text-[28px] text-white tracking-tight leading-[1.1] mb-2 uppercase">
                        ARCHITECTURAL<br/>COOKIES
                    </h2>
                    <p class="font-headline font-bold text-[#d5dcd8] text-[9px] tracking-[0.2em] uppercase mt-4">
                        DESIGNED FOR THE PERFECT BITE.
                    </p>
                </div>
            </div>

            {{-- Right Panel --}}
            <div class="p-8 sm:p-14 flex flex-col justify-center bg-[#f4fbf7]">
                <div class="mb-8">
                    <a class="inline-block font-headline font-black text-lg text-primary tracking-tight uppercase mb-6" href="/">
                        Dapute
                    </a>
                    <h1 class="font-headline font-black text-3xl sm:text-[2rem] text-primary tracking-tight mb-2 uppercase">
                        Create Account
                    </h1>
                    <p class="font-body text-[#5b6560] text-sm leading-relaxed max-w-sm">
                        Create an account to join the Architectural Cookie Collection. Start by crafting your own profile.
                    </p>
                </div>

                {{-- Error banner dari Action (misal: email duplikat, Supabase error) --}}
                @if($actionError)
                    <div class="mb-4 p-3 bg-[#ffdad6] border border-[#ba1a1a] text-[#93000a] font-body text-sm leading-snug" role="alert">
                        {{ $actionError }}
                    </div>
                @endif
                
                <form wire:submit.prevent="register" class="space-y-4">
                    {{-- Full Name --}}
                    <div class="relative pb-5">
                        <label class="block font-headline text-[10px] font-bold text-primary uppercase tracking-widest mb-1.5" for="name">
                            Full Name
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span aria-hidden="true" class="material-symbols-outlined text-primary text-[20px] leading-none">
                                    person
                                </span>
                            </div>
                            <input
                                wire:model.live="full_name"
                                class="w-full bg-white border border-[#c1c8c2] text-primary font-body text-sm py-3 pl-10 pr-4 placeholder:text-[#717973] focus:border-primary focus:ring-1 focus:ring-primary transition-colors {{ $errors->has('full_name') ? 'border-[#ba1a1a] focus:border-[#ba1a1a] focus:ring-[#ba1a1a]' : '' }}"
                                id="name"
                                name="full_name"
                                placeholder="Master Baker"
                                type="text"
                            />
                        </div>
                        {{-- Inline error: Full Name --}}
                        @if($full_name !== '' && mb_strlen(trim($full_name)) < 2)
                            <p class="absolute bottom-0 left-0 flex items-center gap-1 mt-1 font-body text-[10px] text-[#ba1a1a]">
                                <span class="material-symbols-outlined text-[12px] leading-none">cancel</span>
                                Minimum 2 characters
                            </p>
                        @elseif($full_name === '')
                            @error('full_name')
                                <p class="absolute bottom-0 left-0 flex items-center gap-1 mt-1 font-body text-[10px] text-[#ba1a1a]">
                                    <span class="material-symbols-outlined text-[12px] leading-none">cancel</span>
                                    Minimum 2 characters
                                </p>
                            @enderror
                        @endif
                    </div>

                    {{-- Email --}}
                    <div class="relative pb-5">
                        <label class="block font-headline text-[10px] font-bold text-primary uppercase tracking-widest mb-1.5" for="email">
                            Email Address
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span aria-hidden="true" class="material-symbols-outlined text-primary text-[18px] leading-none">
                                    mail
                                </span>
                            </div>
                            <input
                                wire:model.live="email"
                                class="w-full bg-white border border-[#c1c8c2] text-primary font-body text-sm py-3 pl-10 pr-4 placeholder:text-[#717973] focus:border-primary focus:ring-1 focus:ring-primary transition-colors {{ $errors->has('email') ? 'border-[#ba1a1a] focus:border-[#ba1a1a] focus:ring-[#ba1a1a]' : '' }}"
                                id="email"
                                name="email"
                                placeholder="baker@greenhouse.com"
                                type="email"
                            />
                        </div>
                        {{-- Inline error: Email --}}
                        @if($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL))
                            <p class="absolute -bottom-5 left-0 flex items-center gap-1 mt-1 font-body text-[10px] text-[#ba1a1a]">
                                <span class="material-symbols-outlined text-[12px] leading-none">cancel</span>
                                Invalid email format
                            </p>
                        @elseif($email === '')
                            @error('email')
                                <p class="absolute -bottom-5 left-0 flex items-center gap-1 mt-1 font-body text-[10px] text-[#ba1a1a]">
                                    <span class="material-symbols-outlined text-[12px] leading-none">cancel</span>
                                    Invalid email format
                                </p>
                            @enderror
                        @endif
                    </div>

                    {{-- Phone Number --}}
                    <div class="relative pb-5">
                        <label class="block font-headline text-[10px] font-bold text-primary uppercase tracking-widest mb-1.5" for="phone">
                            Phone Number <span class="text-[#717973] font-normal lowercase tracking-normal">(optional)</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span aria-hidden="true" class="material-symbols-outlined text-primary text-[18px] leading-none">
                                    call
                                </span>
                            </div>
                            <input
                                wire:model.live="phone_number"
                                class="w-full bg-white border border-[#c1c8c2] text-primary font-body text-sm py-3 pl-10 pr-4 placeholder:text-[#717973] focus:border-primary focus:ring-1 focus:ring-primary transition-colors"
                                id="phone"
                                name="phone_number"
                                placeholder="08xxxxxxxxxx"
                                type="tel"
                            />
                        </div>
                        {{-- Inline error: Phone Number --}}
                        @error('phone_number')
                            <p class="absolute bottom-0 left-0 flex items-center gap-1 mt-1 font-body text-[10px] text-[#ba1a1a]">
                                <span class="material-symbols-outlined text-[12px] leading-none">cancel</span>
                                Invalid phone number
                            </p>
                        @enderror
                    </div>

                    {{-- Password --}}
                    <div class="relative pb-5">
                        <label class="block font-headline text-[10px] font-bold text-primary uppercase tracking-widest mb-1.5" for="password">
                            Password
                        </label>
                        <div class="relative group">
                            <input
                                wire:model.live="password"
                                class="w-full bg-white border border-[#c1c8c2] text-primary font-body text-sm py-3 pl-4 pr-10 placeholder:text-[#717973] focus:border-primary focus:ring-1 focus:ring-primary transition-colors {{ $errors->has('password') ? 'border-[#ba1a1a] focus:border-[#ba1a1a] focus:ring-[#ba1a1a]' : '' }}"
                                id="password"
                                name="password"
                                placeholder="••••••••"
                                type="password"
                            />
                            {{-- Eye toggle: only visible on hover --}}
                            <button
                                type="button"
                                tabindex="-1"
                                aria-label="Toggle password visibility"
                                onclick="togglePassword('password', 'eye-pw')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity duration-150 cursor-pointer"
                            >
                                <span id="eye-pw" class="material-symbols-outlined text-[#717973] hover:text-primary text-[18px] leading-none select-none">visibility_off</span>
                            </button>
                        </div>
                        {{-- Inline error: Password (3 levels) --}}
                        @if($password !== '')
                            @php
                                $pwShort  = mb_strlen($password) < 8;
                                $pwWeak   = !$pwShort && !preg_match('/^(?=.*[a-zA-Z])(?=.*\d)(?=.*[@!,\.\?\/]).+$/', $password);
                            @endphp
                            @if($pwShort)
                                <p class="absolute bottom-0 left-0 flex items-center gap-1 mt-1 font-body text-[10px] text-[#ba1a1a]">
                                    <span class="material-symbols-outlined text-[12px] leading-none">cancel</span>
                                    Minimum 8 characters
                                </p>
                            @elseif($pwWeak)
                                <p class="absolute -bottom-3 left-0 flex items-center gap-1 mt-1 font-body text-[10px] text-[#ba1a1a]">
                                    <span class="material-symbols-outlined text-[12px] leading-none">cancel</span>
                                    Password is too weak. Use letters, numbers, and symbols (@!,.?/).
                                </p>
                            @endif
                        @else
                            @error('password')
                                <p class="absolute bottom-0 left-0 flex items-center gap-1 mt-1 font-body text-[10px] text-[#ba1a1a]">
                                    <span class="material-symbols-outlined text-[12px] leading-none">cancel</span>
                                    {{ $message }}
                                </p>
                            @enderror
                        @endif
                    </div>

                    {{-- Confirm Password --}}
                    <div class="relative pb-5">
                        <label class="block font-headline text-[10px] font-bold text-primary uppercase tracking-widest mb-1.5" for="password_confirmation">
                            Confirm Password
                        </label>
                        <div class="relative group">
                            <input
                                wire:model.live="password_confirmation"
                                class="w-full bg-white border border-[#c1c8c2] text-primary font-body text-sm py-3 pl-4 pr-10 placeholder:text-[#717973] focus:border-primary focus:ring-1 focus:ring-primary transition-colors {{ $errors->has('password_confirmation') ? 'border-[#ba1a1a] focus:border-[#ba1a1a] focus:ring-[#ba1a1a]' : '' }}"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="••••••••"
                                type="password"
                            />
                            {{-- Eye toggle: only visible on hover --}}
                            <button
                                type="button"
                                tabindex="-1"
                                aria-label="Toggle confirm password visibility"
                                onclick="togglePassword('password_confirmation', 'eye-pc')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center opacity-0 group-hover:opacity-100 focus:opacity-100 transition-opacity duration-150 cursor-pointer"
                            >
                                <span id="eye-pc" class="material-symbols-outlined text-[#717973] hover:text-primary text-[18px] leading-none select-none">visibility_off</span>
                            </button>
                        </div>
                        {{-- Inline error: Confirm Password --}}
                        @if($password_confirmation !== '' && $password_confirmation !== $password)
                            <p class="absolute bottom-0 left-0 flex items-center gap-1 mt-1 font-body text-[10px] text-[#ba1a1a]">
                                <span class="material-symbols-outlined text-[12px] leading-none">cancel</span>
                                Passwords do not match
                            </p>
                        @elseif($password_confirmation === '')
                            @error('password_confirmation')
                                <p class="absolute bottom-0 left-0 flex items-center gap-1 mt-1 font-body text-[10px] text-[#ba1a1a]">
                                    <span class="material-symbols-outlined text-[12px] leading-none">cancel</span>
                                    Passwords do not match
                                </p>
                            @enderror
                        @endif
                    </div>

                    {{-- Submit button: loading only scoped to 'register' action, not keystrokes --}}
                    <button
                        wire:loading.attr="disabled" wire:target="register"
                        wire:loading.class="animate-pulse opacity-75 cursor-not-allowed" wire:target="register"
                        class="w-full bg-primary text-white font-headline font-bold text-[13px] py-4 uppercase tracking-[0.15em] mt-2 cursor-pointer input-border hard-shadow hover:bg-[#1b4332] transition-colors"
                        type="submit"
                    >
                        SIGN UP
                    </button>
                </form>
                
                <div class="mt-8 text-center">
                    <p class="font-body text-[#5b6560] text-xs">
                        Already have an account? 
                        <a class="font-headline font-bold text-primary uppercase tracking-widest underline decoration-2 underline-offset-4 transition-colors ml-1 cursor-pointer hover:text-opacity-80" href="/login">
                            SIGN IN
                        </a>
                    </p>
                </div>
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
