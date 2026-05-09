{{--
  ui/card — Forest Brutalist product card shell
  Props: $class (extra classes)
--}}
@props([])

<div {{ $attributes->merge(['class' => 'bg-white border-[3px] border-[#012d1d] shadow-[4px_4px_0_0_#012d1d] hover:shadow-[6px_6px_0_0_#012d1d] transition-shadow rounded-none']) }}>
    {{ $slot }}
</div>
