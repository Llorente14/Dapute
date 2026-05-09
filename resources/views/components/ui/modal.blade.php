{{--
  ui/modal — Alpine.js driven
  Props: $title
  Usage: wrap with x-data="{ open: false }"
--}}
@props(['title' => ''])

<div x-show="open" x-cloak class="fixed inset-0 z-50 flex items-center justify-center">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-[#012d1d]/60" @click="open = false"></div>

    {{-- Panel --}}
    <div class="relative bg-white border-[3px] border-[#012d1d] shadow-[6px_6px_0_0_#012d1d] w-full max-w-lg mx-4 p-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="font-epilogue font-black text-xl uppercase text-[#012d1d]">{{ $title }}</h2>
            <button @click="open = false" class="border-[3px] border-[#012d1d] px-2 py-1 text-xs font-space-grotesk uppercase">✕</button>
        </div>
        {{ $slot }}
    </div>
</div>
