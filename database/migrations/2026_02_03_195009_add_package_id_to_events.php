<?php
// database/migrations/2026_02_04_100001_add_package_id_to_events.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::table('events', function (Blueprint $table) {
        //     $table->foreignId('organization_package_id')->nullable()
        //         ->after('organization_id')
        //         ->constrained('organization_packages')
        //         ->nullOnDelete()
        //         ->comment('Which package this event uses (locked after creation)');
            
        //     $table->index('organization_package_id');
        // });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropForeign(['organization_package_id']);
            $table->dropColumn('organization_package_id');
        });
    }
};