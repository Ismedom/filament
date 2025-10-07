<?php

namespace App\Filament\Resources\UniversalItemResource\Pages;

use App\Filament\Resources\UniversalItemResource;
use App\Models\Product;
use App\Models\Article;
use App\Models\User;
use App\Models\Category;
use App\Models\Media;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Forms\Components\Select;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ListUniversalItems extends ListRecords
{
    protected static string $resource = UniversalItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // Override the table query to combine all models
    protected function getTableQuery(): Builder
    {
        // Create a dummy Eloquent builder and modify its query to use our union
        $dummyModel = new class extends \Illuminate\Database\Eloquent\Model {
            protected $table = 'unified_items';
        };
        
        $builder = $dummyModel->newQuery();
        $builder->setQuery($this->getUnifiedQuery());
        
        return $builder;
    }

    protected function getUnifiedQuery(): \Illuminate\Database\Query\Builder
    {
        $tableConfigs = UniversalItemResource::getTableConfigs();
        $queries = [];

        foreach ($tableConfigs as $type => $config) {
            $model = new $config['model'];
            $tableName = $model->getTable();
            
            // Create a select query for each model type with unified columns
            $query = DB::table($tableName)->select([
                'id',
                $this->getTitleColumn($type) . ' as title',
                DB::raw("'{$type}' as item_type"),
                DB::raw("'{$config['label']}' as type_label"),
                'created_at',
                'updated_at'
            ]);

            $queries[] = $query;
        }

        // Union all queries
        $unionQuery = $queries[0];
        for ($i = 1; $i < count($queries); $i++) {
            $unionQuery = $unionQuery->union($queries[$i]);
        }

        return $unionQuery;
    }

    protected function getTitleColumn(string $type): string
    {
        $titleColumns = [
            'products' => 'name',
            'articles' => 'title',
            'users' => 'name',
            'categories' => 'name',
            'media' => 'title'
        ];

        return $titleColumns[$type] ?? 'id';
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item_type')
                    ->label('Type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'products' => 'success',
                        'articles' => 'info',
                        'users' => 'warning',
                        'categories' => 'primary',
                        'media' => 'secondary',
                        default => 'gray',
                    }),
                
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('type_label')
                    ->label('Category')
                    ->sortable(),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('item_type')
                    ->label('Item Type')
                    ->options([
                        'products' => 'Products',
                        'articles' => 'Articles',
                        'users' => 'Users',
                        'categories' => 'Categories',
                        'media' => 'Media',
                    ])
                    ->placeholder('All Types'),
            ])
            ->actions([
                // Custom actions based on item type
                Actions\Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn ($record) => $this->getItemUrl($record))
                    ->openUrlInNewTab(),
                
                Actions\Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn ($record) => $this->getEditUrl($record)),
            ])
            ->bulkActions([
                // Add bulk actions if needed
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([10, 25, 50, 100]);
    }

    protected function getItemUrl($record): string
    {
        // Generate appropriate URLs based on item type
        $configs = UniversalItemResource::getTableConfigs();
        $config = $configs[$record->item_type];
        
        // You might want to redirect to the specific resource or a custom view
        return '#'; // Replace with actual URL logic
    }

    protected function getEditUrl($record): string
    {
        // For editing, you might want to redirect to the original resource
        // or handle it within the universal resource
        return static::getResource()::getUrl('create') . '?edit=' . $record->id . '&type=' . $record->item_type;
    }
}