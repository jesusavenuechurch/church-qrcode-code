<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\OrganizationPackage;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        $user = auth()->user();
        
        // Super admins always get create button
        if ($user->isSuperAdmin()) {
            return [
                CreateAction::make()
                    ->label('Create Event')
                    ->icon('heroicon-o-plus'),
            ];
        }

        // Check if organization has any package (active or not)
        $hasAnyPackage = OrganizationPackage::where('organization_id', $user->organization_id)->exists();

        // If no package ever purchased, offer FREE TRIAL
        if (!$hasAnyPackage) {
            return [
                Action::make('start_free_trial')
                    ->label('Start Free Trial')
                    ->icon('heroicon-o-gift')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Start Your Free Trial')
                    ->modalDescription('Get started with a FREE Standard package (300 tickets, 1 event). No payment required!')
                    ->modalSubmitActionLabel('Activate Free Trial')
                    ->action(function () use ($user) {
                        // Create free trial package - ACTIVE immediately
                        OrganizationPackage::createFreeTrialPackage($user->organization_id);

                        Notification::make()
                            ->title('Free Trial Activated! 🎉')
                            ->body('You can now create your first event.')
                            ->success()
                            ->send();

                        // Refresh the page to show Create Event button
                        redirect()->to(EventResource::getUrl('index'));
                    }),
            ];
        }

        // Check if user has active package with slots
        $canCreate = static::getResource()::canCreate();

        if ($canCreate) {
            return [
                CreateAction::make()
                    ->label('Create Event')
                    ->icon('heroicon-o-plus'),
            ];
        }

        // User has packages but all exhausted/expired
        return [
            Action::make('upgrade')
                ->label('Upgrade Capacity')
                ->icon('heroicon-o-arrow-up-circle')
                ->color('primary')
                ->url(route('filament.admin.resources.package-purchases.index'))
                ->tooltip('Package exhausted. Click to upgrade.'),
        ];
    }
}