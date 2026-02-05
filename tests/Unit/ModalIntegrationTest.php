<?php

namespace Repay\Modal\Tests\Feature;

use Repay\Modal\Livewire\ModalManager;
use Repay\Modal\Tests\Fixtures\TestModal;
use Repay\Modal\Traits\InteractsWithModal;
use Livewire\Component;
use Livewire\Livewire;

// Create a page component that uses modals
class TestPage extends Component
{
    use InteractsWithModal;

    public function openTestModal()
    {
        $this->openModal(TestModal::class, [
            'title' => 'Test Modal from Page',
            'size' => 'lg',
            'userId' => 123
        ]);
    }

    public function render()
    {
        return '<div><button wire:click="openTestModal">Open Modal</button></div>';
    }
}

beforeEach(function () {
    Livewire::component('test-page', TestPage::class);
});

it('can open modal from page component', function () {
    $page = Livewire::test(TestPage::class);
    
    $page->call('openTestModal')
        ->assertDispatched('openModal');
});

it('modal manager receives event from page component', function () {
    // First, open a modal from the page
    $page = Livewire::test(TestPage::class);
    $page->call('openTestModal');
    
    // The manager should be able to process this
    $manager = Livewire::test(ModalManager::class);
    
    $manager->call('openModal', [
        'component' => TestModal::class,
        'params' => [
            'title' => 'Test Modal from Page',
            'size' => 'lg',
            'userId' => 123
        ]
    ]);
    
    expect(count($manager->get('modalStack')))->toBe(1);
});

it('can open multiple modals from page component', function () {
    $manager = Livewire::test(ModalManager::class);
    
    // Simulate multiple opens from page
    $manager
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'First Modal', 'userId' => 1]
        ])
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'Second Modal', 'userId' => 2]
        ]);
    
    expect(count($manager->get('modalStack')))->toBe(2);
});

it('modal receives correct parameters from page', function () {
    $modal = Livewire::test(TestModal::class, [
        'modalId' => 'test-id',
        'title' => 'Test Modal from Page',
        'size' => 'lg',
        'userId' => 123
    ]);
    
    expect($modal->get('title'))->toBe('Test Modal from Page')
        ->and($modal->get('size'))->toBe('lg')
        ->and($modal->get('modalId'))->toBe('test-id');
});

it('can close modal and return to page', function () {
    $manager = Livewire::test(ModalManager::class)
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'Test']
        ]);
    
    $modalId = $manager->get('modalStack')[0]['id'];
    
    // Close the modal
    $manager->call('closeModal', $modalId);
    
    expect(count($manager->get('modalStack')))->toBe(0);
});

it('page component can close modal by id', function () {
    $manager = Livewire::test(ModalManager::class)
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'Test', 'modalId' => 'specific-id']
        ]);
    
    $page = Livewire::test(TestPage::class);
    $page->call('closeModal', 'specific-id')
        ->assertDispatched('closeModal');
});

it('page component can close all modals', function () {
    $page = Livewire::test(TestPage::class);
    
    $page->call('closeAllModals')
        ->assertDispatched('closeAllModals');
});

it('maintains separation between multiple page instances', function () {
    $page1 = Livewire::test(TestPage::class);
    $page2 = Livewire::test(TestPage::class);
    
    $page1->call('openTestModal')->assertDispatched('openModal');
    $page2->call('openTestModal')->assertDispatched('openModal');
    
    // Each page instance should dispatch independently
    expect($page1->instance())->not->toBe($page2->instance());
});
