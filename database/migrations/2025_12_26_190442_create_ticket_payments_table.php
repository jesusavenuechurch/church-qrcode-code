<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add installments column to events table
        Schema::table('events', function (Blueprint $table) {
            $table->boolean('allow_installments')->default(false)->after('is_public');
            $table->decimal('minimum_deposit_percentage', 5, 2)->nullable()->after('allow_installments');
            $table->text('installment_instructions')->nullable()->after('minimum_deposit_percentage');
        });

        // Create ticket_payments table
        Schema::create('ticket_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->string('payment_method', 50)->nullable();
            $table->string('payment_reference')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('payment_date')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->string('payment_type', 50)->default('installment'); // 'deposit', 'installment', 'full'
            $table->timestamps();

            $table->index(['ticket_id', 'status']);
        });

        // Update tickets table
        Schema::table('tickets', function (Blueprint $table) {
            $table->decimal('amount_paid', 10, 2)->default(0)->after('amount');
            // Note: payment_status already exists, just update its enum values
            // We'll handle this in code, not migration to avoid data loss
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['allow_installments', 'minimum_deposit_percentage', 'installment_instructions']);
        });

        Schema::dropIfExists('ticket_payments');

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn('amount_paid');
        });
    }
};