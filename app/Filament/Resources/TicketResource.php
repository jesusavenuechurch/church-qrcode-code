<?php

namespace App\Filament\Resources;

use App\Models\Ticket;
use App\Models\OrganizationPaymentMethod;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
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
     | Form: Create / Edit
     ------------------------------------------------------------ */
    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Ticket Details')
                ->description('Manage guest access and event tier assignment.')
                ->schema([
                    Forms\Components\Select::make('event_id')
                        ->relationship('event', 'name')
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (Forms\Set $set) => $set('event_tier_id', null)),

                    Forms\Components\Select::make('client_id')
                        ->relationship('client', 'full_name')
                        ->required()
                        ->searchable()
                        ->preload(),

                    Forms\Components\Select::make('event_tier_id')
                        ->label('Access Tier')
                        ->relationship(
                            name: 'tier', 
                            titleAttribute: 'tier_name', 
                            // Corrected $query variable to prevent BindingResolutionException
                            modifyQueryUsing: fn (Builder $query, Forms\Get $get) => $query
                                ->when($get('event_id'), fn ($q) => $q->where('event_id', $get('event_id')))
                        )
                        ->required()
                        ->searchable()
                        ->preload(),
                        
                    Forms\Components\TextInput::make('amount')
                        ->numeric()
                        ->prefix(config('constants.currency.symbol'))
                        ->required(),
                ])->columns(2),
        ]);
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
                    ->copyable()
                    ->description(fn($record) => $record->tier->tier_name)
                    ->searchable(),

                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Guest')
                    ->description(fn($record) => $record->client->phone)
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->money(config('constants.currency.code'))
                    ->weight(FontWeight::Black)
                    ->color('primary') // Ventiq Navy
                    ->alignment(Alignment::Right),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning', // Ventiq Orange
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Access')
                    ->badge()
                    ->icons([
                        'heroicon-m-ticket' => 'active', 
                        'heroicon-m-check-badge' => 'checked_in'
                    ])
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'info',
                        'checked_in' => 'success',
                        'refunded' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->actions([
                // Approval Action
                Tables\Actions\Action::make('approve_payment')
                    ->label('Approve')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->button()
                    ->visible(fn ($record) =>
                        ($record->payment_status === 'pending' || $record->payment_status === 'partial')
                        && auth()->user()?->can('approve_payment')
                        && $record->hasPendingPayments()
                    )
                    ->form(fn($record) => static::getOriginalApproveForm($record))
                    ->action(fn(Ticket $record, array $data) => static::handleOriginalApproval($record, $data)),

                // Sharing & Options Group
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('preview_pass')
                        ->label('View Public Pass')
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->url(fn ($record) => route('ticket.download', $record->qr_code))
                        ->openUrlInNewTab(),

                    Tables\Actions\Action::make('copy_link')
                        ->label('Share Ticket')
                        ->icon('heroicon-o-share')
                        ->modalHeading('Share Access Pass')
                        ->modalWidth('md')
                        ->modalContent(fn ($record) => view('filament.modals.ticket-link', [
                            'ticket' => $record,
                            'link' => route('ticket.download', $record->qr_code),
                        ]))
                        ->modalSubmitAction(false),

                    Tables\Actions\Action::make('resend_whatsapp')
                        ->label('Resend WhatsApp')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->visible(fn ($record) => $record->payment_status === 'completed' && $record->has_whatsapp)
                        ->action(fn ($record) => WhatsAppController::deliverTicket($record)),

                    Tables\Actions\EditAction::make()->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->button()
                ->label('Options'),
            ]);
    }

    /* ------------------------------------------------------------
     | Logic Handlers
     ------------------------------------------------------------ */

    protected static function getOriginalApproveForm($record): array
    {
        $pendingPayment = $record->payments()->pending()->latest()->first();
        if (!$pendingPayment) return [];

        $orgPaymentMethods = OrganizationPaymentMethod::where('organization_id', $record->event->organization_id)
            ->where('is_active', true)
            ->get();
        
        $paymentOptions = $orgPaymentMethods->mapWithKeys(function ($method) {
            $config = config('constants.payment_methods.' . $method->payment_method, []);
            return [$method->payment_method => is_array($config) ? $config['label'] : $config];
        })->toArray();
        
        $currencySymbol = config('constants.currency.symbol', 'M');

        return [
            Forms\Components\Section::make('Confirmation Details')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Placeholder::make('this_payment')
                            ->label('To Approve')
                            ->content($currencySymbol . ' ' . number_format($pendingPayment->amount, 2)),
                        Forms\Components\Placeholder::make('remaining')
                            ->label('Balance After')
                            ->content($currencySymbol . ' ' . number_format($record->remaining_amount - $pendingPayment->amount, 2)),
                    ]),
                ]),
            
            Forms\Components\Select::make('payment_method')
                ->options($paymentOptions)
                ->default($pendingPayment->payment_method)
                ->required(),
            
            Forms\Components\TextInput::make('payment_reference')
                ->default($pendingPayment->payment_reference)
                ->required(),
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
            Notification::make()->title('Approval Error')->danger()->send();
        }
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