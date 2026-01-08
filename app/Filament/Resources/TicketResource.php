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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use App\Filament\Resources\TicketResource\Pages;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\WhatsAppController;
use App\Jobs\SendTicketApprovedEmail;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $navigationIcon = 'heroicon-o-ticket';
    protected static ?string $navigationLabel = 'Tickets';
    protected static ?string $navigationGroup = 'Events';

    /* ------------------------------------------------------------
     | Permissions
     ------------------------------------------------------------ */

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasPermissionTo('view_ticket') ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasPermissionTo('create_ticket') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return $user->hasPermissionTo('edit_ticket');
        }

        return $user->hasPermissionTo('edit_ticket')
            && $record->event->organization_id === $user->organization_id;
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        return $user->isSuperAdmin()
            || $record->event->organization_id === $user->organization_id;
    }

    /* ------------------------------------------------------------
     | Query Scoping
     ------------------------------------------------------------ */

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->isSuperAdmin()) {
            return $query;
        }

        if ($user?->organization_id) {
            return $query->whereHas(
                'event',
                fn ($q) => $q->where('organization_id', $user->organization_id)
            );
        }

        return $query->whereNull('id');
    }

    /* ------------------------------------------------------------
     | Form
     ------------------------------------------------------------ */

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Ticket Details')
                ->schema([
                    Forms\Components\Select::make('event_id')
                        ->relationship('event', 'name')
                        ->required()
                        ->searchable()
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('event_tier_id', null)),

                    Forms\Components\Select::make('client_id')
                        ->relationship('client', 'full_name')
                        ->required()
                        ->searchable(),

                    Forms\Components\Select::make('event_tier_id')
                        ->relationship('tier', 'tier_name', function ($query, callable $get) {
                            if ($eventId = $get('event_id')) {
                                return $query->where('event_id', $eventId);
                            }
                            return $query;
                        })
                        ->required()
                        ->helperText('Select event first'),

                    Forms\Components\TextInput::make('ticket_number')
                        ->disabled()
                        ->visibleOn('edit'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Payment')
                ->schema([
                    Forms\Components\Select::make('payment_method')
                        ->label('Payment Method')
                        ->options(function (callable $get, $record) {
                            $eventId = $get('event_id') ?? $record?->event_id;
                            
                            if (!$eventId) {
                                return ['free' => 'Free'];
                            }
                            
                            $event = \App\Models\Event::find($eventId);
                            if (!$event) {
                                return ['free' => 'Free'];
                            }
                            
                            $orgPaymentMethods = OrganizationPaymentMethod::where('organization_id', $event->organization_id)
                                ->where('is_active', true)
                                ->orderBy('display_order')
                                ->get();
                            
                            $options = $orgPaymentMethods->mapWithKeys(function ($method) {
                                $config = config('constants.payment_methods.' . $method->payment_method, []);
                                $label = is_array($config) ? $config['label'] : $config;
                                return [$method->payment_method => $label];
                            })->toArray();
                            
                            $options['free'] = 'Free';
                            
                            return $options;
                        })
                        ->default('free')
                        ->helperText('Only configured payment methods are shown'),

                    Forms\Components\TextInput::make('amount')
                        ->numeric()
                        ->default(0)
                        ->prefix(config('constants.currency.symbol'))
                        ->suffix(config('constants.currency.code')),

                    Forms\Components\Select::make('payment_status')
                        ->options(config('constants.payment_statuses'))
                        ->default('pending'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Status')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options(config('constants.ticket_statuses'))
                        ->default('active'),

                    Forms\Components\DateTimePicker::make('delivered_at')
                        ->disabled(),
                ])
                ->columns(2),
        ]);
    }

    /* ------------------------------------------------------------
     | Table
     ------------------------------------------------------------ */

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('ticket_number')
                    ->label('Ticket #')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Client')
                    ->searchable(),

                Tables\Columns\TextColumn::make('event.name')
                    ->label('Event')
                    ->limit(25),

                Tables\Columns\TextColumn::make('amount')
                    ->money(config('constants.currency.code')),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Payment')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'gray' => 'refunded',
                    ]),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'gray' => 'pending',
                        'info' => 'active',
                        'success' => 'checked_in',
                        'danger' => 'refunded',
                    ]),
            ])

            /* ------------------------------------------------------------
             | Row Actions
             ------------------------------------------------------------ */

            ->actions([
                // APPROVE PAYMENT
                Tables\Actions\Action::make('approve_payment')
                    ->label(fn ($record) => $record->hasPendingPayments() ? 'Approve Payment' : 'Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn ($record) =>
                        ($record->payment_status === 'pending' || $record->payment_status === 'partial')
                        && auth()->user()?->hasPermissionTo('approve_payment')
                        && $record->hasPendingPayments()
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Approve Payment')
                    ->modalDescription('Review and approve the pending payment')
                    ->form(function ($record) {
                        // Get pending payment
                        $pendingPayment = $record->payments()->pending()->latest()->first();
                        
                        if (!$pendingPayment) {
                            return [];
                        }

                        $orgPaymentMethods = OrganizationPaymentMethod::where('organization_id', $record->event->organization_id)
                            ->where('is_active', true)
                            ->orderBy('display_order')
                            ->get();
                        
                        $paymentOptions = $orgPaymentMethods->mapWithKeys(function ($method) {
                            $config = config('constants.payment_methods.' . $method->payment_method, []);
                            $label = is_array($config) ? $config['label'] : $config;
                            return [$method->payment_method => $label];
                        })->toArray();
                        
                        $currencySymbol = config('constants.currency.symbol', 'M');
                        
                        return [
                            Forms\Components\Section::make('Current Payment Status')
                                ->schema([
                                    Forms\Components\Grid::make(3)
                                        ->schema([
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
                                    
                                    Forms\Components\Grid::make(2)
                                        ->schema([
                                            Forms\Components\Placeholder::make('payment_type')
                                                ->label('Payment Type')
                                                ->content(ucfirst($pendingPayment->payment_type)),
                                            
                                            Forms\Components\Placeholder::make('payment_date')
                                                ->label('Payment Date')
                                                ->content($pendingPayment->payment_date?->format('M j, Y @ g:i A')),
                                            
                                            Forms\Components\Placeholder::make('current_method')
                                                ->label('Stated Method')
                                                ->content($pendingPayment->payment_method_label ?? 'Not specified'),
                                            
                                            Forms\Components\Placeholder::make('current_reference')
                                                ->label('Stated Reference')
                                                ->content($pendingPayment->payment_reference ?: 'Not provided'),
                                        ]),
                                ])
                                ->columnSpanFull(),
                            
                            Forms\Components\Select::make('payment_method')
                                ->label('Confirm Payment Method')
                                ->options($paymentOptions)
                                ->default($pendingPayment->payment_method)
                                ->required()
                                ->helperText('Verify the payment method'),
                            
                            Forms\Components\TextInput::make('payment_reference')
                                ->label('Payment Reference')
                                ->default($pendingPayment->payment_reference)
                                ->required()
                                ->helperText('Verify the payment reference'),
                        ];
                    })
                    ->action(function (Ticket $record, array $data) {
                        // Get pending payment
                        $pendingPayment = $record->payments()->pending()->latest()->first();
                        
                        if (!$pendingPayment) {
                            Notification::make()
                                ->title('No Pending Payment')
                                ->body('This ticket has no pending payments to approve.')
                                ->warning()
                                ->send();
                            return;
                        }

                        DB::beginTransaction();
                        try {
                            // Approve the payment
                            $pendingPayment->update([
                                'status' => 'approved',
                                'payment_method' => $data['payment_method'],
                                'payment_reference' => $data['payment_reference'],
                                'approved_by' => auth()->id(),
                                'approved_at' => now(),
                            ]);

                            // Update ticket payment status
                            $record->updatePaymentStatus();

                            // Increment tier sold count only if this is first approval
                            if ($record->payments()->approved()->count() === 1) {
                                $record->tier->increment('quantity_sold');
                            }

                            $remainingAmount = $record->remaining_amount;
                            $isFullyPaid = $remainingAmount <= 0;

                            // ✅ NEW: Send email + WhatsApp after approval
                            if ($isFullyPaid && $record->payment_status === 'completed') {
                                // Dispatch email job (if email exists)
                                if ($record->client->email) {
                                    dispatch(new SendTicketApprovedEmail($record->id))->afterResponse();
                                }

                                // Send WhatsApp (if opted in)
                                if ($record->has_whatsapp && $record->client->phone) {
                                    // Use afterResponse to prevent blocking
                                    dispatch(function() use ($record) {
                                        WhatsAppController::deliverTicket($record);
                                    })->afterResponse();
                                }
                            }

                            Notification::make()
                                ->title('Payment Approved')
                                ->body($isFullyPaid 
                                    ? "Ticket {$record->ticket_number} is now fully paid and activated! Notifications sent."
                                    : "Payment approved. Remaining balance: " . config('constants.currency.symbol') . ' ' . number_format($remainingAmount, 2)
                                )
                                ->success()
                                ->send();

                            DB::commit();
                        } catch (\Exception $e) {
                            DB::rollBack();
                            \Log::error('Payment approval failed: ' . $e->getMessage());
                            
                            Notification::make()
                                ->title('Approval Failed')
                                ->body('Failed to approve payment. Please try again.')
                                ->danger()
                                ->send();
                        }
                    }),

                // MANUAL RESEND ACTIONS
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('resend_whatsapp')
                        ->label('Resend WhatsApp')
                        ->icon('heroicon-o-chat-bubble-left-right')
                        ->color('success')
                        ->visible(fn ($record) => 
                            $record->payment_status === 'completed' 
                            && $record->has_whatsapp
                        )
                        ->requiresConfirmation()
                        ->action(function (Ticket $record) {
                            $success = WhatsAppController::deliverTicket($record);
                            
                            if ($success) {
                                Notification::make()
                                    ->title('WhatsApp Sent')
                                    ->body('Ticket sent to ' . $record->client->phone)
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('Failed')
                                    ->body('Failed to send WhatsApp. Check logs.')
                                    ->danger()
                                    ->send();
                            }
                        }),

                    Tables\Actions\Action::make('resend_email')
                        ->label('Resend Email')
                        ->icon('heroicon-o-envelope')
                        ->visible(fn ($record) => 
                            $record->payment_status === 'completed' 
                            && $record->client->email
                        )
                        ->requiresConfirmation()
                        ->action(function (Ticket $record) {
                            dispatch(new SendTicketApprovedEmail($record->id))->afterResponse();
                            
                            Notification::make()
                                ->title('Email Sent')
                                ->body('Ticket sent to ' . $record->client->email)
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\Action::make('copy_link')
                        ->label('Copy Download Link')
                        ->icon('heroicon-o-link')
                        ->modalHeading('Ticket Download Link')
                        ->modalContent(fn ($record) => view(
                            'filament.modals.ticket-link',
                            [
                                'ticket' => $record,
                                'link' => route('ticket.download', $record->qr_code),
                            ]
                        ))
                        ->modalSubmitAction(false),
                ])
                    ->label('Send Ticket')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('primary')
                    ->visible(fn ($record) => $record->payment_status === 'completed'),

                // MORE ACTIONS
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),

                    Tables\Actions\Action::make('refund')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('danger')
                        ->visible(fn ($record) =>
                            $record->payment_status === 'completed'
                            && $record->status !== 'refunded'
                        )
                        ->requiresConfirmation()
                        ->action(fn (Ticket $record) => $record->refund()),

                    Tables\Actions\DeleteAction::make(),
                ])
                    ->icon('heroicon-o-ellipsis-vertical'),
            ])

            /* ------------------------------------------------------------
             | Bulk Actions
             ------------------------------------------------------------ */

            ->bulkActions([
                Tables\Actions\BulkAction::make('approve_payments')
                    ->label('Approve Payments')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve Multiple Payments')
                    ->modalDescription('Review and approve all selected pending payments')
                    ->form(function (Collection $records) {
                        $firstTicket = $records->first();
                        if (!$firstTicket) {
                            return [];
                        }
                        
                        $orgPaymentMethods = OrganizationPaymentMethod::where('organization_id', $firstTicket->event->organization_id)
                            ->where('is_active', true)
                            ->orderBy('display_order')
                            ->get();
                        
                        $paymentOptions = $orgPaymentMethods->mapWithKeys(function ($method) {
                            $config = config('constants.payment_methods.' . $method->payment_method, []);
                            $label = is_array($config) ? $config['label'] : $config;
                            return [$method->payment_method => $label];
                        })->toArray();
                        
                        $defaultMethod = $orgPaymentMethods->first()?->payment_method ?? 'cash';
                        
                        return [
                            Forms\Components\Section::make('Bulk Approval Summary')
                                ->description('Approving ' . $records->where('payment_status', 'pending')->count() . ' ticket(s). Email and WhatsApp will be sent automatically to each approved ticket.')
                                ->schema([
                                    Forms\Components\Toggle::make('auto_use_existing')
                                        ->label('Use Existing Payment Details')
                                        ->helperText('Use payment method/reference from tickets that already have them')
                                        ->default(true),
                                    
                                    Forms\Components\Select::make('payment_method')
                                        ->label('Default Payment Method')
                                        ->helperText('Used only for tickets without a payment method')
                                        ->options($paymentOptions)
                                        ->default($defaultMethod)
                                        ->required(),
                                ])
                                ->columnSpanFull(),
                        ];
                    })
                    ->action(function (Collection $records, array $data) {
                        $approved = 0;
                        
                        foreach ($records as $record) {
                            if ($record->payment_status === 'pending') {
                                $paymentMethod = ($data['auto_use_existing'] && $record->payment_method) 
                                    ? $record->payment_method 
                                    : $data['payment_method'];
                                
                                $paymentReference = ($data['auto_use_existing'] && $record->payment_reference)
                                    ? $record->payment_reference
                                    : 'BULK-APPROVED-' . time() . '-' . $record->id;
                                
                                $record->update([
                                    'payment_status' => 'completed',
                                    'payment_date' => now(),
                                    'payment_method' => $paymentMethod,
                                    'payment_reference' => $paymentReference,
                                    'status' => 'active',
                                ]);
                                
                                $record->tier->increment('quantity_sold');
                                
                                // ✅ NEW: Send notifications
                                if ($record->client->email) {
                                    dispatch(new SendTicketApprovedEmail($record->id))->afterResponse();
                                }
                                
                                if ($record->has_whatsapp && $record->client->phone) {
                                    dispatch(function() use ($record) {
                                        WhatsAppController::deliverTicket($record);
                                    })->afterResponse();
                                }
                                
                                $approved++;
                            }
                        }

                        Notification::make()
                            ->title('Payments Approved')
                            ->body("{$approved} ticket(s) have been activated and notifications sent")
                            ->success()
                            ->send();
                    }),
            ]);
    }

    /* ------------------------------------------------------------
     | Pages
     ------------------------------------------------------------ */

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit' => Pages\EditTicket::route('/{record}/edit'),
        ];
    }
}