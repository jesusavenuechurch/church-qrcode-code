<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add slug and public fields to organizations table
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('name');
            $table->string('tagline')->nullable()->after('description');
            $table->string('contact_email')->nullable()->after('email');
        });

        // Add slug and public fields to events table
        Schema::table('events', function (Blueprint $table) {
            $table->string('slug')->nullable()->after('name');
            $table->boolean('is_public')->default(true)->after('status');
            $table->string('tagline')->nullable()->after('name');
            $table->string('venue')->nullable()->after('location'); // Venue name (e.g., "Kampala Serena Hotel")
            // Keep 'location' as is - it will store the full address
            
            // Add unique constraint on slug within organization
            $table->unique(['organization_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['slug', 'tagline', 'contact_email']);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropUnique(['organization_id', 'slug']);
            $table->dropColumn(['slug', 'is_public', 'tagline', 'venue']);
        });
    }
};