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
        Schema::create('organization_payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->onDelete('cascade');
            $table->string('payment_method', 50); // cash, ecocash, mpesa, bank_transfer, etc.
            $table->string('account_name')->nullable(); // Account holder name
            $table->string('account_number')->nullable(); // Merchant code, phone number, account number
            $table->text('instructions')->nullable(); // Custom instructions for this payment method
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0); // Order to show payment methods
            $table->timestamps();

            //$table->unique(['organization_id', 'payment_method']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_payment_methods');
    }
};
