<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use App\Models\Event; // Added this import

class MobileAppDownload extends Widget
{
    protected static string $view = 'filament.widgets.mobile-app-download';
    protected int | string | array $columnSpan = 'full';
    protected static ?int $sort = -1; // Show at top

        /**
     * Logic to show/hide the entire widget
     */
    public static function canView(): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        // Always show for Super Admins
        if ($user->isSuperAdmin()) {
            return true;
        }

        // Only show if the organization has at least one event created
        return Event::where('organization_id', $user->organization_id)->exists();
    }

    // public static function canView(): bool
    // {
    //     // Only show to non-super admins (org users)
    //     return !auth()->user()?->isSuperAdmin();
    // }
}