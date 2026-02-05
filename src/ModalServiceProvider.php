<?php

namespace Repay\Modal;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Repay\Modal\Console\InstallCommand;
use Repay\Modal\Console\MakeModalCommand;
use Repay\Modal\Livewire\ModalManager;

class ModalServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/modal.php', 'modal'
        );

        $this->app->singleton('repay.modal', function ($app) {
            return new ModalManager($app);
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallCommand::class,
                MakeModalCommand::class,
            ]);

            $this->publishes([
                __DIR__.'/../config/modal.php' => config_path('modal.php'),
            ], 'modal-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/modal'),
            ], 'modal-views');

            $this->publishes([
                __DIR__.'/../stubs' => base_path('stubs/modal'),
            ], 'modal-stubs');
        }

        Livewire::component('modal-manager', \Repay\Modal\Livewire\ModalManager::class);

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'modal');
    }
}
