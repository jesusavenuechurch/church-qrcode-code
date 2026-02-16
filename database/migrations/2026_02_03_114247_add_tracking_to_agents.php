<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            // Performance tracking
            $table->integer('total_paid_organizations')->default(0)
                ->after('is_active')
                ->comment('Count of organizations that made at least 1 payment');
            
            $table->integer('last_milestone_tier')->default(0)
                ->after('total_paid_organizations')
                ->comment('Last milestone tier reached (0=none, 1=5orgs, 2=10orgs, etc.)');
            
            $table->decimal('total_commissions_earned', 10, 2)->default(0)
                ->after('last_milestone_tier')
                ->comment('Total event commissions earned');
            
            $table->decimal('total_bonuses_earned', 10, 2)->default(0)
                ->after('total_commissions_earned')
                ->comment('Total milestone bonuses earned');
            
            $table->decimal('total_earnings', 10, 2)->default(0)
                ->after('total_bonuses_earned')
                ->comment('Total commissions + bonuses');
            
            $table->index('total_paid_organizations');
            $table->index('last_milestone_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn([
                'total_paid_organizations',
                'last_milestone_tier',
                'total_commissions_earned',
                'total_bonuses_earned',
                'total_earnings',
            ]);
        });
    }
};