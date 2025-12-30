<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_tiers', function (Blueprint $table) {
            $table->string('color')->nullable()->after('tier_name')->comment('Hex color for QR code (e.g., #FFD700)');
            $table->text('description')->nullable()->after('color')->comment('Tier description/benefits');
        });
    }

    public function down(): void
    {
        Schema::table('event_tiers', function (Blueprint $table) {
            $table->dropColumn(['color', 'description']);
        });
    }
};