<?php

namespace App\Filament\Pages;

use App\Models\PymedJob;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Contracts\View\View;

class CreateJob extends Page
{
    protected static ?string $title = 'Create or Edit Job';
    protected static string $view = 'filament.pages.create-job';

    public ?array $formData = [];

    public ?PymedJob $job = null;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(?PymedJob $job = null): void
    {
        $this->job = $job;

        if ($job) {
            $this->form->fill($job->toArray());
        } else {
            $this->form->fill();
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('sort_description')
                    ->required()
                    ->rows(3),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->rows(6),
                Forms\Components\Select::make('status')
                    ->required()
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),
                Forms\Components\DateTimePicker::make('published_at')
                    ->required()
                    ->default(now()),
            ])
            ->statePath('formData');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        if ($this->job) {
            $this->job->update($data);
            $message = 'Job updated successfully!';
        } else {
            PymedJob::create($data);
            $message = 'Job created successfully!';
        }

        Notification::make()
            ->title($message)
            ->success()
            ->send();

        $this->redirect(CreateJob::getUrl());
    }

    protected function getForms(): array
    {
        return [
            'form',
        ];
    }
}


