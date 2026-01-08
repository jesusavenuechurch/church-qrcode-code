<?php

namespace App\Filament\Resources;

use App\Models\Event;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\EventResource\Pages;
use Illuminate\Support\Str;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Events';
    protected static ?string $navigationLabel = 'Events';

    /* ------------------------------------------------------------
     | Permissions
     ------------------------------------------------------------ */

    public static function canViewAny(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'view_event',
            'create_event',
        ]) ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->hasPermissionTo('create_event') ?? false;
    }

    public static function canEdit(Model $record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return $user->hasPermissionTo('edit_event');
        }

        return $user->hasPermissionTo('edit_event')
            && $record->organization_id === $user->organization_id;
    }

    public static function canDelete(Model $record): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return $user->hasPermissionTo('delete_event');
        }

        return $user->hasPermissionTo('delete_event')
            && $record->organization_id === $user->organization_id;
    }

    /* ------------------------------------------------------------
     | Query Scope
     ------------------------------------------------------------ */

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = auth()->user();

        if ($user?->isSuperAdmin()) {
            return $query;
        }

        if ($user?->organization_id) {
            return $query->where('organization_id', $user->organization_id);
        }

        return $query->whereNull('id');
    }

    /* ------------------------------------------------------------
     | Form
     ------------------------------------------------------------ */

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Internal UX flags (not persisted)
            Forms\Components\Hidden::make('slug_locked')->default(false),
            Forms\Components\Hidden::make('tagline_locked')->default(false),

            Forms\Components\Section::make('Basic Information')
                ->schema([
                    Forms\Components\Select::make('organization_id')
                        ->relationship('organization', 'name')
                        ->required()
                        ->searchable()
                        ->disabled(fn () => ! auth()->user()?->isSuperAdmin())
                        ->default(auth()->user()?->organization_id)
                        ->dehydrated(true),

                    Forms\Components\TextInput::make('name')
                        ->label('Event Name')
                        ->required()
                        ->live(debounce: 500)
                        ->afterStateUpdated(function ($state, callable $set, callable $get) {
                            if (! $get('slug_locked')) {
                                $set('slug', Str::slug($state));
                            }

                            if (! $get('tagline_locked')) {
                                $set('tagline', $state);
                            }
                        }),

                    Forms\Components\TextInput::make('slug')
                        ->label('Public URL Slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->afterStateUpdated(fn ($set) => $set('slug_locked', true))
                        ->helperText('Auto-generated from event name, editable'),

                    Forms\Components\Toggle::make('is_public')
                        ->label('Public Event')
                        ->default(true),
                ])
                ->columns(2),

            Forms\Components\Section::make('Schedule')
                ->schema([
                    Forms\Components\DateTimePicker::make('event_date')
                        ->label('Event Date & Time')
                        ->required(),

                    Forms\Components\DateTimePicker::make('registration_deadline')
                        ->label('Registration Deadline')
                        ->nullable()
                        ->before('event_date'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Location')
                ->schema([
                    Forms\Components\TextInput::make('venue')
                        ->label('Venue Name')
                        ->maxLength(255),

                    Forms\Components\Textarea::make('location')
                        ->label('Full Address')
                        ->rows(3)
                        ->maxLength(500),
                ]),

            Forms\Components\Section::make('Capacity & Status')
                ->schema([
                    Forms\Components\TextInput::make('capacity')
                        ->numeric()
                        ->nullable(),

                    Forms\Components\Select::make('status')
                        ->options([
                            'draft' => 'Draft',
                            'published' => 'Published',
                            'live' => 'Live',
                            'closed' => 'Closed',
                        ])
                        ->default('draft'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Payment Options')
                ->description('Configure installment payment settings for this event')
                ->schema([
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
                ])
                ->collapsible()
                ->collapsed(),

            Forms\Components\Section::make('Description')
                ->schema([
                    Forms\Components\TextInput::make('tagline')
                        ->afterStateUpdated(fn ($set) => $set('tagline_locked', true))
                        ->helperText('Auto-generated from name, editable'),

                    Forms\Components\Textarea::make('description')
                        ->rows(4),
                ]),
        ]);
    }

    /* ------------------------------------------------------------
     | Table (OPTIMIZED - NO HORIZONTAL SCROLL)
     ------------------------------------------------------------ */

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('event_date')
            ->columns([
                // ALWAYS VISIBLE - Core Info (3 columns)
                Tables\Columns\TextColumn::make('name')
                    ->label('Event')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap()
                    ->limit(40),

                Tables\Columns\TextColumn::make('event_date')
                    ->label('Date')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->description(fn ($record) => $record->event_date?->format('g:i A')),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'gray' => 'draft',
                        'info' => 'published',
                        'success' => 'live',
                        'danger' => 'closed',
                    ]),

                // EXPANDABLE - Click column icon to show/hide
                Tables\Columns\IconColumn::make('is_public')
                    ->label('Public')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('allow_installments')
                    ->label('Installments')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->tooltip(fn ($record) => $record->allow_installments 
                        ? "Min deposit: {$record->minimum_deposit_percentage}%" 
                        : 'Installments disabled'),

                Tables\Columns\TextColumn::make('minimum_deposit_percentage')
                    ->label('Min Deposit')
                    ->suffix('%')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('venue')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(25)
                    ->tooltip(fn ($record) => $record->venue),

                Tables\Columns\TextColumn::make('capacity')
                    ->label('Capacity')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => $state ? number_format($state) : '∞'),

                Tables\Columns\TextColumn::make('registration_deadline')
                    ->label('Reg. Deadline')
                    ->dateTime('M d, Y')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('tiers_count')
                    ->label('Tiers')
                    ->counts('tiers')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('tickets_count')
                    ->label('Tickets')
                    ->counts('tickets')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('organization.name')
                    ->label('Organization')
                    ->visible(fn () => auth()->user()?->isSuperAdmin())
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
            ])

            ->actions([
                // PRIMARY ACTION
                Tables\Actions\Action::make('public_link')
                    ->label('View')
                    ->icon('heroicon-o-link')
                    ->color('primary')
                    ->visible(fn ($record) =>
                        $record->is_public &&
                        $record->slug &&
                        $record->organization?->slug
                    )
                    ->modalHeading('Public Event URL')
                    ->modalContent(fn ($record) => view(
                        'filament.modals.event-url',
                        [
                            'event' => $record,
                            'url' => $record->public_url,
                        ]
                    ))
                    ->modalSubmitAction(false),

                // SECONDARY
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('toggle_installments')
                        ->label(fn ($record) => $record->allow_installments ? 'Disable Installments' : 'Enable Installments')
                        ->icon('heroicon-o-banknotes')
                        ->color(fn ($record) => $record->allow_installments ? 'warning' : 'success')
                        ->requiresConfirmation()
                        ->action(function ($record) {
                            $newState = !$record->allow_installments;
                            $record->update(['allow_installments' => $newState]);
                            
                            Notification::make()
                                ->title($newState ? 'Installments Enabled' : 'Installments Disabled')
                                ->success()
                                ->send();
                        }),
                    
                    Tables\Actions\DeleteAction::make(),
                ])
                ->icon('heroicon-o-ellipsis-vertical'),
            ])

            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'live' => 'Live',
                        'closed' => 'Closed',
                    ]),
                
                Tables\Filters\TernaryFilter::make('allow_installments')
                    ->label('Installments')
                    ->placeholder('All events')
                    ->trueLabel('Enabled')
                    ->falseLabel('Disabled'),
                
                Tables\Filters\TernaryFilter::make('is_public')
                    ->label('Public')
                    ->placeholder('All events')
                    ->trueLabel('Public')
                    ->falseLabel('Private'),
            ])
            
            ->bulkActions([
                Tables\Actions\BulkAction::make('enable_installments')
                    ->label('Enable Installments')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        $records->each->update(['allow_installments' => true]);
                        
                        Notification::make()
                            ->title('Installments enabled for ' . $records->count() . ' event(s)')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\BulkAction::make('disable_installments')
                    ->label('Disable Installments')
                    ->icon('heroicon-o-x-circle')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function ($records) {
                        $records->each->update(['allow_installments' => false]);
                        
                        Notification::make()
                            ->title('Installments disabled for ' . $records->count() . ' event(s)')
                            ->success()
                            ->send();
                    }),
                
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    /* ------------------------------------------------------------
     | Pages
     ------------------------------------------------------------ */

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvents::route('/'),
            'create' => Pages\CreateEvent::route('/create'),
            'edit' => Pages\EditEvent::route('/{record}/edit'),
        ];
    }
}