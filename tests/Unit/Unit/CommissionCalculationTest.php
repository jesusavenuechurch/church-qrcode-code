<?php

use App\Models\AgentEarning;

describe('Commission Calculation — Pure Math', function () {

    it('calculates exactly 20% on standard package', function () {
        expect(AgentEarning::calculateCommission(700))->toBe(140.0);
    });

    it('calculates exactly 20% on starter package', function () {
        expect(AgentEarning::calculateCommission(250))->toBe(50.0);
    });

    it('calculates exactly 20% on multi-event package', function () {
        expect(AgentEarning::calculateCommission(1500))->toBe(300.0);
    });

    it('returns zero commission on free trial', function () {
        expect(AgentEarning::calculateCommission(0))->toBe(0.0);
    });

    it('calculates milestone bonus tier 1 correctly', function () {
        expect(AgentEarning::calculateMilestoneBonus(1))->toBe(300.0);
    });

    it('calculates milestone bonus tier 2 correctly', function () {
        expect(AgentEarning::calculateMilestoneBonus(2))->toBe(320.0);
    });

    it('calculates milestone bonus tier 3 correctly', function () {
        expect(AgentEarning::calculateMilestoneBonus(3))->toBe(340.0);
    });

    it('correctly identifies milestone tier from org count', function () {
        expect(AgentEarning::getMilestoneTier(5))->toBe(1);
        expect(AgentEarning::getMilestoneTier(10))->toBe(2);
        expect(AgentEarning::getMilestoneTier(15))->toBe(3);
        expect(AgentEarning::getMilestoneTier(3))->toBeNull(); // Not a milestone
    });

});