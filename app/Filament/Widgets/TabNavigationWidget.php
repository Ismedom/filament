<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Livewire\Attributes\On;

class TabNavigationWidget extends Widget
{
    protected static string $view = 'filament.widgets.tab-navigation-widget';
    
    public string $activeTab;

    public function mount()
    {
        $this->activeTab = app(\App\Filament\Pages\Testing::class)->activeTab;
    }

    #[On('tabChanged')]
    public function updateActiveTab($tab)
    {
        $this->activeTab = $tab;
    }
}