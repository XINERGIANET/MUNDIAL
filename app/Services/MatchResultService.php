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

    public function register(FootballMatch $match, int $homeScore, int $awayScore, User $admin, ?int $penaltyWinnerId = null): FootballMatch
    {
        $old = $match->only(['home_score', 'away_score', 'penalty_winner_team_id', 'status']);

        $match->update([
            'home_score'             => $homeScore,
            'away_score'             => $awayScore,
            'penalty_winner_team_id' => $homeScore === $awayScore ? $penaltyWinnerId : null,
            'status'                 => 'finished',
            'result_registered_by'   => $admin->id,
            'result_registered_at'   => now(),
        ]);

        $this->auditService->record('match_result_saved', $match, $old, $match->only(['home_score', 'away_score', 'penalty_winner_team_id', 'status']));
        $this->advanceBracket($match->fresh());
        $this->rankingService->recalculateMatchPredictions($match->fresh(['tournament']));
        $this->rankingService->recalculateTournamentRanking($match->tournament);

        return $match->fresh();
    }

    private function advanceBracket(FootballMatch $match): void
    {
        if ($match->home_score === $match->away_score) {
            // Empate → ganador por penales
            $winnerId = $match->penalty_winner_team_id;
            if (! $winnerId) {
                return;
            }
        } else {
            $winnerId = $match->home_score > $match->away_score
                ? $match->home_team_id
                : $match->away_team_id;
        }

        FootballMatch::where('home_source_match_id', $match->id)
            ->update(['home_team_id' => $winnerId]);

        FootballMatch::where('away_source_match_id', $match->id)
            ->update(['away_team_id' => $winnerId]);
    }
}
