<div class="mb-4">
    <div class="flex border-b border-gray-200 dark:border-gray-700">
        @foreach(['products' => 'Products', 'articles' => 'Articles'] as $key => $label)
            <button
                wire:click="$dispatch('tabChanged', { tab: '{{ $key }}' })"
                @class([
                    'px-4 py-2 text-sm font-medium border-b-2',
                    'border-primary-500 text-primary-600 bg-primary-500 dark:text-primary-400 text-gray-100' => $activeTab === $key,
                    'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' => $activeTab !== $key,
                ])
            >
                {{ $label }}
            </button>
        @endforeach
    </div>
</div>