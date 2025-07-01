<x-filament-panels::page>
    <div class="space-y-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Job</h2>

        <form wire:submit.prevent="create" class="space-y-6">
            {{ $this->form }}

            <x-filament::button type="submit" size="lg" color="primary">
                Create Job
            </x-filament::button>
        </form>
    </div>
</x-filament-panels::page>