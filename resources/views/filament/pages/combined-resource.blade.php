<x-filament-panels::page>
    <div class="mb-6">
        <div class="flex space-x-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-lg">
            <button 
                wire:navigate 
                href="{{ url()->current() }}?tab=users"
                class="flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'users' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                Users
            </button>

            <button 
                wire:navigate 
                href="{{ url()->current() }}?tab=freeTrials"
                class="flex-1 px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $activeTab === 'freeTrials' ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                Free Trials
            </button>
        </div>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
