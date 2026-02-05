<?php

namespace Repay\Modal\Tests\Unit;

use Repay\Modal\Livewire\ModalManager;
use Repay\Modal\Tests\Fixtures\TestModal;
use Livewire\Livewire;

beforeEach(function () {
    // Any setup needed before each test
});

it('can instantiate modal manager', function () {
    $manager = new ModalManager();
    
    expect($manager)->toBeInstanceOf(ModalManager::class)
        ->and($manager->modalStack)->toBeArray()
        ->and($manager->modalStack)->toBeEmpty();
});

it('can open a modal', function () {
    Livewire::test(ModalManager::class)
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => [
                'title' => 'Test Modal',
                'size' => 'lg',
            ]
        ])
        ->assertSet('modalStack', function ($stack) {
            return count($stack) === 1 
                && $stack[0]['component'] === TestModal::class
                && $stack[0]['params']['title'] === 'Test Modal';
        });
});

it('generates unique modal id when opening', function () {
    Livewire::test(ModalManager::class)
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'Test']
        ])
        ->assertSet('modalStack', function ($stack) {
            return isset($stack[0]['id']) 
                && isset($stack[0]['params']['modalId'])
                && $stack[0]['id'] === $stack[0]['params']['modalId'];
        });
});

it('can open multiple modals', function () {
    Livewire::test(ModalManager::class)
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'First']
        ])
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'Second']
        ])
        ->assertSet('modalStack', function ($stack) {
            return count($stack) === 2
                && $stack[0]['component'] === TestModal::class
                && $stack[1]['component'] === TestModal::class
                && $stack[0]['params']['title'] === 'First'
                && $stack[1]['params']['title'] === 'Second';
        });
});

it('tracks modal depth correctly', function () {
    Livewire::test(ModalManager::class)
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'First']
        ])
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'Second']
        ])
        ->assertSet('modalStack', function ($stack) {
            return $stack[0]['depth'] === 0 && $stack[1]['depth'] === 1;
        });
});

it('can close the last modal', function () {
    $component = Livewire::test(ModalManager::class)
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'Test']
        ]);
    
    $modalId = $component->get('modalStack')[0]['id'];
    
    $component->call('closeModal', $modalId)
        ->assertSet('modalStack', []);
});

it('can close a specific modal by id', function () {
    $component = Livewire::test(ModalManager::class)
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'First']
        ])
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'Second']
        ]);
    
    $firstModalId = $component->get('modalStack')[0]['id'];
    
    $component->call('closeModal', $firstModalId)
        ->assertSet('modalStack', function ($stack) {
            return count($stack) === 1 
                && $stack[0]['params']['title'] === 'Second';
        });
});

it('can close all modals', function () {
    Livewire::test(ModalManager::class)
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'First']
        ])
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'Second']
        ])
        ->call('closeAllModals')
        ->assertSet('modalStack', []);
});

it('handles closing non-existent modal gracefully', function () {
    Livewire::test(ModalManager::class)
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'Test']
        ])
        ->call('closeModal', 'non-existent-id')
        ->assertSet('modalStack', function ($stack) {
            return count($stack) === 1;
        });
});

it('handles closing when stack is empty', function () {
    Livewire::test(ModalManager::class)
        ->call('closeModal')
        ->assertSet('modalStack', []);
});

it('includes timestamp when opening modal', function () {
    Livewire::test(ModalManager::class)
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'Test']
        ])
        ->assertSet('modalStack', function ($stack) {
            return isset($stack[0]['timestamp']) 
                && is_numeric($stack[0]['timestamp']);
        });
});

it('preserves all params when opening modal', function () {
    Livewire::test(ModalManager::class)
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => [
                'title' => 'Test Modal',
                'size' => '2xl',
                'closeable' => false,
                'userId' => 123,
                'customData' => 'test'
            ]
        ])
        ->assertSet('modalStack', function ($stack) {
            $params = $stack[0]['params'];
            return $params['title'] === 'Test Modal'
                && $params['size'] === '2xl'
                && $params['closeable'] === false
                && $params['userId'] === 123
                && $params['customData'] === 'test';
        });
});

it('can close last modal with null parameter', function () {
    Livewire::test(ModalManager::class)
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'First']
        ])
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'Second']
        ])
        ->call('closeModal', null)
        ->assertSet('modalStack', function ($stack) {
            return count($stack) === 1 
                && $stack[0]['params']['title'] === 'First';
        });
});

it('maintains correct array indices after closing middle modal', function () {
    $component = Livewire::test(ModalManager::class)
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'First']
        ])
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'Second']
        ])
        ->call('openModal', [
            'component' => TestModal::class,
            'params' => ['title' => 'Third']
        ]);
    
    $middleModalId = $component->get('modalStack')[1]['id'];
    
    $component->call('closeModal', $middleModalId)
        ->assertSet('modalStack', function ($stack) {
            // Check that array is re-indexed (0, 1 instead of 0, 2)
            return count($stack) === 2 
                && array_keys($stack) === [0, 1]
                && $stack[0]['params']['title'] === 'First'
                && $stack[1]['params']['title'] === 'Third';
        });
});
