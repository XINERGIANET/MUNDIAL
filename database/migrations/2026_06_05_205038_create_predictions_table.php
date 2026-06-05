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
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('predicted_home_score');
            $table->unsignedTinyInteger('predicted_away_score');
            $table->integer('points_awarded')->default(0)->index();
            $table->string('result_type')->nullable()->default('pending')->index();
            $table->dateTime('locked_at')->nullable();
            $table->dateTime('calculated_at')->nullable();
            $table->timestamps();

            $table->unique(['match_id', 'user_id']);
            $table->index(['tournament_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
