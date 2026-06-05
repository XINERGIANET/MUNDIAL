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
        Schema::create('tournament_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('phase_id')->constrained('tournament_phases')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('order')->default(1);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['phase_id', 'name']);
        });

        Schema::create('tournament_group_team', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_group_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('order')->nullable();
            $table->timestamps();

            $table->unique(['tournament_group_id', 'team_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tournament_group_team');
        Schema::dropIfExists('tournament_groups');
    }
};
