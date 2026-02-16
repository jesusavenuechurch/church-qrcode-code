<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agent_certifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('agents')->cascadeOnDelete();
            $table->integer('score')->default(0);        // e.g. 8 out of 11
            $table->integer('total_questions')->default(11);
            $table->boolean('passed')->default(false);
            $table->integer('attempts')->default(0);
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_attempted_at')->nullable();
            $table->timestamps();

            $table->unique('agent_id'); // one record per agent, updated on retake
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agent_certifications');
    }
};