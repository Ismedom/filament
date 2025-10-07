<?php

namespace App\Filament\Resources\UniversalItemResource\Pages;

use App\Filament\Resources\UniversalItemResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateUniversalItem extends CreateRecord
{
    protected static string $resource = UniversalItemResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $tableType = $data['table_type'];
        $itemData = $data['item_data'] ?? [];
        
        $config = UniversalItemResource::getTableConfigs()[$tableType];
        $modelClass = $config['model'];
        
        // Handle password hashing for users
        if ($tableType === 'users' && isset($itemData['password'])) {
            $itemData['password'] = bcrypt($itemData['password']);
        }
        
        // Create the record in the appropriate table
        return $modelClass::create($itemData);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Item created successfully!';
    }
}