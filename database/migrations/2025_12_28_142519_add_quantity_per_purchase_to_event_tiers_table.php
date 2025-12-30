<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('event_tiers', function (Blueprint $table) {
            $table->integer('quantity_per_purchase')->default(1)->after('quantity_available');
        });
    }

    public function down()
    {
        Schema::table('event_tiers', function (Blueprint $table) {
            $table->dropColumn('quantity_per_purchase');
        });
    }
};
