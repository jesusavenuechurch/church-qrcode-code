<?php

namespace App\Filament\Resources;

use App\Models\Ticket;
use App\Models\OrganizationPaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Filament\Resources\TicketResource\Pages;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\WhatsAppController;
use App\Jobs\SendTicketApprovedEmail;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Alignment;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;
    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationGroup = 'Events';
    protected static ?string $recordTitleAttribute = 'ticket_number';

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();
        if ($user?->isSuperAdmin()) return $query;
        return $query->whereHas('event', fn($q) => $q->where('organization_id', $user->organization_id));
    }

    /* ------------------------------------------------------------
     | Table: The Command Center
     ------------------------------------------------------------ */
    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Access ID')
                    ->fontFamily('mono')
                    ->weight(FontWeight::Bold)
                    ->description(fn($record) => $record->tier->tier_name)
                    ->searchable(),

                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Guest')
                    ->description(fn($record) => $record->client->phone)
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money(config('constants.currency.code'))
                    ->weight(FontWeight::Black)
                    ->color('success')
                    ->alignment(Alignment::Right),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Access')
                    ->icons(['heroicon-m-ticket' => 'active', 'heroicon-m-check-badge' => 'checked_in'])
                    ->colors(['info' => 'active', 'success' => 'checked_in', 'danger' => 'refunded']),
            ])
            ->actions([
                // STANDALONE APPROVE BUTTON (Your Original Logic)
                Tables\Actions\Action::make('approve_payment')
                    ->label('Approve')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->button()
                    ->visible(fn ($record) =>
                        ($record->payment_status === 'pending' || $record->payment_status === 'partial')
                        && auth()->user()?->hasPermissionTo('approve_payment')
                        && $record->hasPendingPayments()
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Approve Payment')
                    ->modalDescription('Review and approve the pending payment')
                    ->form(fn($record) => static::getOriginalApproveForm($record))
                    ->action(fn(Ticket $record, array $data) => static::handleOriginalApproval($record, $data)),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->slideOver(),
                    Tables\Actions\EditAction::make()->slideOver(),
                    
                    Tables\Actions\Action::make('resend_whatsapp')
                        ->label('Resend WhatsApp')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->visible(fn ($record) => $record->payment_status === 'completed' && $record->has_whatsapp)
                        ->action(fn ($record) => WhatsAppController::deliverTicket($record)),

                    Tables\Actions\Action::make('preview_pass')
                        ->label('Live Ticket')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn ($record) => route('ticket.download', $record->qr_code))
                        ->openUrlInNewTab(),
                ])->icon('heroicon-m-ellipsis-vertical')->button()->label('More'),
            ]);
    }

    /* ------------------------------------------------------------
     | Re-integrated Original Logic Methods
     ------------------------------------------------------------ */

    protected static function getOriginalApproveForm($record): array
    {
        $pendingPayment = $record->payments()->pending()->latest()->first();
        if (!$pendingPayment) return [];

        $orgPaymentMethods = OrganizationPaymentMethod::where('organization_id', $record->event->organization_id)
            ->where('is_active', true)
            ->orderBy('display_order')->get();
        
        $paymentOptions = $orgPaymentMethods->mapWithKeys(function ($method) {
            $config = config('constants.payment_methods.' . $method->payment_method, []);
            return [$method->payment_method => is_array($config) ? $config['label'] : $config];
        })->toArray();
        
        $currencySymbol = config('constants.currency.symbol', 'M');

        return [
            Forms\Components\Section::make('Current Payment Status')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Placeholder::make('amount_paid')
                            ->label('Already Paid')
                            ->content($currencySymbol . ' ' . number_format($record->amount_paid, 2)),
                        Forms\Components\Placeholder::make('this_payment')
                            ->label('This Payment')
                            ->content($currencySymbol . ' ' . number_format($pendingPayment->amount, 2)),
                        Forms\Components\Placeholder::make('remaining')
                            ->label('After This Payment')
                            ->content($currencySymbol . ' ' . number_format($record->remaining_amount - $pendingPayment->amount, 2)),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Placeholder::make('payment_type')
                            ->label('Payment Type')->content(ucfirst($pendingPayment->payment_type)),
                        Forms\Components\Placeholder::make('payment_date')
                            ->label('Date')->content($pendingPayment->payment_date?->format('M j, Y @ g:i A')),
                        Forms\Components\Placeholder::make('current_method')
                            ->label('Stated Method')->content($pendingPayment->payment_method_label ?? 'Not specified'),
                        Forms\Components\Placeholder::make('current_reference')
                            ->label('Stated Reference')->content($pendingPayment->payment_reference ?: 'Not provided'),
                    ]),
                ])->columnSpanFull(),
            
            Forms\Components\Select::make('payment_method')
                ->label('Confirm Payment Method')->options($paymentOptions)
                ->default($pendingPayment->payment_method)->required(),
            
            Forms\Components\TextInput::make('payment_reference')
                ->label('Payment Reference')->default($pendingPayment->payment_reference)->required(),
        ];
    }

    protected static function handleOriginalApproval(Ticket $record, array $data): void
    {
        $pendingPayment = $record->payments()->pending()->latest()->first();
        if (!$pendingPayment) return;

        DB::beginTransaction();
        try {
            $pendingPayment->update([
                'status' => 'approved',
                'payment_method' => $data['payment_method'],
                'payment_reference' => $data['payment_reference'],
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            $record->updatePaymentStatus();

            if ($record->payments()->approved()->count() === 1) {
                $record->tier->increment('quantity_sold');
            }

            if ($record->remaining_amount <= 0 && $record->payment_status === 'completed') {
                if ($record->client->email) dispatch(new SendTicketApprovedEmail($record->id))->afterResponse();
                if ($record->has_whatsapp && $record->client->phone) {
                    dispatch(fn() => WhatsAppController::deliverTicket($record))->afterResponse();
                }
            }

            Notification::make()->title('Payment Approved')->success()->send();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()->title('Approval Failed')->danger()->send();
        }
    }

    /* ------------------------------------------------------------
     | Standard Resource Boilerplate
     ------------------------------------------------------------ */

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Ticket Details')->schema([
                Forms\Components\Select::make('event_id')->relationship('event', 'name')->required()->live(),
                Forms\Components\Select::make('client_id')->relationship('client', 'full_name')->required()->searchable(),
                Forms\Components\Select::make('event_tier_id')->relationship('tier', 'tier_name', 
                    fn($q, $get) => $get('event_id') ? $q->where('event_id', $get('event_id')) : $q
                )->required(),
            ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}