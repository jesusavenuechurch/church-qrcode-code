<?php

namespace App\Filament\Resources;

use App\Models\Ticket;
use App\Models\Event;
use App\Models\Client;
use App\Models\EventTier;
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
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TicketsImport;
use Filament\Forms\Components\FileUpload;

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
        return $query->whereHas('event', fn(Builder $q) => $q->where('organization_id', $user->organization_id));
    }

    public static function form(Form $form): Form
    {
        $user = auth()->user();
        $isSuperAdmin = $user?->isSuperAdmin();

        return $form->schema([
            Forms\Components\Hidden::make('organization_id')
                ->default(fn () => $user->organization_id),

            Forms\Components\Section::make('Ticket Details')
                ->description('Manage guest access and event tier assignment.')
                ->schema([
                    Forms\Components\Select::make('event_id')
                        ->relationship('event', 'name', modifyQueryUsing: fn (Builder $query) => 
                            $isSuperAdmin ? $query : $query->where('organization_id', $user->organization_id)
                        )
                        ->required()->live()->hidden(fn ($record) => $record !== null)
                        ->default(fn () => $isSuperAdmin ? null : Event::where('organization_id', $user->organization_id)->orderBy('created_at', 'desc')->value('id'))
                        ->afterStateUpdated(fn (Forms\Set $set) => $set('event_tier_id', null)),

                    Forms\Components\Placeholder::make('event_name_label')
                        ->label('Event')->content(fn ($record) => $record?->event?->name)->visible(fn ($record) => $record !== null),

                    Forms\Components\Select::make('client_id')
                        ->relationship('client', 'full_name', modifyQueryUsing: fn (Builder $query) => 
                            $isSuperAdmin ? $query : $query->where('organization_id', $user->organization_id)
                        )
                        ->required()->searchable()->preload()->hidden(fn ($record) => $record !== null)
                        ->createOptionForm([
                            Forms\Components\TextInput::make('full_name')->required(),
                            Forms\Components\TextInput::make('phone')->tel()->required(),
                            Forms\Components\Hidden::make('organization_id')->default($user->organization_id),
                        ]),

                    Forms\Components\Placeholder::make('client_name_label')
                        ->label('Guest')->content(fn ($record) => $record?->client?->full_name)->visible(fn ($record) => $record !== null),

                    Forms\Components\Select::make('event_tier_id')
                        ->label('Access Tier')
                        ->relationship('tier', 'tier_name', modifyQueryUsing: fn (Builder $query, Forms\Get $get) => 
                            $query->when($get('event_id'), fn ($q) => $q->where('event_id', $get('event_id')))
                        )
                        ->required()->live()->hidden(fn ($record) => $record !== null)
                        ->afterStateUpdated(function ($state, Forms\Set $set) {
                            if ($state) {
                                $tier = EventTier::find($state);
                                if ($tier) $set('amount', $tier->price);
                            }
                        }),

                    Forms\Components\Placeholder::make('tier_name_label')
                        ->label('Access Tier')->content(fn ($record) => $record?->tier?->tier_name)->visible(fn ($record) => $record !== null),

                    Forms\Components\TextInput::make('amount')
                        ->numeric()->prefix(config('constants.currency.symbol'))->required()->readOnly()
                        ->hidden(fn ($record) => $record !== null),

                    Forms\Components\Placeholder::make('amount_label')
                        ->label('Costing')
                        ->content(fn ($record) => $record?->is_complimentary 
                            ? '🎁 Complimentary' 
                            : config('constants.currency.symbol') . ' ' . number_format($record?->amount ?? 0, 2)
                        )
                        ->visible(fn ($record) => $record !== null),
                ])->columns(2),

            Forms\Components\Section::make('Complimentary Ticket Details')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Placeholder::make('issued_by')
                            ->label('Issued By')->content(fn ($record) => $record->complimentaryIssuedBy?->name ?? 'N/A'),
                        Forms\Components\Placeholder::make('reason')
                            ->label('Reason')->content(fn ($record) => $record->complimentary_reason ?? 'No reason provided'),
                    ]),
                ])
                ->visible(fn ($record) => $record?->is_complimentary ?? false),

            Forms\Components\Section::make('Payment & Access Management')
                ->visible(fn ($record) => $record !== null)
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\ToggleButtons::make('payment_status')
                            ->options(config('constants.payment_statuses'))
                            ->inline()->colors(['pending' => 'warning', 'partial' => 'info', 'completed' => 'success', 'failed' => 'danger']),
                        Forms\Components\ToggleButtons::make('status')
                            ->options(['active' => 'Active', 'checked_in' => 'Checked In', 'refunded' => 'Refunded', 'void' => 'Void'])
                            ->inline()->colors(['active' => 'info', 'checked_in' => 'success', 'refunded' => 'danger']),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('client.full_name')
                    ->label('Guest')
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->description(fn ($record) => 
                        $record->ticket_number . " • " . 
                        $record->tier->tier_name . 
                        ($record->is_complimentary ? " 🎁 COMP" : "")
                    )->wrap(),

                Tables\Columns\TextColumn::make('amount')
                    ->money(config('constants.currency.code'))
                    ->weight(FontWeight::Black)
                    ->color('primary')
                    ->alignment(Alignment::Right)
                    ->visible(fn($record) => !$record?->is_complimentary),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record) => $record->is_complimentary ? 'Complimentary' : ucfirst($state))
                    ->color(fn ($state, $record) => $record->is_complimentary ? 'gray' : match ($state) {
                        'pending' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Access')
                    ->badge()
                    ->icons(['heroicon-m-ticket' => 'active', 'heroicon-m-check-badge' => 'checked_in'])
                    ->color(fn ($state) => match ($state) {
                        'active' => 'info',
                        'checked_in' => 'success',
                        'refunded' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->headerActions([
                // ===== ISSUE COMPLIMENTARY TICKET =====
                Tables\Actions\Action::make('issue_complimentary')
                    ->label('Issue Comp Ticket')->icon('heroicon-o-gift')->color('warning')->button()
                    ->form([
                        Forms\Components\Section::make('Event & Tier')->schema([
                                Forms\Components\Select::make('event_id')->label('Event')->options(fn() => Event::where('status', 'published')->when(!auth()->user()->isSuperAdmin(), fn($q) => $q->where('organization_id', auth()->user()->organization_id))->pluck('name', 'id'))->required()->live()->afterStateUpdated(fn ($set) => $set('tier_id', null)),
                                Forms\Components\Select::make('tier_id')->label('Ticket Tier')->options(fn (Forms\Get $get) => EventTier::where('event_id', $get('event_id'))->pluck('tier_name', 'id'))->required()->disabled(fn (Forms\Get $get) => !$get('event_id')),
                        ])->columns(2),
                        Forms\Components\Section::make('Guest Info')->schema([
                                Forms\Components\TextInput::make('full_name')->required(),
                                Forms\Components\TextInput::make('email')->email(),
                                Forms\Components\TextInput::make('phone')->tel()->prefix('+266'),
                                Forms\Components\Toggle::make('has_whatsapp')->label('Send via WhatsApp')->default(true),
                        ])->columns(2),
                        Forms\Components\Textarea::make('reason')->label('Reason for Comp')->rows(2),
                    ])
                    ->action(function (array $data) {
    $user = auth()->user();
    DB::beginTransaction();
    try {
        $client = null;

        // 1. Try to find by Phone within the organization
        if (!empty($data['phone'])) {
            $client = Client::where('phone', $data['phone'])
                ->where('organization_id', $user->organization_id)
                ->first();
        }

        // 2. If not found, try to find by Email within the organization
        if (!$client && !empty($data['email'])) {
            $client = Client::where('email', $data['email'])
                ->where('organization_id', $user->organization_id)
                ->first();
        }

        // 3. If still no client, create a brand new one
        if (!$client) {
            $client = Client::create([
                'full_name' => $data['full_name'],
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'organization_id' => $user->organization_id,
            ]);
        } else {
            // Optional: Update the existing client's name if it changed
            $client->update(['full_name' => $data['full_name']]);
        }

        $ticket = Ticket::create([
            'event_id' => $data['event_id'],
            'client_id' => $client->id,
            'event_tier_id' => $data['tier_id'],
            'created_by' => $user->id,
            'has_whatsapp' => $data['has_whatsapp'] ?? false,
            'preferred_delivery' => $data['has_whatsapp'] ? 'both' : 'email'
        ]);

        $ticket->markAsComplimentary($user->id, $data['reason'] ?? 'Admin Issued');
        $ticket->generateQrCode();

        dispatch(fn() => $ticket->autoDeliverTicket())->afterResponse();

        DB::commit();
        Notification::make()->title('Comp Ticket Issued')->success()->send();
    } catch (\Exception $e) {
        DB::rollBack();
        Notification::make()->title('Error')->body($e->getMessage())->danger()->send();
    }
}),

                // ===== BULK IMPORT TICKETS =====
                Tables\Actions\Action::make('bulk_import')
                    ->label('Bulk Import')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('info')
                    ->button()
                    ->modalHeading('Bulk Import Tickets')
                    ->modalDescription('Upload a CSV/Excel file to create multiple tickets at once')
                    ->modalWidth('2xl')
                    ->form([
                        Forms\Components\Section::make('Event & Tier Selection')
                            ->schema([
                                Forms\Components\Select::make('event_id')
                                    ->label('Event')
                                    ->options(fn() => Event::where('status', 'published')
                                        ->when(!auth()->user()->isSuperAdmin(), fn($q) => 
                                            $q->where('organization_id', auth()->user()->organization_id)
                                        )
                                        ->pluck('name', 'id'))
                                    ->required()
                                    ->live()
                                    ->afterStateUpdated(fn ($set) => $set('tier_id', null)),

                                Forms\Components\Select::make('tier_id')
                                    ->label('Ticket Tier')
                                    ->options(fn (Forms\Get $get) => 
                                        EventTier::where('event_id', $get('event_id'))
                                            ->pluck('tier_name', 'id')
                                    )
                                    ->required()
                                    ->disabled(fn (Forms\Get $get) => !$get('event_id'))
                                    ->helperText('All imported tickets will be assigned to this tier'),
                            ])->columns(2),

                        Forms\Components\Section::make('Ticket Type')
                            ->schema([
                                Forms\Components\Toggle::make('is_complimentary')
                                    ->label('Mark all as Complimentary Tickets')
                                    ->default(true)
                                    ->helperText('If enabled, all tickets will be free and auto-approved')
                                    ->live(),

                                Forms\Components\Textarea::make('reason')
                                    ->label('Reason for Complimentary')
                                    ->visible(fn (Forms\Get $get) => $get('is_complimentary'))
                                    ->default('Bulk import - complimentary tickets')
                                    ->rows(2),
                            ])->columns(1),

                        Forms\Components\Section::make('Upload File')
                            ->schema([
                                FileUpload::make('file')
                                    ->label('CSV/Excel File')
                                    ->acceptedFileTypes([
                                        'text/csv',
                                        'text/plain',
                                        'application/vnd.ms-excel',
                                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                    ])
                                    ->maxSize(5120) // 5MB
                                    ->required()
                                    ->helperText('Upload a CSV or Excel file with columns: full_name, phone, email (optional), has_whatsapp (optional)'),

                                Forms\Components\Placeholder::make('template_download')
                                    ->label('Need a template?')
                                    ->content(new \Illuminate\Support\HtmlString('
                                        <a href="/download-bulk-ticket-template" 
                                           class="text-blue-600 hover:underline font-medium"
                                           download>
                                            📥 Download CSV Template
                                        </a>
                                    ')),
                            ]),

                        Forms\Components\Section::make('Important Notes')
                            ->schema([
                                Forms\Components\Placeholder::make('notes')
                                    ->content(new \Illuminate\Support\HtmlString('
                                        <ul class="text-sm space-y-1 text-gray-600">
                                            <li>• <strong>Required columns:</strong> full_name, phone</li>
                                            <li>• <strong>Optional columns:</strong> email, has_whatsapp</li>
                                            <li>• Phone numbers will be auto-formatted to +266 format</li>
                                            <li>• Duplicate phone numbers will be skipped</li>
                                            <li>• Clients will be created if they don\'t exist</li>
                                            <li>• QR codes will be generated automatically</li>
                                            <li>• Complimentary tickets will be delivered immediately</li>
                                        </ul>
                                    ')),
                            ])
                            ->collapsible(),
                    ])
                    ->action(function (array $data) {
                        $user = auth()->user();
                        
                        try {
                            // Get uploaded file path
                            $filePath = storage_path('app/public/' . $data['file']);

                            // Create importer
                            $import = new TicketsImport(
                                eventId: $data['event_id'],
                                tierId: $data['tier_id'],
                                isComplimentary: $data['is_complimentary'] ?? false,
                                reason: $data['reason'] ?? null,
                                organizationId: $user->organization_id,
                                createdBy: $user->id
                            );

                            // Process import
                            Excel::import($import, $filePath);

                            // Build result message
                            $message = "✅ Successfully created {$import->successCount} ticket(s)";
                            
                            if ($import->errorCount > 0) {
                                $message .= "\n⚠️ {$import->errorCount} row(s) had errors";
                                
                                // Show first 5 errors
                                $errorDetails = collect($import->errors)
                                    ->take(5)
                                    ->map(fn($err) => "Row {$err['row']}: {$err['error']}")
                                    ->join("\n");
                                
                                $message .= "\n\n" . $errorDetails;
                                
                                if ($import->errorCount > 5) {
                                    $message .= "\n... and " . ($import->errorCount - 5) . " more errors";
                                }
                            }

                            Notification::make()
                                ->title('Bulk Import Complete')
                                ->body($message)
                                ->success()
                                ->duration(10000)
                                ->send();

                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Import Failed')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('approve_payment')
                    ->label('Approve')->icon('heroicon-m-check-badge')->color('success')->button()
                    ->visible(fn ($record) => ($record->payment_status === 'pending' || $record->payment_status === 'partial') && auth()->user()?->can('approve_payment') && $record->hasPendingPayments())
                    ->form(fn($record) => static::getOriginalApproveForm($record))
                    ->action(fn(Ticket $record, array $data) => static::handleOriginalApproval($record, $data)),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('preview_pass')->label('View Pass')->icon('heroicon-o-arrow-top-right-on-square')->url(fn ($record) => route('ticket.download', $record->qr_code))->openUrlInNewTab(),
                    
                    Tables\Actions\Action::make('copy_link')
                        ->label('Share Ticket')->icon('heroicon-o-share')
                        ->modalHeading('Share Access Pass')->modalWidth('md')
                        ->modalContent(fn ($record) => view('filament.modals.ticket-link', ['ticket' => $record, 'link' => route('ticket.download', $record->qr_code)]))
                        ->modalSubmitAction(false),

                    Tables\Actions\Action::make('resend_whatsapp')->label('Resend WhatsApp')->icon('heroicon-o-chat-bubble-left-right')->color('success')->visible(fn ($record) => $record->payment_status === 'completed' && $record->has_whatsapp)->action(fn ($record) => WhatsAppController::deliverTicket($record)),
                    Tables\Actions\EditAction::make()->slideOver(),
                    Tables\Actions\DeleteAction::make(),
                ])->icon('heroicon-m-ellipsis-vertical')->color('gray')->button()->label('Options'),
            ]);
    }

    protected static function getOriginalApproveForm($record): array
    {
        $pendingPayment = $record->payments()->pending()->latest()->first();
        if (!$pendingPayment) return [];
        $paymentOptions = OrganizationPaymentMethod::where('organization_id', $record->event->organization_id)->where('is_active', true)->get()->mapWithKeys(fn($m) => [$m->payment_method => config('constants.payment_methods.' . $m->payment_method . '.label', $m->payment_method)])->toArray();
        return [
            Forms\Components\Section::make('Confirmation Details')->schema([
                Forms\Components\Grid::make(2)->schema([
                    Forms\Components\Placeholder::make('pay')->label('To Approve')->content('M ' . number_format($pendingPayment->amount, 2)),
                    Forms\Components\ToggleButtons::make('target_ticket_status')->label('Set Ticket Status To')->options(config('constants.payment_statuses'))->default('completed')->inline()->required()->colors(['pending' => 'warning', 'partial' => 'info', 'completed' => 'success']),
                ]),
            ]),
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('payment_method')->options($paymentOptions)->default($pendingPayment->payment_method)->required(),
                Forms\Components\TextInput::make('payment_reference')->default($pendingPayment->payment_reference)->required(),
            ]),
        ];
    }

    protected static function handleOriginalApproval(Ticket $record, array $data): void
    {
        $pendingPayment = $record->payments()->pending()->latest()->first();
        if (!$pendingPayment) return;
        DB::beginTransaction();
        try {
            $pendingPayment->update(['status' => 'approved', 'payment_method' => $data['payment_method'], 'payment_reference' => $data['payment_reference'], 'approved_by' => auth()->id(), 'approved_at' => now()]);
            $record->update(['payment_status' => $data['target_ticket_status']]);
            if ($record->payments()->approved()->count() === 1) $record->tier->increment('quantity_sold');
            if ($record->payment_status === 'completed') {
                $record->update(['status' => 'active']);
                if ($record->client->email) dispatch(new SendTicketApprovedEmail($record->id))->afterResponse();
                if ($record->has_whatsapp && $record->client->phone) dispatch(fn() => WhatsAppController::deliverTicket($record))->afterResponse();
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
        return ['index' => Pages\ListTickets::route('/'), 'create' => Pages\CreateTicket::route('/create'), 'edit' => Pages\EditTicket::route('/{record}/edit')];
    }
}