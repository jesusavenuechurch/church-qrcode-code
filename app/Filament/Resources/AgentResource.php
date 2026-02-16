<?php

namespace App\Filament\Resources;

use App\Models\Agent;
use Filament\Forms;
use Filament\Tables;
use Filament\Infolists;
use Filament\Resources\Resource;
use App\Filament\Resources\AgentResource\Pages;
use App\Models\User;
use App\Mail\AgentAccountInitialized;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;

class AgentResource extends Resource
{
    protected static ?string $model = Agent::class;
    protected static ?string $navigationIcon = 'heroicon-o-finger-print';
    protected static ?string $navigationGroup = 'System';

        // Super admin only
    public static function canViewAny(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function infolist(Infolists\Infolist $infolist): Infolists\Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Agent Intelligence Brief')
                    ->schema([
                        // IDENTITY FIRST
                        Infolists\Components\Grid::make(3)->schema([
                            Infolists\Components\TextEntry::make('name')
                                ->weight('black')
                                ->extraAttributes(['class' => 'uppercase']),
                            Infolists\Components\TextEntry::make('city_district')
                                ->label('Territory'),
                            Infolists\Components\TextEntry::make('phone')
                                ->label('WhatsApp')
                                ->copyable(),
                        ]),

                        // ACCESS DETAILS
                        Infolists\Components\Grid::make(2)->schema([
                            Infolists\Components\TextEntry::make('referral_token')
                                ->label('Deployment Token')
                                ->fontFamily('mono')
                                ->copyable(),
                            Infolists\Components\TextEntry::make('registration_url')
                                ->label('Onboarding Link')
                                ->icon('heroicon-m-link')
                                ->copyable(),
                        ]),
                        
                        // MOTIVATION
                        Infolists\Components\TextEntry::make('motivation')
                            ->label('Strategy & Motivation')
                            ->columnSpanFull()
                            ->extraAttributes([
                                'class' => 'p-8 bg-gray-50 dark:bg-white/5 rounded-[2.5rem] border border-gray-100 dark:border-white/10 font-medium italic text-gray-700 dark:text-gray-200'
                            ]),
                    ])
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                // 1. NAME FIRST (As it should be)
                Tables\Columns\TextColumn::make('name')
                    ->weight('black')
                    ->description(fn (Agent $record) => $record->city_district ?? 'NO DISTRICT')
                    ->searchable()
                    ->sortable(),

                // 2. STATUS SECOND
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'approved' => 'success',
                        'pending' => 'warning',
                        'rejected' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => strtoupper($state)),

                Tables\Columns\TextColumn::make('certification.passed')
                    ->label('Certified')
                    ->badge()
                    ->getStateUsing(function (Agent $record): string {
                        $cert = $record->certification;
                        if (!$cert || $cert->attempts === 0) return 'pending';
                        return $cert->passed ? 'passed' : 'failed';
                    })
                    ->formatStateUsing(fn ($state) => match($state) {
                        'passed'  => '✓ Certified',
                        'failed'  => '✗ Failed',
                        default   => '— Not Started',
                    })
                    ->color(fn ($state) => match($state) {
                        'passed'  => 'success',
                        'failed'  => 'danger',
                        default   => 'gray',
                    })
                    ->tooltip(function (Agent $record): string {
                        $cert = $record->certification;
                        if (!$cert || $cert->attempts === 0) return 'Has not attempted certification';
                        return "Score: {$cert->score}/{$cert->total_questions} ({$cert->score_percentage}%) · Attempts: {$cert->attempts}";
                    }),

                Tables\Columns\TextColumn::make('earnings_sum_amount')
                    ->label('Total Life-time')
                    ->sum('earnings', 'amount')
                    ->money('LSL'),

                Tables\Columns\TextColumn::make('pending_payouts')
                    ->label('Owed')
                    ->getStateUsing(function (Agent $record) {
                        $owed = $record->earnings()->where('status', 'approved')->sum('amount');
                        return $owed > 0 ? "M " . number_format($owed, 2) : '—';
                    })
                    ->color('warning')
                    ->weight('bold'),

