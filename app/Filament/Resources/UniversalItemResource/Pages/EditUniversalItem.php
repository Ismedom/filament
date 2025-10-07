<?php

namespace App\Filament\Resources\UniversalItemResource\Pages;

use App\Filament\Resources\UniversalItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUniversalItem extends EditRecord
{
    protected static string $resource = UniversalItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
