<?php

namespace App\Filament\Widgets;

use App\Models\Ticket;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Forms;
use Filament\Notifications\Notification;

class PendingApprovalsWidget extends BaseWidget
{
    protected static ?string $heading = 'Pending Payment Approvals';
    protected static ?int $sort = 1;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Ticket::query()
                    ->where('payment_status', 'pending')
                    ->whereHas('event', function ($query) {
                        $user = auth()->user();
                        if (!$user?->isSuperAdmin()) {
                            $query->where('organization_id', $user?->organization_id);
                        }
                    })
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Registered')
                    ->dateTime('M d, H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client')
                    ->searchable(),

                Tables\Columns\TextColumn::make('event.name')
                    ->label('Event')
                    ->limit(30),

                Tables\Columns\TextColumn::make('tier.tier_name')
                    ->label('Tier')
                    ->badge(),

                Tables\Columns\TextColumn::make('amount')
                    ->money(config('constants.currency.code')) // ✅ Use config directly
                    ->label('Amount'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->form([
                        Forms\Components\TextInput::make('payment_reference')
                            ->label('Payment Reference')
                            ->required()
                            ->placeholder('e.g., ECOCASH-ABC123'),
                        
                        Forms\Components\Select::make('payment_method')
                            ->label('Payment Method')
                            ->options(config('constants.payment_methods')) // ✅ Use config directly
                            ->required()
                            ->default('ecocash'),
                    ])
                    ->action(function (Ticket $record, array $data) {
                        $record->update([
                            'payment_status' => 'completed',
                            'payment_date' => now(),
                            'payment_reference' => $data['payment_reference'],
                            'payment_method' => $data['payment_method'],
                            'status' => 'active',
                        ]);

                        // Increment tier sold count
                        $record->tier->increment('quantity_sold');

                        Notification::make()
                            ->title('Payment Approved')
                            ->body("Ticket {$record->ticket_number} has been activated")
                            ->success()
                            ->send();
                    }),
            ])
            ->emptyStateHeading('No pending approvals')
            ->emptyStateDescription('All tickets have been processed')
            ->emptyStateIcon('heroicon-o-check-badge');
    }

    public static function canView(): bool
    {
        return auth()->user()?->hasPermissionTo('approve_payment') ?? false;
    }
}