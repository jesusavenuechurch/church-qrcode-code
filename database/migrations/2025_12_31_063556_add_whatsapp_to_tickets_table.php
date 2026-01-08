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
            $table->string('preferred_delivery')->nullable()->after('delivery_method');
            $table->boolean('has_whatsapp')->default(false)->after('preferred_delivery');
            $table->string('delivery_status')->default('pending')->after('has_whatsapp');
            $table->timestamp('whatsapp_delivered_at')->nullable()->after('delivered_at');
            $table->timestamp('email_delivered_at')->nullable()->after('whatsapp_delivered_at');
            $table->json('delivery_log')->nullable()->after('email_delivered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropColumn([
                'preferred_delivery',
                'has_whatsapp',
                'delivery_status',
                'whatsapp_delivered_at',
                'email_delivered_at',
                'delivery_log',
            ]);
        });
    }
};