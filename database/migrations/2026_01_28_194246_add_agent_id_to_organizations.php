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
            $table->foreignId('agent_id')->nullable()->after('is_active')->constrained('agents')->nullOnDelete();
            $table->timestamp('registered_via_agent_at')->nullable(); // When org registered via agent link
            $table->string('registration_source')->nullable(); // 'agent', 'direct', 'admin'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['agent_id']);
            $table->dropColumn(['agent_id', 'registered_via_agent_at', 'registration_source']);
        });
    }
};
