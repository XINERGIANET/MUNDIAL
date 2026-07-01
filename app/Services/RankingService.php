<?php

namespace App\Services;

use App\Models\FootballMatch;
use App\Models\Prediction;
use App\Models\Tournament;
use App\Models\TournamentRanking;
use Illuminate\Support\Facades\DB;

class RankingService
{
    public function __construct(private PredictionScoringService $scoringService)
    {
    }

    public function recalculateMatchPredictions(FootballMatch $match): void
    {
        if (! $match->hasOfficialResult()) {
            return;
        }

        $match->loadMissing('tournament');

        $match->predictions()->each(function (Prediction $prediction) use ($match) {
            $score = $this->scoringService->score($prediction, $match);
            $prediction->update([
                'points_awarded' => $score['points'],
                'result_type' => $score['type'],
                'locked_at' => $prediction->locked_at ?: now(),
                'calculated_at' => now(),
            ]);
        });
    }

    public function recalculateTournamentRanking(Tournament $tournament): void
    {
        DB::transaction(function () use ($tournament) {
            $rows = Prediction::query()
                ->selectRaw('participant_id, user_id, SUM(points_awarded) as total_points')
                ->selectRaw("SUM(CASE WHEN result_type = 'exact_score' THEN 1 ELSE 0 END) as exact_scores_count")
                ->selectRaw("SUM(CASE WHEN result_type = 'correct_result' THEN 1 ELSE 0 END) as correct_results_count")
                ->selectRaw("SUM(CASE WHEN result_type = 'wrong' THEN 1 ELSE 0 END) as wrong_predictions_count")
                ->selectRaw('COUNT(*) as predictions_count')
                ->where('tournament_id', $tournament->id)
                ->groupBy('participant_id', 'user_id')
                ->orderByDesc('total_points')
                ->orderByDesc('exact_scores_count')
                ->get();

            $position = 1;

            foreach ($rows as $row) {
                TournamentRanking::updateOrCreate(
                    ['tournament_id' => $tournament->id, 'participant_id' => $row->participant_id],
                    [
                        'user_id' => $row->user_id,
                        'total_points' => (int) $row->total_points,
                        'exact_scores_count' => (int) $row->exact_scores_count,
                        'correct_results_count' => (int) $row->correct_results_count,
                        'wrong_predictions_count' => (int) $row->wrong_predictions_count,
                        'predictions_count' => (int) $row->predictions_count,
                        'position' => $position++,
                    ]
                );
            }
        });
    }

    public function getRanking(Tournament $tournament)
    {
        return $tournament->rankings()
            ->with(['user', 'participant.user'])
            ->orderBy('position')
            ->get();
    }
}
