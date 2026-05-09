{{--
  ui/button — Forest Brutalist
  Props: $variant (primary|secondary|ghost), $type (button|submit), $class
  Usage: <x-ui.button variant="primary" type="submit">Label</x-ui.button>
--}}
@props(['variant' => 'primary', 'type' => 'button'])

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => 'inline-block px-6 py-3 border-[3px] border-[#012d1d] font-space-grotesk uppercase tracking-widest text-sm font-bold shadow-[4px_4px_0_0_#012d1d] hover:shadow-[6px_6px_0_0_#012d1d] transition-shadow rounded-none ' . match($variant) {
        'secondary' => 'bg-[#D4EF70] text-[#012d1d]',
        'ghost'     => 'bg-transparent text-[#012d1d]',
        default     => 'bg-[#012d1d] text-white',
    }]) }}
>
    {{ $slot }}
</button>
