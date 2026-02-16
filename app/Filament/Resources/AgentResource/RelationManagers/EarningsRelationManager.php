<?php

namespace App\Filament\Resources\AgentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\AgentEarning;
use Filament\Notifications\Notification;

class EarningsRelationManager extends RelationManager
{
    protected static string $relationship = 'earnings';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('amount')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => strtoupper($state)),
                Tables\Columns\TextColumn::make('organization.name')
                    ->label('Source'),
                Tables\Columns\TextColumn::make('amount')
                    ->money('LSL')
                    ->summarize(Tables\Columns\Summarizers\Sum::make()->label('Total Earned')),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'approved' => 'warning',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'approved' => 'Awaiting Payout',
                        'paid' => 'Already Paid',
                    ])
            ])
            ->actions([
                // THE "MARK AS PAID" ACTION
                Tables\Actions\Action::make('mark_as_paid')
                    ->label('Mark Paid')
                    ->icon('heroicon-m-banknotes')
                    ->color('success')
                    ->visible(fn (AgentEarning $record) => $record->status === 'approved')
                    ->form([
                        \Filament\Forms\Components\Select::make('payment_method')
                            ->options([
                                'm_pesa' => 'M-Pesa',
                                'ecocash' => 'EcoCash',
                                'bank_transfer' => 'Bank Transfer'
                            ])->required(),
                        \Filament\Forms\Components\TextInput::make('payment_reference')
                            ->label('M-Pesa/Bank Ref')
                            ->required(),
                    ])
                    ->action(function (AgentEarning $record, array $data) {
                        $record->markAsPaid($data['payment_method'], $data['payment_reference']);
                        
                        Notification::make()
                            ->title('Payout Recorded')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}