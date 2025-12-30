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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->foreignId('event_tier_id')->constrained('event_tiers')->onDelete('cascade');
            $table->string('ticket_number')->unique(); // e.g., TICKET-ORG-20250108-0001
            $table->string('qr_code')->unique();
            $table->string('qr_code_path')->nullable(); // path to QR image
            
            // Ticket status
            $table->string('status')->default('active');
            
            // Payment info
            $table->string('payment_method', 50)->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('payment_status')->default('pending');
            $table->dateTime('payment_date')->nullable();
            $table->string('payment_reference')->nullable(); // Mpesa ref or online transaction ID
            
            // Delivery & Check-in
            $table->string('delivery_method')->nullable();
            $table->dateTime('delivered_at')->nullable();
            $table->dateTime('checked_in_at')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->onDelete('set null');
            
            // Metadata
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            $table->index(['event_id', 'client_id']);
            $table->index(['status', 'checked_in_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
