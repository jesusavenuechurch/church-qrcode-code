<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            // Application fields (only filled during self-registration)
            $table->string('city_district')->nullable()->after('phone');
            $table->json('access_types')->nullable()->after('city_district')
                ->comment('churches, schools, businesses, event_planners');
            $table->text('motivation')->nullable()->after('access_types')
                ->comment('Why they want to be agent - used for approval');
            
            // Status for self-registered agents
            $table->enum('status', ['pending', 'approved', 'rejected', 'active'])
                ->default('active')
                ->after('is_active')
                ->comment('pending=self-registered awaiting approval, active=admin-created or approved');
            
            $table->foreignId('approved_by')->nullable()->after('status')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('agents', function (Blueprint $table) {
            $table->dropColumn([
                'city_district',
                'access_types',
                'motivation',
                'status',
                'approved_by',
                'approved_at',
            ]);
        });
    }
};