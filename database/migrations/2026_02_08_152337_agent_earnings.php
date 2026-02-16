<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('organization_package_id')->nullable()->constrained()->nullOnDelete();
            
            $table->enum('type', ['commission', 'milestone_bonus']);
            $table->decimal('amount', 10, 2);
            
            // For commissions (20% of package)
            $table->decimal('package_price', 10, 2)->nullable()
                ->comment('Original package price');
            $table->string('package_type')->nullable()
                ->comment('starter, standard, multi_event');
            
            // For milestone bonuses
            $table->integer('milestone_tier')->nullable()
                ->comment('1=5orgs, 2=10orgs, 3=15orgs, etc.');
            $table->integer('milestone_org_count')->nullable()
                ->comment('Number of paid orgs when bonus earned');
            
            // Payment tracking
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['agent_id', 'status']);
            $table->index(['agent_id', 'type']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_earnings');
    }
};