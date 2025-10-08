<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\ProductTableWidget;
use App\Filament\Widgets\ArticleWidget;
use App\Filament\Widgets\TabNavigationWidget;
use Livewire\Attributes\Url;

class Testing extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.testing';
    protected static ?string $slug = 'products-table';

    #[Url(as: 'tab')]
    public string $activeTab = 'products';

    protected $listeners = ['tabChanged' => 'changeTab'];

    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function getHeaderWidgets(): array
    {
        return [
            TabNavigationWidget::class,
            ...match ($this->activeTab) {
                'articles' => [ArticleWidget::class],
                default => [ProductTableWidget::class],
            },
        ];
    }
}