<div>
    <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">
            {{ $title }}
        </h3>
        <p class="text-gray-500">Test modal content.</p>
        @if($testData)
        <div class="mt-4">
            <p class="text-sm text-gray-600">Test Data: {{ $testData }}</p>
        </div>
        @endif
    </div>
    <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3">
        <button type="button" wire:click="closeModal" 
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            Close
        </button>
    </div>
</div>
