<?php

namespace Repay\Modal\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\LivewireServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Repay\Modal\ModalServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function getPackageProviders($app): array
    {
        return [
			LivewireServiceProvider::class,
            ModalServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations(): void
    {

    }

	protected function defineEnvironment($app)
{
    \Livewire\Livewire::component('modal-manager', \Repay\Modal\Livewire\ModalManager::class);
}

	
    protected function getEnvironmentSetUp($app): void
    {

	  config()->set('app.key', 'base64:' . base64_encode(random_bytes(32)));
    
    // Alternatively, generate a test key
    if (empty(config('app.key'))) {
        config()->set('app.key', 'base64:'.base64_encode(
            \Illuminate\Encryption\Encrypter::generateKey(config('app.cipher'))

        ));

    }

        // Configure the modal system
        $app['config']->set('modal.max_depth', 5);

        $app['config']->set('modal.default_size', 'md');
        $app['config']->set('modal.scroll_lock', true);
        $app['config']->set('modal.backdrop_close', true);
        $app['config']->set('modal.escape_close', true);
        $app['config']->set('modal.z_index_base', 50);
        $app['config']->set('modal.z_index_increment', 10);
        
        $app['config']->set('modal.sizes', [
            'sm' => 'sm:max-w-sm',

            'md' => 'sm:max-w-md',
            'lg' => 'sm:max-w-lg',
            'xl' => 'sm:max-w-xl',
            '2xl' => 'sm:max-w-2xl',
            '3xl' => 'sm:max-w-3xl',
            '4xl' => 'sm:max-w-4xl',
            '5xl' => 'sm:max-w-5xl',
            'full' => 'sm:max-w-full sm:mx-4',
        ]);
        
        $app['config']->set('modal.allowed_namespaces', [
            'App\\Livewire\\Modals\\',
            'App\\Http\\Livewire\\Modals\\',
            'Repay\\Modal\\Tests\\Fixtures\\',
        ]);
        
        $app['config']->set('livewire.class_namespace', 'Repay\\Modal\\Tests\\Fixtures');
        
        $app['config']->set('view.paths', [
            __DIR__.'/../resources/views',
            resource_path('views'),
        ]);
    }

}
