<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use App\Models\PymedHeroSection;
use App\Models\PymedJob;
use Illuminate\Support\Str;

class Career extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string $view = 'filament.pages.pymed-career';
    protected static ?string $title = 'Career Management';

    public ?array $heroSectionData = [];
    public $heroSection = null;

    public function mount(): void
    {
        $this->loadData();
        $this->loadHeroSectionForm();
    }

    protected function getForms(): array
    {
        return [
            'heroSectionForm' => $this->makeForm()
                ->schema($this->getHeroSectionFormSchema())
                ->statePath('heroSectionData'),
        ];
    }

    public function getHeroSectionFormSchema(): array
    {
        return [
            Forms\Components\Section::make('Hero Section')
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\FileUpload::make('image')
                        ->image()
                        ->directory('hero-images')
                        ->required(),
                    Forms\Components\FileUpload::make('bg_image')
                        ->image()
                        ->directory('hero-bg-images')
                        ->required(),
                    Forms\Components\Textarea::make('description')
                        ->required()
                        ->maxLength(1000),
                    Forms\Components\TextInput::make('url')
                        ->url()
                        ->required()
                        ->maxLength(255),
                ]),
        ];
    }

    public function updateHeroSection()
    {
        $data = $this->heroSectionForm->getState();

        if ($this->heroSection) {
            $this->heroSection->update($data);
            $message = 'Hero section updated.';
        } else {
            PymedHeroSection::create($data);
            $message = 'Hero section created.';
        }

        Notification::make()
            ->title($message)
            ->success()
            ->send();

        $this->loadData();
        $this->loadHeroSectionForm();
    }

    protected function loadHeroSectionForm(): void
    {
        if ($this->heroSection) {
            $this->heroSectionData = $this->heroSection->toArray();
            $this->heroSectionForm->fill($this->heroSectionData);
        } else {
            $this->heroSectionData = [];
            $this->heroSectionForm->fill();
        }
    }

    protected function loadData(): void
    {
        $this->heroSection = PymedHeroSection::first();
    }

    protected function getTableQuery()
    {
        return PymedJob::query()->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('title')->label('Title')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('slug')->label('Slug')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('sort_description')
                ->label('Description')
                ->limit(100)
                ->wrap(),
            Tables\Columns\TextColumn::make('published_at')
                ->label('Published At')
                ->dateTime('M d, Y H:i')
                ->sortable()
                ->toggleable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make()
                ->requiresConfirmation()
                ->after(fn () => Notification::make()->title('Job deleted')->success()->send()),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Tables\Actions\CreateAction::make()
                ->label('Create Job')
                ->url(route('filament.admin.pages.create-job')),
        ];
    }
}