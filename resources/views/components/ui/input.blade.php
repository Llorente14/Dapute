{{--
  ui/input
  Props: $label, $name, $type, $placeholder, $error
--}}
@props(['label' => '', 'name' => '', 'type' => 'text', 'placeholder' => '', 'error' => null])

<div class="flex flex-col gap-1">
    @if($label)
        <label for="{{ $name }}" class="font-space-grotesk uppercase tracking-widest text-xs text-[#012d1d]">
            {{ $label }}
        </label>
    @endif
    <input
        id="{{ $name }}"
        name="{{ $name }}"
        type="{{ $type }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'border-[3px] border-[#012d1d] px-4 py-3 rounded-none bg-white focus:outline-none focus:border-[#D4EF70] transition-colors font-manrope']) }}
    />
    @if($error)
        <span class="text-red-600 text-xs font-manrope">{{ $error }}</span>
    @endif
</div>
