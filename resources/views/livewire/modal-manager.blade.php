<div>
    @foreach($modalStack as $index => $modal)
    @php
    $component = $modal['component'];
    $params = $modal['params'];
    @endphp

    <div x-data="modalHandler({{ $modal['depth'] }}, '{{ $modal['id'] }}', '{{ $modal['params']['size'] ?? 'md' }}')"
        x-init="init()" x-show="show" x-cloak x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @modal-closed.window="handleModalClosed($event.detail.modalId)"
        @all-modals-closed.window="closeAll()" @keydown.escape.window="handleEscape()"
        class="fixed inset-0 z-50 overflow-y-auto"
        style="z-index: {{ config('modal.z_index_base', 50) + ($modal['depth'] * config('modal.z_index_increment', 10)) }};"
        aria-labelledby="modal-title-{{ $modal['id'] }}" role="dialog" aria-modal="true">
        @if(config('modal.backdrop_close', true))
        <div class="fixed inset-0 bg-black transition-opacity" :style="`opacity: ${0.4 + (depth * 0.1)};`"
            @click="closeOnBackdrop()" aria-hidden="true"></div>
        @endif

        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 w-full"
                :class="{
                        'sm:max-w-sm': size === 'sm',
                        'sm:max-w-md': size === 'md',
                        'sm:max-w-lg': size === 'lg',
                        'sm:max-w-xl': size === 'xl',
                        'sm:max-w-2xl': size === '2xl',
                        'sm:max-w-3xl': size === '3xl',
                        'sm:max-w-4xl': size === '4xl',
                        'sm:max-w-5xl': size === '5xl',
                        'sm:max-w-full sm:mx-4': size === 'full'
                    }" @click.stop>

                @livewire(
                $component,
                [
                'modalId' => $params['modalId'] ?? null,
                'title' => $params['title'] ?? '',
                'size' => $params['size'] ?? 'md',
                'closeable' => $params['closeable'] ?? true,
                'userId' => $params['userId'] ?? null,
                // Add other common parameters here
                ],
                key($modal['id'])
                )
            </div>
        </div>
    </div>
    @endforeach
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('modalHandler', (depth, modalId, size) => ({
            show: false,
            depth: depth,
            modalId: modalId,
            size: size, // Add this line - it was missing!

            init() {
                this.show = true;
                if (depth === 0 && @json(config('modal.scroll_lock', true))) {
            this.lockScroll();
        }
        },

        handleModalClosed(closedModalId) {
        if(this.modalId === closedModalId) {
        this.close();
    }
        },

    closeAll() {
        this.close();
    },

    close() {
        this.show = false;
        if (this.depth === 0) {
            this.unlockScroll();
        }
    },

    handleEscape() {
        if (!@json(config('modal.escape_close', true))) return;

        if (this.$wire.modalStack.length > 0) {
            const topModal = this.$wire.modalStack[this.$wire.modalStack.length - 1];
            if (this.modalId === topModal.id) {
                this.$wire.closeModal(this.modalId);
            }
        }
    },

    closeOnBackdrop() {
        if (!@json(config('modal.backdrop_close', true))) return;

        if (this.$wire.modalStack.length > 0) {
            const topModal = this.$wire.modalStack[this.$wire.modalStack.length - 1];
            if (this.modalId === topModal.id) {
                this.$wire.closeModal(this.modalId);
            }
        }
    },

    lockScroll() {
        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
        document.body.style.overflow = 'hidden';
        document.body.style.paddingRight = `${scrollbarWidth}px`;
        document.body.classList.add('modal-open');
    },

    unlockScroll() {
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
        document.body.classList.remove('modal-open');
    }
    }));
});
</script>
