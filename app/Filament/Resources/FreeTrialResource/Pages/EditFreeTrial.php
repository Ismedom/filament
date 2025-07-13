<?php

namespace App\Filament\Resources\FreeTrialResource\Pages;

use App\Filament\Resources\FreeTrialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFreeTrial extends EditRecord
{
    protected static string $resource = FreeTrialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
