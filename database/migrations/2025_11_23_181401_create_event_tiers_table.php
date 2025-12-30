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
        Schema::create('event_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->onDelete('cascade');
            $table->string('tier_name'); // VIP, Standard, Gold, etc.
            $table->decimal('price', 10, 2);
            $table->integer('quantity_available')->nullable(); // null = unlimited
            $table->integer('quantity_sold')->default(0);
            $table->boolean('is_active')->default(true);
            $table->unique(['event_id', 'tier_name']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_tiers');
    }
};
