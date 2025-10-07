<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8">
                @foreach ($this->getTabs() as $key => $tab)
                    <a 
                        wire:navigate
                        href="{{ $tab['url'] }}"
                        @class([
                            'whitespace-nowrap border-b-2 py-4 px-1 text-sm font-medium',
                            'border-primary-500 text-primary-600' => $activeTab === $key,
                            'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' => $activeTab !== $key,
                        ])
                    >
                        {{ $tab['label'] }}
                    </a>
                @endforeach
            </nav>
        </div>
        
        <!-- Content -->
        <div>
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>