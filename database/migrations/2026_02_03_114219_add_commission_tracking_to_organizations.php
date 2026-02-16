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
        Schema::table('organizations', function (Blueprint $table) {
            // Track commission payments per organization
            $table->integer('agent_commission_packages_count')->default(0)
                ->after('registration_source')
                ->comment('Number of events where agent commission was paid');
            
            $table->integer('agent_commission_packages_limit')->default(3)
                ->after('agent_commission_packages_count')
                ->comment('Maximum events eligible for commission (default: 3)');
            
            $table->decimal('total_agent_commission_paid', 10, 2)->default(0)
                ->after('agent_commission_packages_limit')
                ->comment('Total commission paid to agent for this org');
            
            $table->timestamp('first_payment_at')->nullable()
                ->after('total_agent_commission_paid')
                ->comment('When organization made first payment');
            
            $table->index('agent_id');
            $table->index('agent_commission_packages_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn([
                'agent_commission_packages_count',
                'agent_commission_packages_limit',
                'total_agent_commission_paid',
                'first_payment_at',
            ]);
        });
    }
};