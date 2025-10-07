<?php
namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use App\Filament\Resources\ProductResource;
use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\MediaResource;
use App\Models\Product;
use App\Models\Article;
use App\Models\User;
use App\Models\Category;
use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;

class UniversalItemPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-plus-circle';
    protected static ?string $navigationLabel = 'Universal Items';
    protected static ?string $title = 'Create New Item';
    protected static string $view = 'filament.pages.universal-item-page';
    protected static bool $shouldRegisterNavigation = true;

    public string $activeTab = 'product';

    public function mount(): void
    {
        $this->activeTab = request()->query('tab', 'product');
    }

    public function getTabs(): array
    {
        $baseUrl = url()->current();
        
        return [
            'product' => [
                'label' => 'Product',
                'resource' => ProductResource::class,
                'model' => Product::class,
                'url' => $baseUrl . '?tab=product',
            ],
            'article' => [
                'label' => 'Article', 
                'resource' => ArticleResource::class,
                'model' => Article::class,
                'url' => $baseUrl . '?tab=article',
            ],
            'user' => [
                'label' => 'User',
                'resource' => UserResource::class, 
                'model' => User::class,
                'url' => $baseUrl . '?tab=user',
            ],
            'category' => [
                'label' => 'Category',
                'resource' => CategoryResource::class,
                'model' => Category::class,
                'url' => $baseUrl . '?tab=category',
            ],
            'media' => [
                'label' => 'Media',
                'resource' => MediaResource::class,
                'model' => Media::class,
                'url' => $baseUrl . '?tab=media',
            ],
        ];
    }

    public function table(Table $table): Table
    {
        $tabs = $this->getTabs();
        $activeTabData = $tabs[$this->activeTab] ?? $tabs['product'];
        $resourceClass = $activeTabData['resource'];
        
        try {
            $resource = app($resourceClass);
            $resourceTable = $resource->table($table);
            $columns = $resourceTable->getColumns();
            
            return $table
                ->query($activeTabData['model']::query())
                ->columns($columns)
                ->actions([
                    Action::make('edit')
                        ->url(fn ($record) => $this->getRouteUrl($resourceClass, 'edit', $record))
                        ->icon('heroicon-o-pencil'),
                    Action::make('view')
                        ->url(fn ($record) => $this->getRouteUrl($resourceClass, 'view', $record))
                        ->icon('heroicon-o-eye')
                        ->visible(fn () => $this->hasViewPage($resourceClass)),
                ])
                ->headerActions([
                    Action::make('create')
                        ->label('Create ' . $activeTabData['label'])
                        ->url($resourceClass::getUrl('create'))
                        ->icon('heroicon-o-plus'),
                ]);
        } catch (\Exception $e) {
            return $table
                ->query($activeTabData['model']::query())
                ->columns([
                    TextColumn::make('name')
                        ->searchable()
                        ->sortable()
                        ->default(fn($record) => $record->name ?? $record->title ?? $record->email ?? 'Unnamed'),
                    TextColumn::make('created_at')
                        ->dateTime()
                        ->sortable()
                        ->label('Created'),
                ])
                ->actions([
                    Action::make('edit')
                        ->url(fn ($record) => $this->getRouteUrl($resourceClass, 'edit', $record))
                        ->icon('heroicon-o-pencil'),
                    Action::make('view')
                        ->url(fn ($record) => $this->getRouteUrl($resourceClass, 'view', $record))
                        ->icon('heroicon-o-eye')
                        ->visible(fn () => $this->hasViewPage($resourceClass)),
                ])
                ->headerActions([
                    Action::make('create')
                        ->label('Create ' . $activeTabData['label'])
                        ->url($resourceClass::getUrl('create'))
                        ->icon('heroicon-o-plus'),
                ]);
        }
    }

    protected function getTableQuery(): Builder
    {
        $tabs = $this->getTabs();
        $activeTabData = $tabs[$this->activeTab] ?? $tabs['product'];
        return $activeTabData['model']::query();
    }

    protected function hasViewPage($resourceClass): bool
    {
        try {
            return $resourceClass::hasPage('view');
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getRouteUrl($resourceClass, $action, $record)
    {
        try {
            return $resourceClass::getUrl($action, ['record' => $record]);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getTitle(): string
    {
        $tabs = $this->getTabs();
        $activeTabData = $tabs[$this->activeTab] ?? $tabs['product'];
        return $activeTabData['label'] . ' Management';
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->redirect($this->getUrl() . '?tab=' . $tab);
    }
}