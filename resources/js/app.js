import Alpine from 'alpinejs';
import mask    from '@alpinejs/mask';
import focus   from '@alpinejs/focus';
import collapse from '@alpinejs/collapse';

Alpine.plugin(mask);
Alpine.plugin(focus);
Alpine.plugin(collapse);

window.Alpine = Alpine;
Alpine.start();
