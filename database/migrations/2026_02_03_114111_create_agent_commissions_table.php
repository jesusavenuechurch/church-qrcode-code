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
        Schema::create('agent_commissions', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('agent_id')->constrained()->cascadeOnDelete();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            
            $table->enum('commission_type', ['event', 'milestone_bonus'])
                ->default('event')
                ->comment('Type of commission');
            
            $table->decimal('amount', 10, 2)
                ->comment('Commission amount in Maloti');
            
            $table->decimal('package_price', 10, 2)->nullable()
                ->comment('Original package price');
            
            $table->string('package_type')->nullable()
                ->comment('starter, standard, multi_event, enterprise');
            
            $table->enum('status', ['pending', 'approved', 'paid', 'cancelled'])
                ->default('pending')
                ->comment('Commission payment status');
            
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable()
                ->comment('mobile_money, bank_transfer, etc.');
            $table->string('payment_reference')->nullable();
            
            $table->text('notes')->nullable();
            
            $table->timestamps();
            
            // Indexes
            $table->index('status');
            $table->index('commission_type');
            $table->index(['agent_id', 'status']);
            $table->index(['organization_id', 'commission_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_commissions');
    }
};