<?php

namespace App\Filament\Resources\EventResource\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Components\Wizard\Step;

class CreateEventWizard extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-plus';
    protected static ?string $navigationLabel = 'Create Event';
    protected static ?string $navigationGroup = 'Events';
    protected static string $view = 'filament.pages.create-event-wizard';

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Wizard::make([
                Step::make('Event Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required(),

                        Forms\Components\DateTimePicker::make('event_date')
                            ->required(),

                        Forms\Components\TextInput::make('venue'),

                        Forms\Components\Textarea::make('description')
                            ->rows(4),
                    ]),

                Step::make('Ticket Tiers')
                    ->schema([
                        Forms\Components\Repeater::make('tiers')
                            ->schema([
                                Forms\Components\TextInput::make('name')->required(),
                                Forms\Components\TextInput::make('price')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\TextInput::make('quantity')
                                    ->numeric()
                                    ->required(),
                            ])
                            ->minItems(1),
                    ]),

                Step::make('Publish')
                    ->schema([
                        Forms\Components\Toggle::make('publish_now')
                            ->label('Publish event immediately')
                            ->default(true),
                    ]),
            ])
            ->submitAction(
                Forms\Components\Actions\Action::make('create')
                    ->label('Create Event')
                    ->action('submit')
            ),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();

        // 1️⃣ Create Event
        $event = Event::create([
            'organization_id' => auth()->user()->organization_id,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'event_date' => $data['event_date'],
            'venue' => $data['venue'],
            'description' => $data['description'],
            'status' => $data['publish_now'] ? 'published' : 'draft',
        ]);

        // 2️⃣ Create Tiers
        foreach ($data['tiers'] as $tier) {
            $event->tiers()->create($tier);
        }

        // 3️⃣ Redirect
        $this->redirect(route('filament.admin.resources.events.view', $event));
    }
}