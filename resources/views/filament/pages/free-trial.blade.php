<x-filament::page>
    <div class="space-y-8">
        <div class="p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">Free Trials</h2>
            {{ $freeTrialsTable }}
        </div>

        <div class="p-6 rounded-lg shadow">
            <h2 class="text-xl font-bold mb-4">Users</h2>
            {{ $usersTable }}
        </div>
    </div>
</x-filament::page>