                // 3. TOKEN (WITH ONE-CLICK COPY)
                Tables\Columns\TextColumn::make('referral_token')
                    ->label('Token')
                    ->fontFamily('mono')
                    ->copyable()
                    ->icon('heroicon-m-clipboard')
                    ->toggleable(),
            ])
        ->actions([
            // QUICK AUTHORIZE
            Tables\Actions\Action::make('approve_quick')
                ->label('Authorize')
                ->icon('heroicon-m-check-badge')
                ->color('success')
                ->requiresConfirmation()
                ->hidden(fn (Agent $record) => $record->status === 'approved')
                ->action(function (Agent $record) {
                    DB::transaction(function () use ($record) {
                        // 1. Create or retrieve the User
                        $user = User::firstOrCreate(
                            ['email' => $record->email],
                            [
                                'name' => $record->name,
                                'password' => Hash::make(Str::random(32)),
                                'email_verified_at' => now(),
                            ]
                        );
                        $user->assignRole('sales_agent');

                        // 2. Update Agent Status
                        $record->update([
                            'status' => 'approved',
                            'is_active' => true,
                            'approved_at' => now(),
                            'approved_by' => auth()->id()
                        ]);

                        // 3. Generate Secure Initialization Link
                        $token = Password::createToken($user);
                        $url = url(route('password.reset', [
                            'token' => $token,
                            'email' => $user->email,
                        ]));

                        // 4. Fire Protocol Email
                        Mail::to($user->email)->send(new AgentAccountInitialized($user, $url));
                    });

                    \Filament\Notifications\Notification::make()
                        ->title('Agent Authorized')
                        ->body('User account created and initialization email sent.')
                        ->success()
                        ->send();
                }),

            // REVIEW SLIDE-OVER
            Tables\Actions\ViewAction::make()
                ->label('Review')
                ->slideOver()
                ->extraModalFooterActions([
                    Tables\Actions\Action::make('approve_view')
                        ->label('Authorize Agent')
                        ->icon('heroicon-m-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->hidden(fn (Agent $record) => $record->status === 'approved')
                        ->action(function (Agent $record) {
                            DB::transaction(function () use ($record) {
                                $user = User::firstOrCreate(
                                    ['email' => $record->email],
                                    ['name' => $record->name, 'password' => Hash::make(Str::random(32)), 'email_verified_at' => now()]
                                );

                                $record->update(['status' => 'approved', 'is_active' => true, 'approved_at' => now(), 'approved_by' => auth()->id()]);

                                $token = Password::createToken($user);
                                $url = url(route('password.reset', ['token' => $token, 'email' => $user->email]));

                                Mail::to($user->email)->send(new AgentAccountInitialized($user, $url));
                            });
                        }),
                ]),

                Tables\Actions\Action::make('pay_agent')
                ->label('Record Payout')
                ->icon('heroicon-m-currency-dollar')
                ->color('success')
                ->modalHeading(fn (Agent $record) => "Payout for {$record->name}")
                // Only show if they are actually owed money
                ->visible(fn (Agent $record) => $record->earnings()->where('status', 'approved')->sum('amount') > 0)
                ->form([
                    Forms\Components\Placeholder::make('amount_owed')
                        ->label('Total Balance Owed')
                        ->content(fn (Agent $record) => 'M ' . number_format($record->earnings()->where('status', 'approved')->sum('amount'), 2)),
                    
                    Forms\Components\Select::make('payment_method')
                        ->options([
                            'cash' => 'Cash',
                            'm_pesa' => 'M-Pesa',
                            'ecocash' => 'EcoCash',
                            'bank_transfer' => 'Bank Transfer'
                        ])->required(),
                        
                    Forms\Components\TextInput::make('payment_reference')
                        ->label('Reference (e.g. M-Pesa ID)')
                        ->required(),
                ])
                ->action(function (Agent $record, array $data) {
                    // Find all approved earnings and mark them as paid in one go
                    $approvedEarnings = $record->earnings()->where('status', 'approved')->get();
                    
                    foreach ($approvedEarnings as $earning) {
                        $earning->markAsPaid($data['payment_method'], $data['payment_reference']);
                    }

                                    // 2. Fire the Receipt Email
                    \Illuminate\Support\Facades\Mail::to($record->email)->send(
                        new \App\Mail\AgentPayoutReceipt(
                            $record, 
                            $totalPaid, 
                            $data['payment_method'], 
                            $data['payment_reference'],
                            $approvedEarnings
                        )
                    );

                    Notification::make()
                        ->title('Payout Successful')
                        ->body('All pending commissions have been moved to Paid status.')
                        ->success()
                        ->send();
                })
        ]);
    }

    public static function getRelations(): array
    {
        return [
            \App\Filament\Resources\AgentResource\RelationManagers\EarningsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAgents::route('/'),
        ];
    }
}