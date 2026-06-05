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
        $this->rankingService->recalculateMatchPredictions($match->fresh(['tournament']));
        $this->rankingService->recalculateTournamentRanking($match->tournament);

        return $match->fresh();
    }
}
