{{--
  ui/toast — Forest Brutalist notification toast
  Used for cart add confirmations and general feedback.

  Usage: Include once in layout via <x-ui.toast />
  Trigger via:
    window.dispatchEvent(new CustomEvent('show-toast', {
      detail: { title: 'Product Name', subtitle: 'ditambahkan ke keranjang', type: 'cart' }
    }));
--}}

{{-- Toast Container — fixed position, top-right --}}
<div id="dapute-toast-container"
     class="fixed top-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"
     style="max-width: 380px; width: calc(100vw - 48px);">
</div>

{{-- Toast Template (hidden, cloned by JS) --}}
<template id="dapute-toast-template">
    <div class="dapute-toast pointer-events-auto relative
                bg-white border-[3px] border-[#012d1d]
                shadow-[4px_4px_0_0_#012d1d]
                flex items-start gap-3 p-4
                transform translate-x-[calc(100%+24px)]
                transition-all duration-300 ease-out overflow-hidden"
         role="alert">

        {{-- Icon container --}}
        <div class="toast-icon flex-shrink-0 w-9 h-9 flex items-center justify-center border-[3px] border-[#012d1d]">
            {{-- Success icon (default) --}}
            <svg class="icon-success w-5 h-5 text-[#012d1d]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="square" stroke-linejoin="miter" d="M5 13l4 4L19 7" />
            </svg>
            {{-- Cart icon --}}
            <svg class="icon-cart w-5 h-5 text-white hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="square" stroke-linejoin="miter" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 100 4 2 2 0 000-4z" />
            </svg>
        </div>

        {{-- Text content --}}
        <div class="flex-1 min-w-0">
            <p class="toast-title font-[var(--font-display)] font-bold text-sm text-[#012d1d] leading-tight truncate"></p>
            <p class="toast-subtitle font-[var(--font-body)] text-xs text-[#3d6651] mt-0.5"></p>
        </div>

        {{-- Close button --}}
        <button type="button"
                class="toast-close flex-shrink-0 w-6 h-6 flex items-center justify-center text-[#3d6651] hover:text-[#012d1d] transition-colors duration-150"
                aria-label="Tutup">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                <path stroke-linecap="square" stroke-linejoin="miter" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>

        {{-- Progress bar (auto-dismiss indicator) --}}
        <div class="toast-progress absolute bottom-0 left-0 h-[3px] bg-[#D4EF70]" style="width: 100%;"></div>
    </div>
</template>

<script>
(function () {
    const container = document.getElementById('dapute-toast-container');
    const template  = document.getElementById('dapute-toast-template');
    const DURATION   = 3500;

    window.addEventListener('show-toast', function (e) {
        const { title = 'Berhasil!', subtitle = '', type = 'success' } = e.detail || {};

        // Clone template
        const toast = template.content.firstElementChild.cloneNode(true);

        // Fill text
        toast.querySelector('.toast-title').textContent = title;
        toast.querySelector('.toast-subtitle').textContent = subtitle;

        // Style by type
        const iconContainer = toast.querySelector('.toast-icon');
        const iconSuccess   = toast.querySelector('.icon-success');
        const iconCart       = toast.querySelector('.icon-cart');

        if (type === 'cart') {
            iconContainer.style.backgroundColor = '#012d1d';
            iconSuccess.classList.add('hidden');
            iconCart.classList.remove('hidden');
        } else {
            iconContainer.style.backgroundColor = '#D4EF70';
        }

        // Close handler
        toast.querySelector('.toast-close').addEventListener('click', () => dismissToast(toast));

        // Add to container
        container.appendChild(toast);

        // Slide in (next frame)
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                toast.style.transform = 'translateX(0)';
            });
        });

        // Progress bar animation
        const progressBar = toast.querySelector('.toast-progress');
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                progressBar.style.transition = 'width ' + DURATION + 'ms linear';
                progressBar.style.width = '0%';
            });
        });

        // Auto-dismiss
        setTimeout(() => dismissToast(toast), DURATION);
    });

    function dismissToast(toast) {
        if (!toast || !toast.parentNode) return;
        toast.style.transform = 'translateX(calc(100% + 24px))';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
    }
})();
</script>
