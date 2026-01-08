<?php

namespace App\Filament\Resources\EventResource\Pages;

use App\Filament\Resources\EventResource;
use App\Models\OrganizationPaymentMethod;
use Filament\Forms;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    use CreateRecord\Concerns\HasWizard;

    protected function getSteps(): array
    {
        $user = auth()->user();
        $showPaymentWarning = false;

        // Check if payment methods exist
        if (!$user?->isSuperAdmin() && $user?->organization_id) {
            $showPaymentWarning = !OrganizationPaymentMethod::where('organization_id', $user->organization_id)
                ->where('is_active', true)
                ->exists();
        }

        $steps = [];

        // ============================================================================
        // STEP 0: Payment Warning (only if no payment methods)
        // ============================================================================
        if ($showPaymentWarning) {
            $steps[] = Step::make('Important Notice')
                ->icon('heroicon-o-exclamation-triangle')
                ->description('Before you start')
                ->schema([
                    Forms\Components\Placeholder::make('payment_setup_warning')
                        ->label('')
                        ->content(new \Illuminate\Support\HtmlString('
                            <div class="rounded-lg bg-yellow-50 border-2 border-yellow-400 p-6">
                                <div class="flex items-start">
                                    <svg class="h-10 w-10 text-yellow-600 mr-4 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    <div class="flex-1">
                                        <h3 class="text-2xl font-bold text-yellow-900 mb-3">
                                            ⚠️ Payment Methods Not Set Up
                                        </h3>
                                        <p class="text-yellow-800 mb-4 text-lg leading-relaxed">
                                            You haven\'t configured any payment methods yet. If you plan to create <strong>paid tickets</strong>,
                                            you need to set up payment methods first so customers know where to send money.
                                        </p>
                                        <div class="bg-white rounded-lg p-4 mb-4 border border-yellow-200">
                                            <p class="text-sm font-semibold text-gray-700 mb-2">💡 What are payment methods?</p>
                                            <p class="text-sm text-gray-600">
                                                Payment methods tell customers your M-Pesa number, EcoCash details, bank account, etc.
                                                They\'ll see these during registration so they know where to send payments.
                                            </p>
                                        </div>
                                        <div class="space-y-3">
                                            <a href="' . route('filament.admin.resources.organization-payment-methods.create') . '"
                                               class="inline-flex items-center px-6 py-3 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg shadow-sm transition-colors text-base"
                                               target="_blank">
                                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z"/>
                                                </svg>
                                                Set Up Payment Methods Now
                                            </a>
                                            <p class="text-sm text-yellow-700">
                                                <strong>Only creating free events?</strong> You can skip this and continue to the next step.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        '))
                        ->columnSpanFull(),
                ]);
        }

        // ============================================================================
        // STEP 1: Event Details
        // ============================================================================
        $steps[] = Step::make('Event Details')
            ->icon('heroicon-o-information-circle')
            ->description('Basic event information')
            ->schema([
                Forms\Components\Hidden::make('slug_locked')->default(false),
                Forms\Components\Hidden::make('tagline_locked')->default(false),

                Forms\Components\Select::make('organization_id')
                    ->relationship('organization', 'name')
                    ->required()
                    ->searchable()
                    ->disabled(fn () => !auth()->user()?->isSuperAdmin())
                    ->default(auth()->user()?->organization_id)
                    ->dehydrated(true)
                    ->columnSpanFull(),

                Forms\Components\TextInput::make('name')
                    ->label('Event Name')
                    ->required()
                    ->live(debounce: 500)
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        if (!$get('slug_locked')) {
                            $set('slug', Str::slug($state));
                        }
                        if (!$get('tagline_locked')) {
                            $set('tagline', $state);
                        }
                    })
                    ->columnSpan(1),

                Forms\Components\TextInput::make('slug')
                    ->label('Public URL Slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->afterStateUpdated(fn ($set) => $set('slug_locked', true))
                    ->helperText('Auto-generated from event name')
                    ->columnSpan(1),

                Forms\Components\TextInput::make('tagline')
                    ->label('Tagline')
                    ->afterStateUpdated(fn ($set) => $set('tagline_locked', true))
                    ->helperText('Short catchy description')
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('description')
                    ->label('Full Description')
                    ->rows(4)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('is_public')
                    ->label('Public Event')
                    ->default(true)
                    ->columnSpanFull(),
            ])
            ->columns(2);

        // ============================================================================
        // STEP 2: Schedule & Location
        // ============================================================================
        $steps[] = Step::make('Schedule & Location')
            ->icon('heroicon-o-calendar')
            ->description('When and where')
            ->schema([
                Forms\Components\Section::make('Event Date & Time')
                    ->description('When will your event take place?')
                    ->schema([
                        Forms\Components\DatePicker::make('event_date_only')
                            ->label('Event Date')
                            ->required()
                            ->native(false)
                            ->displayFormat('D, M j, Y')
                            ->minDate(now())
                            ->prefixIcon('heroicon-o-calendar')
                            ->default(now())
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $date = $get('event_date_only');
                                $time = $get('event_time_only');

                                if ($date && $time) {
                                    $set(
                                        'event_date',
                                        \Carbon\Carbon::parse($date)
                                            ->setTimeFromTimeString($time)
                                            ->format('Y-m-d H:i:s')
                                    );
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\TimePicker::make('event_time_only')
                            ->label('Event Time')
                            ->required()
                            ->native(false)
                            ->seconds(false)
                            ->prefixIcon('heroicon-o-clock')
                            ->default('18:00')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                $date = $get('event_date_only');
                                $time = $get('event_time_only');

                                if ($date && $time) {
                                    $set(
                                        'event_date',
                                        \Carbon\Carbon::parse($date)
                                            ->setTimeFromTimeString($time)
                                            ->format('Y-m-d H:i:s')
                                    );
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\Hidden::make('event_date'),

                        Forms\Components\Placeholder::make('event_datetime_preview')
                            ->label('Full Event Date & Time')
                            ->content(function (Forms\Get $get) {
                                $date = $get('event_date_only');
                                $time = $get('event_time_only');

                                if (!$date || !$time) {
                                    return '⏱️ Select date and time above';
                                }

                                try {
                                    $datetime = \Carbon\Carbon::parse($date . ' ' . $time);
                                    return '📅 <strong>' . $datetime->format('l, F j, Y @ g:i A') . '</strong>';
                                } catch (\Exception $e) {
                                    return '❌ Invalid date/time';
                                }
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Forms\Components\Section::make('Registration Deadline')
                    ->description('When should registration close? (Optional)')
                    ->schema([
                        Forms\Components\DatePicker::make('registration_deadline_date')
                            ->label('Deadline Date')
                            ->nullable()
                            ->native(false)
                            ->displayFormat('D, M j, Y')
                            ->prefixIcon('heroicon-o-calendar')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state && $get('registration_deadline_time')) {
                                    $set('registration_deadline', $state . ' ' . $get('registration_deadline_time'));
                                } elseif (!$state) {
                                    $set('registration_deadline', null);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\TimePicker::make('registration_deadline_time')
                            ->label('Deadline Time')
                            ->nullable()
                            ->native(false)
                            ->seconds(false)
                            ->prefixIcon('heroicon-o-clock')
                            ->default('23:59')
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state && $get('registration_deadline_date')) {
                                    $set('registration_deadline', $get('registration_deadline_date') . ' ' . $state);
                                }
                            })
                            ->columnSpan(1),

                        Forms\Components\Hidden::make('registration_deadline'),

                        Forms\Components\Placeholder::make('deadline_preview')
                            ->label('Registration Closes')
                            ->content(function (Forms\Get $get) {
                                $date = $get('registration_deadline_date');
                                $time = $get('registration_deadline_time');

                                if (!$date || !$time) {
                                    return '♾️ No deadline - registration stays open until event';
                                }

                                try {
                                    $datetime = \Carbon\Carbon::parse($date . ' ' . $time);
                                    return '🔒 <strong>' . $datetime->format('l, F j, Y @ g:i A') . '</strong>';
                                } catch (\Exception $e) {
                                    return '❌ Invalid date/time';
                                }
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(),

                Forms\Components\Section::make('Venue Details')
                    ->description('Where will the event take place?')
                    ->schema([
                        Forms\Components\TextInput::make('venue')
                            ->label('Venue Name')
                            ->maxLength(255)
                            ->placeholder('e.g., Maseru Convention Center')
                            ->prefixIcon('heroicon-o-building-office')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('capacity')
                            ->label('Maximum Capacity')
                            ->numeric()
                            ->nullable()
                            ->placeholder('e.g., 500')
                            ->helperText('Leave empty for unlimited')
                            ->prefixIcon('heroicon-o-users')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('location')
                            ->label('Full Address')
                            ->rows(3)
                            ->maxLength(500)
                            ->placeholder('Complete address with directions if needed')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);

        // ============================================================================
        // STEP 3: Ticket Tiers
        // ============================================================================
        $steps[] = Step::make('Ticket Tiers')
            ->icon('heroicon-o-ticket')
            ->description('Create ticket types')
            ->schema([
                Forms\Components\Repeater::make('tiers')
                    ->label('Ticket Tiers')
                    ->relationship('tiers')
                    ->schema([
                        Forms\Components\TextInput::make('tier_name')
                            ->label('Tier Name')
                            ->required()
                            ->placeholder('e.g., VIP, Gold, Early Bird')
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('price')
                            ->label('Price')
                            ->numeric()
                            ->required()
                            ->step(0.01)
                            ->minValue(0)
                            ->suffix('LSL')
                            ->helperText('Set to 0 for free tickets')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('quantity_available')
                            ->label('Quantity')
                            ->numeric()
                            ->nullable()
                            ->minValue(1)
                            ->helperText('Empty = unlimited')
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('quantity_per_purchase')
                            ->label('Max Per Purchase')
                            ->numeric()
                            ->default(1)
                            ->minValue(1)
                            ->helperText('Maximum tickets a single customer can buy in one transaction')
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('description')
                            ->label('Benefits / Description')
                            ->rows(2)
                            ->placeholder('What does this tier include?')
                            ->columnSpanFull(),

                                                Forms\Components\Toggle::make('allow_installments')
                            ->label('Allow Installment Payments')
                            ->default(false)
                            ->live()
                            ->helperText('Enable clients to pay in multiple installments'),

                        Forms\Components\TextInput::make('minimum_deposit_percentage')
                            ->label('Minimum Deposit (%)')
                            ->numeric()
                            ->default(30)
                            ->minValue(1)
                            ->maxValue(100)
                            ->suffix('%')
                            ->visible(fn (Forms\Get $get) => $get('allow_installments'))
                            ->required(fn (Forms\Get $get) => $get('allow_installments'))
                            ->helperText('Minimum percentage clients must pay as first deposit'),

                        Forms\Components\Textarea::make('installment_instructions')
                            ->label('Installment Instructions')
                            ->rows(3)
                            ->visible(fn (Forms\Get $get) => $get('allow_installments'))
                            ->helperText('Instructions shown to clients about payment installments')
                            ->placeholder('Example: Pay minimum 30% deposit to secure your spot. Complete payment before event date.')
                            ->columnSpanFull(),

                        Forms\Components\ColorPicker::make('color')
                            ->label('QR Code Color')
                            ->nullable()
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->columnSpan(1),
                    ])
                    ->columns(4)
                    ->defaultItems(1)
                    ->addActionLabel('Add Another Tier')
                    ->collapsible()
                    ->itemLabel(fn (array $state): ?string => $state['tier_name'] ?? 'New Tier')
                    ->reorderableWithButtons()
                    ->columnSpanFull(),
            ]);

        // ============================================================================
        // STEP 4: Review & Publish
        // ============================================================================
        $steps[] = Step::make('Review & Publish')
            ->icon('heroicon-o-eye')
            ->description('Review before publishing')
            ->schema([
                Forms\Components\Select::make('status')
                    ->label('Event Status')
                    ->options([
                        'draft' => 'Draft - Save but don\'t publish',
                        'published' => 'Published - Make visible to public',
                    ])
                    ->default('draft')
                    ->required()
                    ->helperText('You can change this later')
                    ->columnSpanFull(),

                Forms\Components\Placeholder::make('review')
                    ->label('Event Summary')
                    ->content(function (Forms\Get $get) {
                        $name = $get('name') ?? 'Not set';
                        $date = $get('event_date') ?? 'Not set';
                        $venue = $get('venue') ?? 'Not set';
                        $tiers = $get('tiers') ?? [];
                        $tierCount = count($tiers);

                        return view('filament.components.event-summary', [
                            'name' => $name,
                            'date' => $date,
                            'venue' => $venue,
                            'tierCount' => $tierCount,
                            'tiers' => $tiers,
                        ]);
                    })
                    ->columnSpanFull(),
            ]);

        return $steps;
    }

    /**
     * Prepare data before saving
     */
    protected function mutateFormDataBeforeCreate(array $data): array
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

    protected function afterCreate(): void
    {
        $event = $this->record;

        Notification::make()
            ->title('Event Created Successfully!')
            ->body("Event '{$event->name}' has been created with " . $event->tiers()->count() . " tier(s).")
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}
