<?php

namespace App\Filament\Resources;

use App\Models\Ticket;
use App\Models\Event;
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
            // Section 1: Read-Only Ticket Context (Displays names instead of IDs)
            Forms\Components\Section::make('Ticket Context')
                ->description('Core assignment for this guest pass.')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Placeholder::make('event_name')
                            ->label('Event')
                            ->content(fn ($record) => $record?->event?->name ?? 'N/A'),

                        Forms\Components\Placeholder::make('tier_name')
                            ->label('Access Tier')
                            ->content(fn ($record) => $record?->tier?->tier_name ?? 'N/A'),

                        Forms\Components\Placeholder::make('total_amount')
                            ->label('Total Cost')
                            ->content(fn ($record) => config('constants.currency.symbol') . ' ' . number_format($record?->amount, 2)),
                    ]),
                ]),

            // Section 2: Status Management (Interactive Toggles)
            Forms\Components\Section::make('Payment & Access Management')
                ->description('Manually update the current standing of this ticket.')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\ToggleButtons::make('payment_status')
                            ->label('Payment Status')
                            ->options(config('constants.payment_statuses'))
                            ->required()
                            ->inline()
                            ->live()
                            ->colors([
                                'pending' => 'warning',
                                'partial' => 'info',
                                'completed' => 'success',
                                'failed' => 'danger',
                                'refunded' => 'gray',
                            ])
                            ->icons([
                                'pending' => 'heroicon-o-clock',
                                'partial' => 'heroicon-o-adjustments-horizontal',
                                'completed' => 'heroicon-o-check-circle',
                                'failed' => 'heroicon-o-x-circle',
                                'refunded' => 'heroicon-o-arrow-uturn-left',
                            ]),

                        Forms\Components\ToggleButtons::make('status')
                            ->label('Access Status')
                            ->options([
                                'active' => 'Active',
                                'checked_in' => 'Checked In',
                                'refunded' => 'Refunded',
                                'void' => 'Void',
                            ])
                            ->required()
                            ->inline()
                            ->colors([
                                'active' => 'info',
                                'checked_in' => 'success',
                                'refunded' => 'danger',
                                'void' => 'gray',
                            ])
                            ->icons([
                                'active' => 'heroicon-o-ticket',
                                'checked_in' => 'heroicon-o-check-badge',
                            ]),
                    ]),
                ]),

            // Section 3: Guest Info (Hidden/Collapsed by default)
            Forms\Components\Section::make('Guest Information')
                ->schema([
                    Forms\Components\Select::make('client_id')
                        ->relationship('client', 'full_name')
                        ->disabled()
                        ->label('Guest Assigned'),
                ])->collapsed(),
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
            // GUEST & INFO: This is the only column that will show on mobile
            Tables\Columns\TextColumn::make('client.full_name')
                ->label('Guest')
                ->weight(FontWeight::Bold)
                ->searchable()
                ->description(fn ($record) => 
                    // Stacking ID and Tier using a line break or simple bullet
                    $record->ticket_number . " \n " . $record->tier->tier_name
                )
                // This ensures the description can wrap if needed
                ->wrap(),

            // ACCESS ID: Shows only on desktop
            Tables\Columns\TextColumn::make('ticket_number')
                ->label('Access ID')
                ->fontFamily('mono')
                ->visibleFrom('md'), 

            // STATUS: Shows only on desktop
            Tables\Columns\TextColumn::make('payment_status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'pending' => 'warning',
                    'partial' => 'info',
                    'completed' => 'success',
                    default => 'gray',
                })
                ->visibleFrom('md'), 
        ])
        ->actions([
            // APPROVE: Stays on the right, icon-only on mobile
            Tables\Actions\Action::make('approve_payment')
                ->label('Approve')
                ->icon('heroicon-m-check-badge')
                ->color('success')
                ->button()
                ->labeledFrom('md') 
                ->visible(fn ($record) =>
                    ($record->payment_status === 'pending' || $record->payment_status === 'partial')
                    && auth()->user()?->can('approve_payment')
                    && $record->hasPendingPayments()
                )
                ->form(fn($record) => static::getApproveForm($record))
                ->action(fn(Ticket $record, array $data) => static::handleApproval($record, $data)),

            Tables\Actions\ActionGroup::make([
                Tables\Actions\EditAction::make()->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->icon('heroicon-m-ellipsis-vertical')
            ->color('gray')
            ->button(),
        ], position: Tables\Enums\ActionsPosition::AfterColumns);
}

    /* ------------------------------------------------------------
     | Logic Handlers
     ------------------------------------------------------------ */

    protected static function getApproveForm($record): array
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
            Forms\Components\Section::make('Payment Validation')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Placeholder::make('this_payment')
                            ->label('Amount to Approve')
                            ->content($currencySymbol . ' ' . number_format($pendingPayment->amount, 2)),
                        
                        Forms\Components\ToggleButtons::make('target_ticket_status')
                            ->label('Set Overall Status To')
                            ->options(config('constants.payment_statuses'))
                            ->default(fn() => ($record->remaining_amount - $pendingPayment->amount <= 0) ? 'completed' : 'partial')
                            ->inline()
                            ->required()
                            ->colors(['pending' => 'warning', 'partial' => 'info', 'completed' => 'success']),
                    ]),
                ]),
            
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('payment_method')
                    ->options($paymentOptions)
                    ->default($pendingPayment->payment_method)
                    ->required(),
                
                Forms\Components\TextInput::make('payment_reference')
                    ->default($pendingPayment->payment_reference)
                    ->required(),
            ]),
        ];
    }

    protected static function handleApproval(Ticket $record, array $data): void
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

            // Update ticket based on selection in popup
            $record->update(['payment_status' => $data['target_ticket_status']]);

            if ($record->payments()->approved()->count() === 1) {
                $record->tier->increment('quantity_sold');
            }

            if ($record->payment_status === 'completed') {
                $record->update(['status' => 'active']);
                if ($record->client->email) dispatch(new SendTicketApprovedEmail($record->id))->afterResponse();
                if ($record->has_whatsapp && $record->client->phone) {
                    dispatch(fn() => WhatsAppController::deliverTicket($record))->afterResponse();
                }
            }

            Notification::make()->title('Payment Approved & Status Updated')->success()->send();
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