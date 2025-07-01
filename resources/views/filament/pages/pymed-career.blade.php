<x-filament-panels::page>
    <div class="space-y-10">

        {{-- Hero Section Form --}}
        <x-filament::section>
            <form wire:submit.prevent="updateHeroSection">
                {{ $this->heroSectionForm }}
                <x-filament::button class="mt-6" type="submit">
                    {{ $heroSection ? 'Update Hero Section' : 'Create Hero Section' }}
                </x-filament::button>
            </form>
        </x-filament::section>

        {{-- Jobs Table --}}
        <x-filament::section>
            <h2 class="text-xl font-semibold mb-4">Job Listings</h2>
            {{ $this->table }}
        </x-filament::section>

    </div>
</x-filament-panels::page>
