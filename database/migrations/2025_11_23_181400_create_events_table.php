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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->dateTime('event_date');
            $table->integer('duration_days')->default(1); // number of days event runs
            $table->string('location')->nullable();
            $table->integer('capacity')->nullable();
            $table->enum('status', ['draft', 'published', 'live', 'closed'])->default('draft');
            $table->timestamps();
            
            $table->index(['organization_id', 'event_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
