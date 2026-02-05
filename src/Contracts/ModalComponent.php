<?php

namespace Repay\Modal\Contracts;

interface ModalComponent
{
    public function mount(array $params = []): void;
    public function getModalSizeClass(): string;
    public function closeModal(): void;
    public function getModalId(): string;
    public function getTitle(): string;
    public function isCloseable(): bool;
}
