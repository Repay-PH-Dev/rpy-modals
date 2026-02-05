<?php

namespace Repay\Modal\Traits;

trait InteractsWithModal
{
    public function openModal(string $component, array $params = []): void
    {
        $this->dispatch('openModal', [
            'component' => $component,
            'params' => $params
        ])->to('modal-manager');
    }

    public function closeModal(string $modalId): void
    {
        $this->dispatch('closeModal', modalId: $modalId)->to('modal-manager');
    }

    public function closeAllModals(): void
    {
        $this->dispatch('closeAllModals')->to('modal-manager');
    }
}
