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
            $table->boolean('is_complimentary')->default(false)->after('amount_paid');
            $table->integer('complimentary_issued_by')->nullable()->after('is_complimentary');
            $table->text('complimentary_reason')->nullable()->after('complimentary_issued_by');
            
            // Add index for filtering
            $table->index('is_complimentary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
             $table->dropColumn(['is_complimentary', 'complimentary_issued_by', 'complimentary_reason']);
        });
    }
};
