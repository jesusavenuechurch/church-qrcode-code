<?php
// database/migrations/2026_02_05_create_organization_packages_system.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. CREATE ORGANIZATION_PACKAGES TABLE
        Schema::create('organization_packages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            
            $table->enum('package_type', ['starter', 'standard', 'multi_event', 'free_trial']);
            $table->decimal('price_paid', 10, 2)->default(0);
            
            $table->integer('events_included');
            $table->integer('events_used')->default(0);
            
            $table->integer('tickets_included');
            $table->integer('tickets_used')->default(0);
            
            $table->integer('comp_tickets_included')->default(0);
            $table->integer('comp_tickets_used')->default(0);
            
            $table->decimal('overage_ticket_rate', 10, 2)->default(0);
            
            $table->enum('status', ['active', 'pending', 'exhausted', 'expired'])->default('active');
            
            $table->timestamp('purchased_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->foreignId('purchased_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->boolean('agent_commission_processed')->default(false);
            $table->boolean('is_free_trial')->default(false);
            $table->text('notes')->nullable();
            
            $table->timestamps();

            $table->index(['organization_id', 'status']);
        });

        // 2. CREATE PACKAGE_OVERAGES TABLE
        Schema::create('package_overages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_package_id')->constrained()->cascadeOnDelete();
            $table->foreignId('event_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('overage_type', ['tickets', 'comp_tickets']);
            $table->integer('quantity');
            $table->decimal('rate_per_unit', 10, 2);
            $table->decimal('total_amount', 10, 2);
            $table->boolean('accepted')->default(false);
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('accepted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['pending', 'accepted', 'declined', 'paid'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_reference')->nullable();
            $table->timestamps();
        });

        // 3. UPDATE EVENTS TABLE
        Schema::table('events', function (Blueprint $table) {
            $table->foreignId('organization_package_id')->nullable()
                ->after('organization_id')
                ->constrained('organization_packages')
                ->nullOnDelete();
        });

        // 4. UPDATE TICKETS TABLE (The Missing Piece!)
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('organization_package_id')->nullable()
                ->after('event_tier_id')
                ->constrained('organization_packages')
                ->nullOnDelete();
            
            $table->index('organization_package_id');
        });

        // 5. UPDATE ORGANIZATIONS TABLE (OTP)
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('otp_code', 6)->nullable()->after('phone');
            $table->timestamp('otp_expires_at')->nullable()->after('otp_code');
            $table->timestamp('phone_verified_at')->nullable()->after('otp_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['organization_package_id']);
            $table->dropColumn('organization_package_id');
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['organization_package_id']);
            $table->dropColumn('organization_package_id');
        });
        
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['otp_code', 'otp_expires_at', 'phone_verified_at']);
        });
        
        Schema::dropIfExists('package_overages');
        Schema::dropIfExists('organization_packages');
    }
};