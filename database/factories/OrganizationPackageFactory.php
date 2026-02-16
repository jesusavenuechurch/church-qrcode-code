<?php

namespace Database\Factories;

use App\Models\OrganizationPackage;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrganizationPackageFactory extends Factory
{
    protected $model = OrganizationPackage::class;

    public function definition(): array
    {
        // Default: standard pending package
        return [
            'package_type'          => 'standard',
            'price_paid'            => 700.00,
            'events_included'       => 1,
            'events_used'           => 0,
            'tickets_included'      => 300,
            'tickets_used'          => 0,
            'comp_tickets_included' => 40,
            'comp_tickets_used'     => 0,
            'overage_ticket_rate'   => 8.00,
            'status'                => 'pending',
            'is_free_trial'         => false,
            'agent_commission_processed' => false,
            'purchased_at'          => now(),
            'payment_method'        => 'mpesa',
            'payment_reference'     => 'MP' . fake()->numerify('########'),
        ];
    }

    // ── States — match actual getPackageDefinitions() ──

    public function starter(): static
    {
        return $this->state([
            'package_type'          => 'starter',
            'price_paid'            => 250.00,
            'events_included'       => 1,
            'tickets_included'      => 50,
            'comp_tickets_included' => 12,
            'overage_ticket_rate'   => 10.00,
        ]);
    }

    public function standard(): static
    {
        return $this->state([
            'package_type'          => 'standard',
            'price_paid'            => 700.00,
            'events_included'       => 1,
            'tickets_included'      => 300,
            'comp_tickets_included' => 40,
            'overage_ticket_rate'   => 8.00,
        ]);
    }

    public function multiEvent(): static
    {
        return $this->state([
            'package_type'          => 'multi_event',
            'price_paid'            => 1500.00,
            'events_included'       => 3,
            'tickets_included'      => 1000,
            'comp_tickets_included' => 150,
            'overage_ticket_rate'   => 6.00,
        ]);
    }

    // ── Status states ──

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function pending(): static
    {
        return $this->state(['status' => 'pending']);
    }

    public function expired(): static
    {
        return $this->state([
            'status'     => 'expired',
            'expires_at' => now()->subDay(),
        ]);
    }

    public function exhausted(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'status'       => 'exhausted',
                'events_used'  => $attributes['events_included'],
                'tickets_used' => $attributes['tickets_included'],
            ];
        });
    }

    // ── Free trial — matches createFreeTrialPackage() exactly ──

    public function freeTrial(): static
    {
        return $this->state([
            'package_type'          => 'free_trial',
            'price_paid'            => 0.00,
            'events_included'       => 1,
            'events_used'           => 0,
            'tickets_included'      => 300,
            'comp_tickets_included' => 40,
            'overage_ticket_rate'   => 8.00,
            'status'                => 'active',
            'is_free_trial'         => true,
            'payment_method'        => null,
            'payment_reference'     => null,
        ]);
    }

    // ── Near capacity ──

    public function nearCapacity(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'tickets_used' => (int) ($attributes['tickets_included'] * 0.9),
            ];
        });
    }

    public function commissionProcessed(): static
    {
        return $this->state(['agent_commission_processed' => true]);
    }
}