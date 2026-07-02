<?php

namespace App\Services;

use App\Models\FootballMatch;
use App\Models\User;

class MatchResultService
{
    public function __construct(
        private AuditService $auditService,
        private RankingService $rankingService,
    ) {
    }

    public function register(FootballMatch $match, int $homeScore, int $awayScore, User $admin): FootballMatch
    {
        $old = $match->only(['home_score', 'away_score', 'status']);

        $match->update([
            'home_score' => $homeScore,
            'away_score' => $awayScore,
            'status' => 'finished',
            'result_registered_by' => $admin->id,
            'result_registered_at' => now(),
        ]);

        $this->auditService->record('match_result_saved', $match, $old, $match->only(['home_score', 'away_score', 'status']));
        $this->advanceBracket($match->fresh());
        $this->rankingService->recalculateMatchPredictions($match->fresh(['tournament']));
        $this->rankingService->recalculateTournamentRanking($match->tournament);

        return $match->fresh();
    }

    // Si hay ganador claro, lo coloca en el partido siguiente.
    // En caso de empate (penales), el admin asigna el equipo manualmente.
    private function advanceBracket(FootballMatch $match): void
    {
        if ($match->home_score === $match->away_score) {
            return;
        }

        $winnerId = $match->home_score > $match->away_score
            ? $match->home_team_id
            : $match->away_team_id;

        FootballMatch::where('home_source_match_id', $match->id)
            ->update(['home_team_id' => $winnerId]);

        FootballMatch::where('away_source_match_id', $match->id)
            ->update(['away_team_id' => $winnerId]);
    }
}
