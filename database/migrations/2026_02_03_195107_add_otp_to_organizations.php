<?php
// database/migrations/2026_02_04_100002_add_otp_to_organizations.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schema::table('organizations', function (Blueprint $table) {
        //     $table->string('otp_code', 6)->nullable()
        //         ->after('phone')
        //         ->comment('WhatsApp OTP verification code');
            
        //     $table->timestamp('otp_expires_at')->nullable()
        //         ->after('otp_code')
        //         ->comment('When OTP expires (10 minutes)');
            
        //     $table->timestamp('phone_verified_at')->nullable()
        //         ->after('otp_expires_at')
        //         ->comment('When phone number was verified');
            
        //     $table->index('otp_code');
        // });
    }

    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropColumn(['otp_code', 'otp_expires_at', 'phone_verified_at']);
        });
    }
};