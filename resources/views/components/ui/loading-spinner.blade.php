{{--
  ui/loading-spinner — wire:loading target
--}}
@props(['size' => 'md'])

<span {{ $attributes->merge(['class' => 'inline-block animate-spin border-[3px] border-[#012d1d] border-t-[#D4EF70] rounded-full ' . match($size) {
    'sm' => 'w-4 h-4',
    'lg' => 'w-10 h-10',
    default => 'w-6 h-6',
}]) }}></span>
