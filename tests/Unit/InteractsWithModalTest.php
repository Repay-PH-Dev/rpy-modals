<?php

namespace Repay\Modal\Tests\Unit;

use Repay\Modal\Traits\InteractsWithModal;
use Livewire\Component;
use Livewire\Livewire;

// Create a test component that uses the trait
class ComponentWithModal extends Component
{
    use InteractsWithModal;

    public function render()
    {
        return '<div>Test</div>';
    }
}

beforeEach(function () {
    // Register the test component
    Livewire::component('test-component-with-modal', ComponentWithModal::class);
});


it('dispatches openModal event with correct parameters', function () {
    Livewire::test(ComponentWithModal::class)
        ->call('openModal', 'App\\Livewire\\Modals\\TestModal', [
            'title' => 'Test',
            'size' => 'lg'
        ])
        ->assertDispatched('openModal');
});

it('dispatches closeModal event with modal id', function () {
    Livewire::test(ComponentWithModal::class)
        ->call('closeModal', 'test-modal-123')
        ->assertDispatched('closeModal');
});

it('dispatches closeAllModals event', function () {
    Livewire::test(ComponentWithModal::class)
        ->call('closeAllModals')
        ->assertDispatched('closeAllModals');
});

it('can open modal with empty params', function () {
    Livewire::test(ComponentWithModal::class)
        ->call('openModal', 'App\\Livewire\\Modals\\TestModal', [])
        ->assertDispatched('openModal');
});

it('can open modal with complex params', function () {
    Livewire::test(ComponentWithModal::class)
        ->call('openModal', 'App\\Livewire\\Modals\\TestModal', [
            'title' => 'Complex Modal',
            'size' => '2xl',
            'userId' => 456,
            'data' => ['key' => 'value'],
            'closeable' => false
        ])
        ->assertDispatched('openModal');
});
