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
        Schema::create('agent_bonuses', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            
            $table->integer('milestone_tier')
                ->comment('Which tier bonus (1=5orgs, 2=10orgs, 3=15orgs, etc.)');
            
            $table->integer('organizations_count')
                ->comment('Number of paid orgs when bonus was earned');
            
            $table->decimal('bonus_amount', 10, 2)
                ->comment('Bonus amount: 300 + ((tier-1) * 20)');
            
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])
                ->default('pending');
            
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index(['agent_id', 'milestone_tier']);
            $table->index(['agent_id', 'status']);
            
            // Prevent duplicate bonuses for same tier
            $table->unique(['agent_id', 'milestone_tier']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_bonuses');
    }
};