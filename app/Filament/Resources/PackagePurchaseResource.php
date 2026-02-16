<?php

namespace App\Filament\Resources;

use App\Models\OrganizationPackage;
use App\Models\AgentEarning;
use App\Models\Organization;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Alignment;

class PackagePurchaseResource extends Resource
{
    protected static ?string $model = OrganizationPackage::class;
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'My Packages';
    protected static ?string $modelLabel = 'Package';

    public static function canCreate(): bool { return false; }

    public static function canViewAny(): bool
    {
        $user = auth()->user();
        if (!$user) return false;

        // Only allow Super Admins OR people assigned to an Organization
        // This automatically blocks Sales Agents because they have no organization_id
        return $user->isSuperAdmin() || $user->organization_id !== null;
    }

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        if ($user->isSuperAdmin()) {
            return $query;
        }

        // Safety: If for some reason the user has no org_id, return nothing (empty query)
        if (!$user->organization_id) {
            return $query->whereRaw('1 = 0'); 
        }

        return $query->where('organization_id', $user->organization_id);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort(function (Builder $query) {
                if (auth()->user()?->isSuperAdmin()) {
                    return $query->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")->orderBy('created_at', 'desc');
                }
                return $query->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")->orderBy('purchased_at', 'desc');
            })
            ->columns([
                Tables\Columns\TextColumn::make('organization.name')
                    ->label('Organization')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->visible(fn () => auth()->user()?->isSuperAdmin()),

                Tables\Columns\TextColumn::make('package_type')
                    ->label('Intelligence Plan')
                    ->formatStateUsing(fn ($record) => $record->is_free_trial ? '🎁 FREE TRIAL' : strtoupper($record->display_name))
                    ->extraAttributes(['class' => 'tracking-tighter font-black'])
                    ->badge()
                    ->color(fn ($record) => $record->is_free_trial ? 'success' : match($record->package_type) {
                        'starter' => 'info',
                        'standard' => 'success',
                        'multi_event' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('events_status')
                    ->label('Slots')
                    ->getStateUsing(fn ($record) => "{$record->events_used} / {$record->events_included}")
                    ->icon('heroicon-m-cpu-chip')
                    ->badge()
                    ->color(fn ($record) => $record->events_used < $record->events_included ? 'success' : 'gray'),

                Tables\Columns\TextColumn::make('tickets_status')
                    ->label('Throughput')
                    ->html()
                    ->getStateUsing(function ($record) {
                        $rawPct = ($record->tickets_included > 0) ? ($record->tickets_used / $record->tickets_included) * 100 : 0;
                        $visualPct = min($rawPct, 100); 
                        $color = $rawPct >= 100 ? '#ef4444' : ($rawPct >= 80 ? '#F07F22' : '#10B981');
                        return new HtmlString("<div class='w-full max-w-[200px] py-1'><div class='flex justify-between items-end mb-1'><span class='text-[9px] font-black text-gray-400 uppercase tracking-widest'>Usage</span><span class='text-[10px] font-bold' style='color: {$color}'>" . number_format($rawPct, 0) . "%</span></div><div class='w-full bg-gray-100 dark:bg-white/5 rounded-full h-1 overflow-hidden'><div class='h-full rounded-full transition-all duration-1000' style='width: {$visualPct}%; background: {$color};'></div></div></div>");
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => strtoupper($state))
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'pending' => 'warning',
                        'exhausted' => 'danger',
                        'expired' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('price_paid')
                    ->label('Value')
                    ->money('LSL')
                    ->alignment(Alignment::Right)
                    ->weight(FontWeight::Black),

                Tables\Columns\TextColumn::make('payment_reference')
                    ->label('Verification')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->visible(fn () => auth()->user()?->isSuperAdmin()),
            ])
            ->headerActions([
                Tables\Actions\Action::make('start_free_trial')
                    ->label('Start Free Trial')
                    ->icon('heroicon-o-gift')
                    ->color('success')
                    ->button()
                    ->visible(fn () => !auth()->user()->isSuperAdmin() && !OrganizationPackage::where('organization_id', auth()->user()->organization_id)->exists())
                    ->action(function () {
                        OrganizationPackage::createFreeTrialPackage(auth()->user()->organization_id);
                        Notification::make()->title('Free Trial Activated!')->success()->send();
                    }),

                // ✅ RESTORED HIGH-FIDELITY UPGRADE ACTION
                Tables\Actions\Action::make('purchase_package')
                    ->label('Upgrade Capacity')
                    ->icon('heroicon-o-arrow-trending-up')
                    ->color('primary')
                    ->button()
                    ->visible(fn () => !auth()->user()->isSuperAdmin())
                    ->modalWidth('4xl')
                    ->modalHeading('System Capacity Upgrade')
                    ->form([
                        Forms\Components\Radio::make('package_type')
                            ->label('Select Deployment Tier')
                            ->options([
                                'starter' => 'STARTER',
                                'standard' => 'STANDARD',
                                'multi_event' => 'MULTI-EVENT',
                            ])
                            ->descriptions([
                                'starter' => '50 Ticket limit. Single event use.',
                                'standard' => '300 Ticket limit. High-performance event sync.',
                                'multi_event' => '1,000 Ticket limit. Manage 3 events simultaneously.',
                            ])
                            ->default('standard')
                            ->columns(3)
                            ->required()
                            ->live(),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Section::make('Tier Specifications')
                                ->columnSpan(1)
                                ->schema([
                                    Forms\Components\Placeholder::make('summary')
                                        ->content(function (Forms\Get $get) {
                                            $type = $get('package_type');
                                            if (!$type) return new HtmlString('<p class="text-xs italic">Select a tier...</p>');
                                            $def = OrganizationPackage::getPackageDefinitions()[$type];
                                            return new HtmlString("
                                                <div class='p-4 bg-gray-50 dark:bg-white/5 rounded-2xl border border-gray-100 dark:border-white/10 space-y-2'>
                                                    <div class='flex justify-between'><span class='text-[10px] font-bold uppercase text-gray-400'>Slots</span><span class='font-bold text-[#1D4069] dark:text-white'>{$def['events']} Available</span></div>
                                                    <div class='flex justify-between'><span class='text-[10px] font-bold uppercase text-gray-400'>Capacity</span><span class='font-bold text-[#1D4069] dark:text-white'>{$def['tickets']} Tickets</span></div>
                                                    <div class='pt-2 border-t border-gray-200 dark:border-white/10 flex justify-between items-baseline'>
                                                        <span class='text-xs font-black'>COST</span>
                                                        <span class='text-2xl font-black text-[#F07F22]'>M" . number_format($def['price'], 2) . "</span>
                                                    </div>
                                                </div>
                                            ");
                                        }),
                                ]),

                            Forms\Components\Section::make('Payment Verification')
                                ->columnSpan(1)
                                ->schema([
                                    Forms\Components\Select::make('payment_method')
                                        ->options(['m_pesa' => 'M-Pesa', 'ecocash' => 'EcoCash', 'bank_transfer' => 'Bank Transfer'])
                                        ->required(),
                                    Forms\Components\TextInput::make('payment_reference')
                                        ->label('Reference ID')
                                        ->required(),
                                ]),
                        ]),
                    ])
                    ->action(function (array $data) {
                        $packageDef = OrganizationPackage::getPackageDefinitions()[$data['package_type']];
                        OrganizationPackage::create([
                            'organization_id' => auth()->user()->organization_id,
                            'package_type' => $data['package_type'],
                            'price_paid' => $packageDef['price'],
                            'events_included' => $packageDef['events'],
                            'tickets_included' => $packageDef['tickets'],
                            'status' => 'pending',
                            'purchased_at' => now(),
                            'payment_method' => $data['payment_method'],
                            'payment_reference' => $data['payment_reference'],
                            'purchased_by' => auth()->id(),
                        ]);
                        Notification::make()->title('Purchase Submitted')->success()->send();
                    })
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('approve_package')
                        ->label('Approve')
                        ->icon('heroicon-m-check-badge')
                        ->color('success')
                        ->visible(fn ($record) => $record->status === 'pending' && auth()->user()?->isSuperAdmin())
                        ->requiresConfirmation()
                        ->action(function (OrganizationPackage $record) {
                            OrganizationPackage::where('organization_id', $record->organization_id)
                                ->where('id', '!=', $record->id)
                                ->where('status', 'active')
                                ->update(['status' => 'expired']);

                            $record->update(['status' => 'active']);

                            $org = $record->organization;
                            if ($org && $org->agent_id) {
                                $commission = AgentEarning::calculateCommission($record->price_paid);
                                AgentEarning::create([
                                    'agent_id' => $org->agent_id,
                                    'organization_id' => $org->id,
                                    'organization_package_id' => $record->id,
                                    'type' => 'commission',
                                    'amount' => $commission,
                                    'package_price' => $record->price_paid,
                                    'package_type' => $record->package_type,
                                    'status' => 'approved',
                                    'approved_by' => auth()->id(),
                                    'approved_at' => now(),
                                    'notes' => "Commission for {$org->name}",
                                ]);

                                $paidCount = Organization::where('agent_id', $org->agent_id)
                                    ->whereHas('activePackages', fn($q) => $q->where('is_free_trial', false)->where('status', 'active'))
                                    ->count();

                                if ($paidCount > 0 && $paidCount % 5 === 0) {
                                    $tier = AgentEarning::getMilestoneTier($paidCount);
                                    AgentEarning::firstOrcreate([
                                        'organization_package_id' => $record->id, 'type' => 'commission'],
                                        ['agent_id' => $org->agent_id,
                                        'type' => 'milestone_bonus',
                                        'amount' => AgentEarning::calculateMilestoneBonus($tier),
                                        'milestone_tier' => $tier,
                                        'milestone_org_count' => $paidCount,
                                        'status' => 'approved',
                                        'approved_by' => auth()->id(),
                                        'approved_at' => now(),
                                    ]);
                                }
                            }
                            Notification::make()->title('Approved & Commission Allocated')->success()->send();
                        }),
                    Tables\Actions\DeleteAction::make()->visible(fn () => auth()->user()?->isSuperAdmin()),
                ])
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => \App\Filament\Resources\PackagePurchaseResource\Pages\ListPackagePurchases::route('/')];
    }
}