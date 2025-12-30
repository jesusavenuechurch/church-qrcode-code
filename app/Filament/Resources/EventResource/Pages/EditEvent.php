<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEvent extends EditRecord
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    /**
     * Prepare data before filling the form
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Split event_date into date and time
        if (isset($data['event_date'])) {
            try {
                $datetime = \Carbon\Carbon::parse($data['event_date']);
                $data['event_date_only'] = $datetime->format('Y-m-d');
                $data['event_time_only'] = $datetime->format('H:i');
            } catch (\Exception $e) {
                // Keep original if parsing fails
            }
        }

        // Split registration_deadline into date and time
        if (isset($data['registration_deadline'])) {
            try {
                $datetime = \Carbon\Carbon::parse($data['registration_deadline']);
                $data['registration_deadline_date'] = $datetime->format('Y-m-d');
                $data['registration_deadline_time'] = $datetime->format('H:i');
            } catch (\Exception $e) {
                // Keep original if parsing fails
            }
        }

        return $data;
    }

    /**
     * Prepare data before saving
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Combine date and time fields
        if (isset($data['event_date_only']) && isset($data['event_time_only'])) {
            $data['event_date'] = $data['event_date_only'] . ' ' . $data['event_time_only'];
        }

        if (isset($data['registration_deadline_date']) && isset($data['registration_deadline_time'])) {
            $data['registration_deadline'] = $data['registration_deadline_date'] . ' ' . $data['registration_deadline_time'];
        } elseif (isset($data['registration_deadline_date']) && !isset($data['registration_deadline_time'])) {
            $data['registration_deadline'] = null;
        }

        // Remove temporary fields
        unset($data['event_date_only']);
        unset($data['event_time_only']);
        unset($data['registration_deadline_date']);
        unset($data['registration_deadline_time']);

        return $data;
    }
}
