<?php

namespace App\Filament\Resources\OrganizationPaymentMethodResource\Pages;

use App\Filament\Resources\OrganizationPaymentMethodResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOrganizationPaymentMethods extends ListRecords
{
    protected static string $resource = OrganizationPaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Payment Method'),
        ];
    }

    public function getEmptyStateHeading(): ?string
    {
        return 'No payment methods configured';
    }

    public function getEmptyStateDescription(): ?string
    {
        return 'Add payment methods to accept payments for your events. You can add M-Pesa, EcoCash, bank transfers, and more.';
    }

    public function getEmptyStateIcon(): ?string
    {
        return 'heroicon-o-credit-card';
    }

    public function getEmptyStateActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Add Your First Payment Method')
                ->icon('heroicon-o-plus'),
        ];
    }
}