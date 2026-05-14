import mask    from '@alpinejs/mask';
import focus   from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';

// Livewire v3 bundles and starts Alpine automatically.
// Register plugins into Livewire's Alpine instance via the alpine:init event.
// DO NOT import Alpine or call Alpine.start() here — that creates a second instance.
document.addEventListener('alpine:init', () => {
    Alpine.plugin(mask);
    Alpine.plugin(focus);
    Alpine.plugin(collapse);
});
