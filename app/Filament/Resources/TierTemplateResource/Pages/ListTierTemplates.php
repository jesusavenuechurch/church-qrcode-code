<?php

namespace App\Filament\Resources\TierTemplateResource\Pages;

use App\Filament\Resources\TierTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTierTemplates extends ListRecords
{
    protected static string $resource = TierTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
