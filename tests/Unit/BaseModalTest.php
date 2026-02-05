<?php

namespace Repay\Modal\Tests\Unit;

use Repay\Modal\Tests\Fixtures\TestModal;
use Livewire\Livewire;

it('can instantiate base modal', function () {
    $modal = new TestModal();
    
    expect($modal)->toBeInstanceOf(TestModal::class);
});

it('mounts with correct parameters', function () {
    Livewire::test(TestModal::class, [
        'modalId' => 'test-123',
        'title' => 'My Test Modal',
        'size' => 'xl',
        'closeable' => true,
    ])
    ->assertSet('modalId', 'test-123')
    ->assertSet('title', 'My Test Modal')
    ->assertSet('size', 'xl')
    ->assertSet('closeable', true);
});

it('generates modal id if not provided', function () {
    Livewire::test(TestModal::class)
        ->assertSet('modalId', function ($id) {
            return !empty($id) && is_string($id);
        });
});

it('uses default values when params not provided', function () {
    Livewire::test(TestModal::class)
        ->assertSet('size', 'md')
        ->assertSet('closeable', true)
        ->assertSet('showCloseButton', true);
});

it('can get modal id', function () {
    $component = Livewire::test(TestModal::class, ['modalId' => 'test-456']);
    
    expect($component->instance()->getModalId())->toBe('test-456');
});

it('can get modal title', function () {
    $component = Livewire::test(TestModal::class, ['title' => 'My Title']);
    
    expect($component->instance()->getTitle())->toBe('My Title');
});

it('can check if closeable', function () {
    $closeable = Livewire::test(TestModal::class, ['closeable' => true]);
    $notCloseable = Livewire::test(TestModal::class, ['closeable' => false]);
    
    expect($closeable->instance()->isCloseable())->toBeTrue()
        ->and($notCloseable->instance()->isCloseable())->toBeFalse();
});

it('dispatches close event when closeModal is called', function () {
    Livewire::test(TestModal::class, ['modalId' => 'test-789'])
        ->call('closeModal')
        ->assertDispatched('closeModal');
});

it('passes custom parameters to setupModal', function () {
    Livewire::test(TestModal::class, [
        'title' => 'Custom Title',
        'userId' => 123
    ])
    ->assertSet('title', 'Custom Title');
});

it('handles null userId parameter', function () {
    Livewire::test(TestModal::class, [
        'title' => 'Test',
        'userId' => null
    ])
    ->assertSet('title', 'Test');
});

it('returns correct modal size class', function () {
    $component = Livewire::test(TestModal::class, ['size' => 'lg']);
    
    $sizeClass = $component->instance()->getModalSizeClass();
    
    expect($sizeClass)->toBe('sm:max-w-lg');
});

it('returns default size class for invalid size', function () {
    $component = Livewire::test(TestModal::class, ['size' => 'invalid']);
    
    $sizeClass = $component->instance()->getModalSizeClass();
    
    // Should return the default 'md' size
    expect($sizeClass)->toBe('sm:max-w-md');
});

it('preserves all size options', function () {
    $sizes = ['sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl', 'full'];
    
    foreach ($sizes as $size) {
        $component = Livewire::test(TestModal::class, ['size' => $size]);
        $sizeClass = $component->instance()->getModalSizeClass();
        
        expect($sizeClass)->toContain('max-w');
    }
});
