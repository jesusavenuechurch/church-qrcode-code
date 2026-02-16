<?php

namespace App\Filament\Resources;

use App\Models\ContactInquiry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use App\Filament\Resources\ContactInquiryResource\Pages;
use Illuminate\Support\HtmlString;

class ContactInquiryResource extends Resource
{
    protected static ?string $model = ContactInquiry::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-bottom-center-text';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Contact Inquiries';
    protected static ?int $navigationSort = 1;

public static function canViewAny(): bool 
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function canCreate(): bool 
    {
        return false; 
    }

    public static function canEdit($record): bool 
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function canDelete($record): bool 
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Inquiry Content')
                ->description('Client transmission details')
                ->schema([
                    Forms\Components\Grid::make(3)->schema([
                        Forms\Components\Placeholder::make('name')
                            ->content(fn ($record) => $record->name),
                        Forms\Components\Placeholder::make('phone')
                            ->content(fn ($record) => $record->phone),
                        Forms\Components\Placeholder::make('email')
                            ->content(fn ($record) => $record->email ?? 'N/A'),
                    ]),
                    Forms\Components\Placeholder::make('subject')
                        ->label('Nature of Inquiry')
                        ->content(fn ($record) => new HtmlString("<strong>{$record->subject}</strong>")),
                    
                    Forms\Components\Placeholder::make('message')
                        ->label('Message Detail')
                        ->content(fn ($record) => new HtmlString('<div class="p-4 bg-gray-50 rounded-xl border border-gray-100">' . nl2br(e($record->message)) . '</div>'))
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Management')
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'new' => 'New',
                            'read' => 'Read',
                            'replied' => 'Replied',
                            'archived' => 'Archived',
                        ])
                        ->native(false)
                        ->required(),
                    Forms\Components\Textarea::make('notes')
                        ->label('Internal Admin Notes')
                        ->placeholder('Add follow-up notes here...')
                        ->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->description(fn($record) => $record->subject)
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('phone')
                    ->label('Contact')
                    ->searchable()
                    ->copyable()
                    ->icon('heroicon-m-phone')
                    ->color('success'),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'danger' => 'new',
                        'info' => 'read',
                        'success' => 'replied',
                        'gray' => 'archived',
                    ]),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Received')
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                // QUICK ACTION: WhatsApp Reply
                Tables\Actions\Action::make('whatsapp_reply')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('success')
                    ->url(fn ($record) => "https://wa.me/" . preg_replace('/\D/', '', $record->phone) . "?text=" . urlencode("Hi {$record->name}, this is VENTIQ Support regarding your inquiry about '{$record->subject}'..."))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('mark_read')
                    ->label('Read')
                    ->icon('heroicon-m-check')
                    ->color('info')
                    ->visible(fn ($record) => $record->status === 'new')
                    ->action(function ($record) {
                        $record->update(['status' => 'read']);
                        Notification::make()->title('Marked as read')->success()->send();
                    }),

                Tables\Actions\EditAction::make()->slideOver(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'read' => 'Read',
                        'replied' => 'Replied',
                        'archived' => 'Archived',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactInquiries::route('/'),
            'edit' => Pages\EditContactInquiry::route('/{record}/edit'),
        ];
    }
}