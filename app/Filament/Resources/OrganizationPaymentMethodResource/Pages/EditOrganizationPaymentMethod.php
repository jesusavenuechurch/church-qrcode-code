<?php

namespace App\Filament\Resources\OrganizationPaymentMethodResource\Pages;

use App\Filament\Resources\OrganizationPaymentMethodResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrganizationPaymentMethod extends EditRecord
{
    protected static string $resource = OrganizationPaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
