<?php

namespace Repay\Modal\Livewire;

use Illuminate\Support\Str;
use Livewire\Component;

class ModalManager extends Component
{
    public array $modalStack = [];

    protected $listeners = [
        'openModal' => 'openModal',
        'closeModal' => 'closeModal',
        'closeAllModals' => 'closeAllModals',
    ];

    public function openModal(array $data): void
    {

        $component = $data['component'] ?? '';
        $params = $data['params'] ?? [];

        if (! class_exists($component)) {
            \Log::error('Modal component does not exist:', ['component' => $component]);
            return;
        }

        $modalId = Str::uuid()->toString();

        $params['modalId'] = $modalId;

        $this->modalStack[] = [
            'id' => $modalId,
            'component' => $component,
            'params' => $params, // This now includes modalId
            'timestamp' => now()->timestamp,
            'depth' => count($this->modalStack),
        ];
    }

    public function closeModal($payload = null): void
    {
        if (empty($this->modalStack)) {
            return;
        }

        $modalId = is_array($payload) ? ($payload['modalId'] ?? null) : $payload;

        if ($modalId === null) {
            array_pop($this->modalStack);

            return;
        }

        $this->modalStack = array_values(array_filter(
            $this->modalStack,
            fn ($modal) => $modal['id'] !== $modalId
        ));
    }

    public function closeAllModals(): void
    {
        $this->modalStack = [];
    }

    protected function isValidModalComponent(string $component): bool
    {
        $allowedNamespaces = config('modal.allowed_namespaces', []);

        $isAllowed = false;
        foreach ($allowedNamespaces as $namespace) {
            if (Str::startsWith($component, $namespace)) {
                $isAllowed = true;
                break;
            }
        }

        if (! $isAllowed) {
            return false;
        }
        if (! class_exists($component)) {
            return false;
        }

        $reflection = new \ReflectionClass($component);

        return $reflection->isSubclassOf(\Repay\Modal\Livewire\Modals\BaseModal::class);
    }

    protected function sanitizeParams(array $params): array
    {
        $sanitized = [];

        foreach ($params as $key => $value) {
            if (! preg_match('/^[a-zA-Z0-9_]+$/', $key)) {
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizeParams($value);
            } elseif (is_string($value)) {
                $sanitized[$key] = htmlspecialchars(mb_substr($value, 0, 1000), ENT_QUOTES);
            } elseif (is_numeric($value) || is_bool($value) || is_null($value)) {
                $sanitized[$key] = $value;
            }
        }

        return $sanitized;
    }

    public function render()
    {
        return view('modal::livewire.modal-manager');
    }
}
