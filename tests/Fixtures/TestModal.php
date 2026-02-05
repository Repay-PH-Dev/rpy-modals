<?php

namespace Repay\Modal\Tests\Fixtures;

use Repay\Modal\Livewire\Modals\BaseModal;

class TestModal extends BaseModal
{
    public $testData;

    protected function setupModal(array $params): void
    {
        $this->title = $params['title'] ?? 'Test Modal';
        $this->testData = $params['testData'] ?? null;
    }

    public function render()
    {
        return view('modal::test-modal');
    }
}
