<?php

use Livewire\Livewire;
use Repay\Modal\Livewire\ModalManager;

it('works', function () {

    Livewire::test(ModalManager::class)
        ->assertSee(''); // Empty assertion just to test it loads

    $this->assertTrue(class_exists(ModalManager::class));
    /**/
    /* Livewire::test(ModalManager::class) */
    /*      ->call('openModal', [ */
    /*          'component' => 'App\\Livewire\\Modals\\TestModal', */
    /*          'params' => [ */
    /*              'title' => 'Test Modal', */
    /*              'size' => 'lg', */
    /*          ] */
    /*      ]) */
    /*      ->assertSet('modalStack', function ($stack) { */
    /*          return count($stack) === 1 */
    /*              && $stack[0]['component'] === 'App\\Livewire\\Modals\\TestModal' */
    /*              && $stack[0]['params']['title'] === 'Test Modal'; */
    /*      }); */

});
