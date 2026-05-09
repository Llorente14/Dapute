{{--
  ui/badge — chip/tag
  Props: $variant (default|bestseller|new)
--}}
@props(['variant' => 'default'])

<span {{ $attributes->merge(['class' => 'inline-block border px-2 py-0.5 text-xs font-space-grotesk uppercase tracking-widest rounded-none ' . match($variant) {
    'bestseller' => 'bg-[#D4EF70] border-[#012d1d] text-[#012d1d] border',
    'new'        => 'bg-[#012d1d] text-white border-[#012d1d]',
    default      => 'bg-white text-[#012d1d] border-[#012d1d]',
}]) }}>
    {{ $slot }}
</span>
