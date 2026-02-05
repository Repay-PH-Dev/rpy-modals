<?php

namespace Repay\Modal\Livewire\Modals;

use Illuminate\Support\Str;
use Livewire\Component;
use Repay\Modal\Contracts\ModalComponent;

abstract class BaseModal extends Component implements ModalComponent
{
    public string $modalId;

    public string $size;

    public bool $closeable;

    public bool $showCloseButton = true;

    public string $title = '';

    protected $listeners = ['closeThisModal' => 'closeModal'];

    public function mount(
        $modalId = null,
        $title = '',
        $size = 'md',
        $closeable = true,
        $userId = null
    ): void {

        $this->modalId = $modalId ?? Str::uuid();
        $this->title = $title;
        $this->size = $size;
        $this->closeable = $closeable;

        // Create params array for setupModal
        $params = [
            'modalId' => $this->modalId,
            'title' => $this->title,
            'size' => $this->size,
            'closeable' => $this->closeable,
            'userId' => $userId,
        ];

        $this->setupModal($params);
    }

    abstract protected function setupModal(array $params): void;

    public function closeModal(): void
    {
        $this->dispatch('closeModal', modalId: $this->modalId)->to('modal-manager');
    }

    protected function openNestedModal(string $component, array $params = []): void
    {
        $this->dispatch('openModal', [
            'component' => $component,
            'params' => $params,
        ])->to('modal-manager');
    }

    public function getModalSizeClass(): string
    {
        return config("modal.sizes.{$this->size}", config('modal.sizes.md'));
    }

    public function getModalId(): string
    {
        return $this->modalId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function isCloseable(): bool
    {
        return $this->closeable;
    }

    abstract public function render();
}
