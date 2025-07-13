<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\FreeTrial;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class CombinedResourcePage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static string $view = 'filament.pages.combined-resource';
    protected static ?string $navigationLabel = 'Users & Trials';
    protected static ?string $navigationGroup = 'Management';
    public string $activeTab = 'users';

    public function mount(Request $request): void
    {
        $this->activeTab = in_array($request->query('tab'), ['users', 'freeTrials'])
            ? $request->query('tab')
            : 'users';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns($this->getTableColumns())
            ->actions($this->getTableActions())
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label($this->activeTab === 'users' ? 'Create User' : 'Create Trial')
                    ->url($this->activeTab === 'users' 
                        ? route('filament.admin.resources.users.create')
                        : route('filament.admin.resources.free-trials.create')
                    ),
            ])
            ->emptyStateHeading('No ' . ($this->activeTab === 'users' ? 'users' : 'trials') . ' found')
            ->emptyStateDescription('Create your first ' . ($this->activeTab === 'users' ? 'user' : 'trial') . ' to get started.');
    }

    protected function getTableQuery(): Builder
    {
        if ($this->activeTab === 'users') {
            return User::query();
        }
        return FreeTrial::query();
    }

    protected function getTableColumns(): array
    {
        if ($this->activeTab === 'users') {
            return [
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ];
        }
        
        return [
            Tables\Columns\TextColumn::make('name')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('description')
                ->limit(50),
            Tables\Columns\TextColumn::make('status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'active' => 'success',
                    'expired' => 'warning',
                    'cancelled' => 'danger',
                }),
            Tables\Columns\TextColumn::make('trial_days')
                ->sortable()
                ->suffix(' days'),
            Tables\Columns\TextColumn::make('created_at')
                ->dateTime()
                ->sortable()
                ->since(),
        ];
    }

    protected function getTableActions(): array
    {
        if ($this->activeTab === 'users') {
            return [
                Tables\Actions\EditAction::make()
                    ->url(fn ($record) => route('filament.admin.resources.users.edit', $record)),
                Tables\Actions\DeleteAction::make(),
            ];
        }
        
        return [
            Tables\Actions\EditAction::make()
                ->url(fn ($record) => route('filament.admin.resources.free-trials.edit', $record)),
            Tables\Actions\DeleteAction::make(),
        ];
    }

    public function updatedActiveTab(): void
    {
        $this->resetTable();
        redirect()->to(url()->current() . '?tab=' . $this->activeTab);
    }
}