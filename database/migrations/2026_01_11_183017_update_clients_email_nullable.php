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
        Schema::table('clients', function (Blueprint $table) {
            // If email was ever unique before, drop the unique index first
            // (this line is safe even if it doesn't exist, as long as name matches)
            $table->dropUnique('clients_email_unique');

            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('email')->unique()->change();
        });
    }
};
