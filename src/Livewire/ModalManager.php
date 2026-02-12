<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;

class ModalManager extends Component
{
    /**

     * Stack of active modals with their data
     * Each modal has: id, component, params, timestamp
     */
    public array $modalStack = [];

    /**

     * Maximum number of nested modals allowed (prevent abuse)
     */

    protected int $maxModalDepth = 5;

    /**
     * Listeners for modal events

     */
    protected $listeners = [
        'openModal' => 'openModal',
        'openModalAlpine' => 'openModalAlpine',
        'closeModal' => 'closeModal',
        'closeAllModals' => 'closeAllModals',
    ];

    public function openModalAlpine(string $component, array $params = []): void
{
    if (count($this->modalStack) >= $this->maxModalDepth) {
        $this->dispatch('modal-error', message: 'Maximum modal depth reached');
        return;
    }

    if (!$this->isValidModalComponent($component)) {

        $this->dispatch('modal-error', message: 'Invalid modal component');
        return;
    }

    $sanitizedParams = $this->sanitizeParams($params);


    $modalId = Str::uuid()->toString();
    $sanitizedParams['modalId'] = $modalId;

    // Add modal to stack
    $this->modalStack[] = [
        'id' => $modalId,
        'component' => $component,
        'params' => $sanitizedParams,


        'timestamp' => now()->timestamp,
        'depth' => count($this->modalStack),
    ];



    // Dispatch event to frontend

    $this->dispatch('modal-opened', modalId: $modalId);
}

    public function openModal(array $data): void
    {

    $component = $data['component'] ?? '';
    $params = $data['params'] ?? [];

    if (count($this->modalStack) >= $this->maxModalDepth) {

        $this->dispatch('modal-error', message: 'Maximum modal depth reached');

        return;
    }

    if (!$this->isValidModalComponent($component)) {
        $this->dispatch('modal-error', message: 'Invalid modal component');
        return;
    }


    $sanitizedParams = $this->sanitizeParams($params);

    $modalId = Str::uuid()->toString();

    $sanitizedParams['modalId'] = $modalId;

    // Add modal to stack
    $this->modalStack[] = [
        'id' => $modalId,
        'component' => $component,
        'params' => $sanitizedParams, // This now includes modalId
        'timestamp' => now()->timestamp,
        'depth' => count($this->modalStack),
    ];


    // Dispatch event to frontend

    $this->dispatch('modal-opened', modalId: $modalId);
    }


    /**
     * Close the most recent modal or a specific modal by ID

     *
     * @param string|null $modalId Optional specific modal ID to close
     * @return void
     */

    public function closeModal($payload = null): void
    {
        if (empty($this->modalStack)) {
            return;
        }

        // Event payload case

        if (is_array($payload)) {
            $modalId = $payload['modalId'] ?? null;
        } else {
            $modalId = $payload;
        }

        if ($modalId === null) {
            $closed = array_pop($this->modalStack);
            $this->dispatch('modal-closed', modalId: $closed['id']);
            return;
        }


        $this->modalStack = array_values(array_filter(
            $this->modalStack,
            fn ($modal) => $modal['id'] !== $modalId
        ));

        $this->dispatch('modal-closed', modalId: $modalId);
    }



    /**
     * Close all modals in the stack
     *
     * @return void
     */

    public function closeAllModals(): void
    {
        $this->modalStack = [];

        $this->dispatch('all-modals-closed');
    }


    /**
     * Validate if component is a valid modal component

     *
     * @param string $component
     * @return bool
     */
    protected function isValidModalComponent(string $component): bool
    {
        // Security: Only allow components from specific namespaces
        $allowedNamespaces = [
            'App\\Livewire\\',
            'App\\Http\\Livewire\\Modals\\',
        ];

        $isAllowedNamespace = false;
        foreach ($allowedNamespaces as $namespace) {
            if (Str::startsWith($component, $namespace)) {
                $isAllowedNamespace = true;
                break;
            }
        }

        if (!$isAllowedNamespace) {
            return false;
        }

        // Check if class exists
        if (!class_exists($component)) {
            return false;
        }

        // Check if it's a Livewire component
        $reflection = new \ReflectionClass($component);
        return $reflection->isSubclassOf(Component::class);
    }

    /**
     * Sanitize parameters to prevent XSS and injection attacks
     *
     * @param array $params
     * @return array
     */
    protected function sanitizeParams(array $params): array
    {
        $sanitized = [];

        foreach ($params as $key => $value) {
            // Only allow alphanumeric keys with underscores
            if (!preg_match('/^[a-zA-Z0-9_]+$/', $key)) {
                continue;
            }

            // Recursively sanitize arrays
            if (is_array($value)) {

                $sanitized[$key] = $this->sanitizeParams($value);
            }
            // Allow specific types only
            elseif (is_string($value)) {
                // Limit string length to prevent abuse
                $sanitized[$key] = mb_substr($value, 0, 1000);
            }
            elseif (is_numeric($value) || is_bool($value) || is_null($value)) {
                $sanitized[$key] = $value;
            }
            // Skip other types for security
        }

        return $sanitized;
    }

    /**
     * Get the current modal depth
     *
     * @return int
     */
    public function getModalDepth(): int
    {
        return count($this->modalStack);
    }

    /**
     * Check if any modals are open
     *

     * @return bool

     */
    public function hasOpenModals(): bool
    {
        return !empty($this->modalStack);
    }


    public function render()
    {
        return view('livewire.modal-manager');

    }
}
