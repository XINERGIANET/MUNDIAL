<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->foreignId('penalty_winner_team_id')->nullable()->after('away_score')->constrained('teams')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Team::class, 'penalty_winner_team_id');
            $table->dropColumn('penalty_winner_team_id');
        });
    }
};
