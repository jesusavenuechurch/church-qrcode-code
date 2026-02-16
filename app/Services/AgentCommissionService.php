<?php
// app/Services/AgentCommissionService.php

namespace App\Services;

use App\Models\Organization;
use App\Models\Agent;
use App\Models\AgentCommission;
use App\Models\AgentBonus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AgentCommissionService
{
    /**
     * Process commission when organization makes a payment
     * 
     * @param Organization $organization
     * @param float $packagePrice - e.g., 250, 700, 1500
     * @param string $packageType - 'starter', 'standard', 'multi_event'
     * @param int|null $eventId - Optional event ID
     * @return array - ['commission_created' => bool, 'bonus_created' => bool, 'commission' => AgentCommission|null, 'bonus' => AgentBonus|null]
     */
    public static function processPayment(
        Organization $organization,
        float $packagePrice,
        string $packageType,
        ?int $eventId = null
    ): array {
        try {
            DB::beginTransaction();

            $result = [
                'commission_created' => false,
                'bonus_created' => false,
                'commission' => null,
                'bonus' => null,
            ];

            // Check if organization was referred by an agent
            if (!$organization->wasReferredByAgent()) {
                Log::info("Organization {$organization->id} not referred by agent - no commission");
                DB::commit();
                return $result;
            }

            // Mark first payment if this is it
            if ($organization->isFirstPayment()) {
                $organization->markFirstPayment();
                Log::info("Marked first payment for organization {$organization->id}");
            }

            // Check if agent can still earn commission from this org
            if (!$organization->canEarnAgentCommission()) {
                Log::info("Organization {$organization->id} reached commission limit ({$organization->agent_commission_events_count}/{$organization->agent_commission_events_limit})");
                DB::commit();
                return $result;
            }

            // Calculate commission (20%)
            $commissionAmount = round($packagePrice * 0.20, 2);

            // Create commission record
            $commission = $organization->recordAgentCommission($commissionAmount, [
                'event_id' => $eventId,
                'package_price' => $packagePrice,
                'package_type' => $packageType,
            ]);

            if ($commission) {
                $result['commission_created'] = true;
                $result['commission'] = $commission;
                
                Log::info("Commission created for agent {$organization->agent_id}: M{$commissionAmount} ({$organization->agent_commission_events_count}/{$organization->agent_commission_events_limit} events)");
            }

            // Check for milestone bonus
            $bonus = self::checkAndCreateMilestoneBonus($organization->agent);
            
            if ($bonus) {
                $result['bonus_created'] = true;
                $result['bonus'] = $bonus;
                
                Log::info("Milestone bonus created for agent {$organization->agent_id}: M{$bonus->bonus_amount} (Tier {$bonus->milestone_tier})");
            }

            DB::commit();
            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Commission processing error: {$e->getMessage()}", [
                'organization_id' => $organization->id,
                'package_price' => $packagePrice,
                'package_type' => $packageType,
            ]);
            
            throw $e;
        }
    }

    /**
     * Check if agent reached a milestone and create bonus
     * 
     * @param Agent $agent
     * @return AgentBonus|null
     */
    protected static function checkAndCreateMilestoneBonus(Agent $agent): ?AgentBonus
    {
        $paidOrgsCount = $agent->total_paid_organizations;
        
        // Check if at a milestone (5, 10, 15, 20, etc.)
        if ($paidOrgsCount % 5 !== 0 || $paidOrgsCount === 0) {
            return null;
        }

        $currentTier = (int) floor($paidOrgsCount / 5);
        $lastTier = $agent->last_milestone_tier;

        // Check if this is a NEW milestone
        if ($currentTier <= $lastTier) {
            return null;
        }

        // Calculate bonus amount: 300 + ((tier - 1) × 20)
        $bonusAmount = AgentBonus::calculateBonusAmount($currentTier);

        // Check if bonus already exists for this tier (safety check)
        $existingBonus = AgentBonus::where('agent_id', $agent->id)
            ->where('milestone_tier', $currentTier)
            ->first();

        if ($existingBonus) {
            Log::warning("Bonus already exists for agent {$agent->id} tier {$currentTier}");
            return null;
        }

        // Create bonus
        $bonus = AgentBonus::create([
            'agent_id' => $agent->id,
            'milestone_tier' => $currentTier,
            'organizations_count' => $paidOrgsCount,
            'bonus_amount' => $bonusAmount,
            'status' => 'pending',
        ]);

        // Update agent's last milestone tier
        $agent->update(['last_milestone_tier' => $currentTier]);

        return $bonus;
    }

    /**
     * Get package pricing
     * Used when you implement package selection
     * 
     * @return array
     */
    public static function getPackagePricing(): array
    {
        return [
            'starter' => [
                'name' => 'Starter Event',
                'price' => 250,
                'tickets' => 50,
                'events' => 1,
                'commission_rate' => 0.20,
            ],
            'standard' => [
                'name' => 'Standard Event',
                'price' => 700,
                'tickets' => 300,
                'events' => 1,
                'commission_rate' => 0.20,
            ],
            'multi_event' => [
                'name' => 'Multi-Event Pack',
                'price' => 1500,
                'tickets' => 1000,
                'events' => 3,
                'commission_rate' => 0.20,
            ],
            'enterprise' => [
                'name' => 'Enterprise Plan',
                'price' => 5000,
                'tickets' => null, // unlimited
                'events' => null, // unlimited
                'commission_rate' => 0.20,
            ],
        ];
    }

    /**
     * Calculate commission for a package
     * 
     * @param string $packageType
     * @return float
     */
    public static function calculateCommission(string $packageType): float
    {
        $packages = self::getPackagePricing();
        
        if (!isset($packages[$packageType])) {
            return 0;
        }

        $price = $packages[$packageType]['price'];
        $rate = $packages[$packageType]['commission_rate'];

        return round($price * $rate, 2);
    }

    /**
     * Get agent performance summary
     * 
     * @param Agent $agent
     * @return array
     */
    public static function getAgentPerformance(Agent $agent): array
    {
        return [
            'total_referrals' => $agent->organizations()->count(),
            'paid_organizations' => $agent->total_paid_organizations,
            'commission_events_count' => $agent->organizations()->sum('agent_commission_events_count'),
            'total_commissions' => $agent->commissions()->sum('amount'),
            'paid_commissions' => $agent->commissions()->paid()->sum('amount'),
            'pending_commissions' => $agent->commissions()->pending()->sum('amount'),
            'total_bonuses' => $agent->bonuses()->sum('bonus_amount'),
            'paid_bonuses' => $agent->bonuses()->paid()->sum('bonus_amount'),
            'pending_bonuses' => $agent->bonuses()->pending()->sum('bonus_amount'),
            'total_earnings' => $agent->total_earnings,
            'milestone_progress' => $agent->milestone_progress,
            'next_bonus_amount' => $agent->next_bonus_amount,
        ];
    }

    /**
     * Get commission summary for organization
     * 
     * @param Organization $organization
     * @return array
     */
    public static function getOrganizationCommissionSummary(Organization $organization): array
    {
        return [
            'has_agent' => $organization->wasReferredByAgent(),
            'agent_name' => $organization->agent?->name,
            'can_earn_commission' => $organization->canEarnAgentCommission(),
            'commission_events_count' => $organization->agent_commission_events_count,
            'commission_events_limit' => $organization->agent_commission_events_limit,
            'remaining_commission_events' => $organization->remaining_commission_events,
            'total_commission_paid' => $organization->total_agent_commission_paid,
            'first_payment_at' => $organization->first_payment_at,
        ];
    }
}