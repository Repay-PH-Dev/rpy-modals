<div>
    {{-- Modal Stack Container --}}

    @foreach($modalStack as $index => $modal)

        <div
            x-data="modalHandler({{ $index }}, {{ $modal['depth'] }}, '{{ $modal['id'] }}')"
            x-init="init()"
            x-show="show"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @modal-closed.window="handleModalClosed($event.detail.modalId)"
            @all-modals-closed.window="closeAll()"

            @keydown.escape.window="handleEscape()"
            class="fixed inset-0 z-50 overflow-y-auto"
            style="z-index: {{ 50 + ($modal['depth'] * 10) }};"
            aria-labelledby="modal-title-{{ $modal['id'] }}"
            role="dialog"
            aria-modal="true"
        >

            {{-- Backdrop with increasing opacity for nested modals --}}

            <div
                class="fixed inset-0 bg-black transition-opacity"
                :style="`opacity: ${0.4 + (depth * 0.1)};`"
                @click="closeOnBackdrop()"
                aria-hidden="true"
            ></div>

            {{-- Modal Container --}}
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div
                    x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"

                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 w-full sm:max-w-lg sm:w-full"
                    @click.stop
                >
                    {{-- Render the actual modal component --}}
                    @livewire($modal['component'], array_merge($modal['params'], ['modalId' => $modal['id']]), key($modal['id']))
                </div>
            </div>
        </div>
    @endforeach
</div>

@push('scripts')

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('modalHandler', (index, depth, modalId) => ({
        show: false,
        index: index,
        depth: depth,
        modalId: modalId,

        init() {
            console.log('Modal initialized:', { index, depth, modalId });
            // Show modal after a tiny delay to ensure smooth animation
            setTimeout(() => {

                this.show = true;
                this.lockScroll();
                this.trapFocus();
            }, 50);
        },

        handleModalClosed(closedModalId) {

            console.log('Modal closed event received:', closedModalId, 'Current modal:', this.modalId);
            if (this.modalId === closedModalId) {
                this.close();
            }

        },

        closeAll() {
            console.log('Closing all modals');

            this.close();
        },

        close() {
            this.show = false;
            setTimeout(() => {
                this.unlockScroll();

                // Let Livewire handle the actual removal
            }, 300);
        },

        handleEscape() {
            console.log('Escape pressed, modal stack length:', this.$wire.modalStack.length);
            // Only the topmost modal should handle escape
            if (this.$wire.modalStack.length > 0) {
                const topModal = this.$wire.modalStack[this.$wire.modalStack.length - 1];

                if (this.modalId === topModal.id) {
                    console.log('Closing modal via escape:', this.modalId);
                    this.$wire.closeModal(this.modalId);
                }
            }
        },

        closeOnBackdrop() {

            console.log('Backdrop clicked, modal stack length:', this.$wire.modalStack.length);
            if (this.$wire.modalStack.length > 0) {
                const topModal = this.$wire.modalStack[this.$wire.modalStack.length - 1];
                if (this.modalId === topModal.id) {

                    console.log('Closing modal via backdrop:', this.modalId);
                    this.$wire.closeModal(this.modalId);
                }
            }
        },

lockScroll() {
    // Only lock scroll for the first modal

    if (this.depth === 0 && !document.body.classList.contains('modal-open')) {
        console.log('Locking scroll');

        const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;


        document.body.style.overflow = 'hidden';
        if (scrollbarWidth > 0) {
            document.body.style.paddingRight = `${scrollbarWidth}px`;
        }

        // Add a class to prevent double-locking

        document.body.classList.add('modal-open');

    }
},

unlockScroll() {
    // Only unlock if no more modals are open
    setTimeout(() => {
        if (this.$wire.modalStack.length === 0 && document.body.classList.contains('modal-open')) {
            console.log('Unlocking scroll');

            document.body.style.overflow = '';
            document.body.style.paddingRight = '';


            document.body.classList.remove('modal-open');
        }
    }, 200);
},


        getScrollbarWidth() {
            return window.innerWidth - document.documentElement.clientWidth;

        },

        trapFocus() {
            // Focus trapping for accessibility
            const modal = this.$el.querySelector('.relative.transform');
            if (!modal) {
                console.warn('Modal container not found for focus trapping');
                return;
            }


            const focusableElements = modal.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );


            if (focusableElements.length === 0) {
                console.warn('No focusable elements found in modal');
                return;
            }

            const firstFocusable = focusableElements[0];
            const lastFocusable = focusableElements[focusableElements.length - 1];

            // Focus first element
            setTimeout(() => {
                firstFocusable.focus();
            }, 100);

            // Trap focus within modal
            const handleTabKey = (e) => {
                if (e.key !== 'Tab') return;

                if (e.shiftKey) {
                    if (document.activeElement === firstFocusable) {

                        lastFocusable.focus();
                        e.preventDefault();
                    }
                } else {
                    if (document.activeElement === lastFocusable) {
                        firstFocusable.focus();
                        e.preventDefault();
                    }
                }
            };

            modal.addEventListener('keydown', handleTabKey);

            // Store the handler for cleanup
            this.focusHandler = handleTabKey;
        },


        cleanup() {
            // Cleanup event listeners
            if (this.focusHandler) {
                const modal = this.$el.querySelector('.relative.transform');
                if (modal) {
                    modal.removeEventListener('keydown', this.focusHandler);
                }
            }

        }
    }));
});

</script>
@endpush
