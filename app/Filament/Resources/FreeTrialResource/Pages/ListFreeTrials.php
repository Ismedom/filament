<?php

namespace App\Filament\Resources\FreeTrialResource\Pages;

use App\Filament\Resources\FreeTrialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFreeTrials extends ListRecords
{
    protected static string $resource = FreeTrialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
