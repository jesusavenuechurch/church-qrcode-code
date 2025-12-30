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
        Schema::table('tickets', function (Blueprint $table) {
           // Client's choice: 'digital' or 'print'
            $table->enum('ticket_preference', ['digital', 'print'])->default('digital')->after('delivery_method');
            
            // Print tracking
            $table->dateTime('printed_at')->nullable()->after('ticket_preference');
            $table->foreignId('printed_by')->nullable()->constrained('users')->onDelete('set null')->after('printed_at');
            
            // Avatar/PDF
            $table->string('avatar_path')->nullable()->after('qr_code_path');
            $table->dateTime('avatar_generated_at')->nullable()->after('avatar_path');
      
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            //
        });
    }
};
