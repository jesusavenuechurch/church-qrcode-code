<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Models\Agent;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrganizationFactory extends Factory
{
    protected $model = Organization::class;

    public function definition(): array
    {
        $name = fake()->company();

        return [
            'name'             => $name,
            'slug'             => Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'email'            => fake()->unique()->companyEmail(),
            'phone'            => '+266 5' . fake()->numerify('### ####'),
            'is_active'        => true,
            'registration_source' => 'direct',
            'agent_id'         => null,
            'registered_via_agent_at'         => null,
        ];
    }

    // ── States ──

    public function referredBy(Agent $agent): static
    {
        return $this->state([
            'agent_id'                => $agent->id,
            'registration_source'     => 'agent',
            'registered_via_agent_at' => now(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}