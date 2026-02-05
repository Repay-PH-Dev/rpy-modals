<?php

namespace Repay\Modal\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallCommand extends Command
{
    protected $signature = 'modal:install';
    protected $description = 'Install the Modal package';

    public function handle(): void
    {
        $this->info('Installing Repay Modal Package...');

        if (!File::exists(config_path('modal.php'))) {
            $this->call('vendor:publish', [
                '--provider' => 'Repay\\Modal\\ModalServiceProvider',
                '--tag' => 'modal-config'
            ]);
        }

        $this->call('vendor:publish', [
            '--provider' => 'Repay\\Modal\\ModalServiceProvider',
            '--tag' => 'modal-views',
            '--force' => true
        ]);

        $this->createDirectories();

        $this->info('✅ Modal package installed successfully!');
        $this->line('');
        $this->line('Usage:');
        $this->line('  • Create a modal: php artisan make:modal UserEditModal');
        $this->line('  • Open modal from Livewire: $this->openModal(Modal::class, $params)');
        $this->line('  • Open modal from Blade: @modal-trigger or Alpine.js');
    }

    protected function createDirectories(): void
    {
        $directories = [
            app_path('Livewire/Modals'),
            resource_path('views/livewire/modals'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->info("✓ Created directory: " . $directory);
            }
        }
    }
}
