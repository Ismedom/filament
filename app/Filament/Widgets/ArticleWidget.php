<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ArticleResource;
use App\Models\Article;
use App\Models\Title;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ArticleWidget extends BaseWidget
{
    protected static bool $isDiscovered = false;
    protected int|string|array $columnSpan = 'full';

    public $record; // per-row, for table actions
    public $title;  // holds the singleton Title instance

    public function table(Table $table): Table
    {
        return $table
            ->query(Article::query())
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('author.name')->sortable(),
                Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->url(fn (Article $record): string => ArticleResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-o-pencil'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('create')
                    ->label('New Article')
                    ->url(ArticleResource::getUrl('create'))
                    ->icon('heroicon-o-plus'),

                // Singleton Title action
                Tables\Actions\Action::make('createOrUpdateTitle')
                    ->label('Article Title')
                    ->button()
                    ->modalHeading('Article Title')
                    ->modalWidth('lg')
                    ->form([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->default(function () {
                                // Force fresh query
                                return Title::first()->title ?? '';
                            }),
                        Toggle::make('is_active')
                            ->label('Status')
                            ->default(function () {
                                // Force fresh query
                                return Title::first()->is_active ?? true;
                            }),
                    ])
                    ->action(function ($data) {
                        dd($data);
                        $title = Title::first() ?? new Title();
                        $title->title = $data['title'] ?? '';
                        $title->is_active = $data['is_active'] ?? false;
                        $title->save();
                    }),
            ]);
    }
}
