<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tier_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('tiers_config'); // Array of tier configs
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index(['organization_id', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tier_templates');
    }
};