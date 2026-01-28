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
        Schema::create('agents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('referral_token')->unique(); // e.g., "AG-ABC123"
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable(); // Internal notes about agent
            $table->integer('created_by')->nullable(); // Which admin created this agent
            
            // Future: Commission settings (add in Phase 2)
            // $table->decimal('commission_rate', 5, 2)->default(30.00); // 30%
            // $table->enum('commission_type', ['percentage', 'flat'])->default('percentage');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
