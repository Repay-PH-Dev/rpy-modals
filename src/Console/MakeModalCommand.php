<?php

namespace Repay\Modal\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModalCommand extends Command
{
    protected $signature = 'make:modal {name} {--force}';
    protected $description = 'Create a new modal component';

    public function handle(): void
    {
        $name = $this->argument('name');
        $force = $this->option('force');

        $className = Str::studly($name);
        $viewName = Str::kebab($name);

        $directory = app_path('Livewire/Modals');
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        $componentPath = $directory . '/' . $className . '.php';
        
        if (File::exists($componentPath) && !$force) {
            $this->error("Modal {$className} already exists!");
            return;
        }

        $stubPath = __DIR__ . '/../../stubs/modal.stub';
        
        if (!File::exists($stubPath)) {
            $this->error("Stub file not found at: {$stubPath}");
            $this->info('Creating default stub...');
            
            // Create default stub content
            $stubContent = <<<'STUB'
<?php

namespace {{ namespace }};

use Repay\Modal\Livewire\Modals\BaseModal;

class {{ class }} extends BaseModal
{
    protected function setupModal(array \$params): void
    {
        // Setup your modal properties here
        \$this->title = \$params['title'] ?? '{{ class }}';
    }

    public function render()
    {
        return view('livewire.modals.{{ view }}');
    }
}
STUB;
            
            File::ensureDirectoryExists(dirname($stubPath));
            File::put($stubPath, $stubContent);
        }

        $stub = File::get($stubPath);
        $stub = str_replace(
            ['{{ namespace }}', '{{ class }}', '{{ view }}'],
            ['App\\Livewire\\Modals', $className, $viewName],
            $stub
        );

        File::put($componentPath, $stub);

        $viewDirectory = resource_path('views/livewire/modals');
        if (!File::exists($viewDirectory)) {
            File::makeDirectory($viewDirectory, 0755, true);
        }

        $viewPath = $viewDirectory . '/' . $viewName . '.blade.php';
        $viewStubPath = __DIR__ . '/../../stubs/view.stub';
        
        if (!File::exists($viewPath) || $force) {
            if (File::exists($viewStubPath)) {
                $viewStub = File::get($viewStubPath);
                $viewStub = str_replace('{{ $view }}', $viewName, $viewStub);
            } else {
                // Default view stub
                $viewStub = <<<'BLADE'
<div>
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ \$title }}
        </h3>
        
        <p class="text-gray-500">
            Modal content goes here.
        </p>
    </div>
    
    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
        <button
            type="button"
            wire:click="closeModal"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
        >
            Close
        </button>
    </div>
</div>
BLADE;
            }
            
            File::put($viewPath, $viewStub);
        }

        $this->info("Modal {$className} created successfully!");
        $this->line("Component: {$componentPath}");
        $this->line("View: {$viewPath}");
    }
}
