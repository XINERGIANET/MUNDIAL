<?php

namespace App\Services;

use App\Models\FootballMatch;
use App\Models\Prediction;
use App\Models\Tournament;
use App\Models\TournamentParticipant;
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
            $predictionStats = Prediction::query()
                ->selectRaw('participant_id, user_id, SUM(points_awarded) as total_points')
                ->selectRaw("SUM(CASE WHEN result_type = 'exact_score' THEN 1 ELSE 0 END) as exact_scores_count")
                ->selectRaw("SUM(CASE WHEN result_type = 'correct_result' THEN 1 ELSE 0 END) as correct_results_count")
                ->selectRaw("SUM(CASE WHEN result_type = 'wrong' THEN 1 ELSE 0 END) as wrong_predictions_count")
                ->selectRaw('COUNT(*) as predictions_count')
                ->where('tournament_id', $tournament->id)
                ->groupBy('participant_id', 'user_id')
                ->get()
                ->keyBy('participant_id');

            $participants = TournamentParticipant::query()
                ->where('tournament_id', $tournament->id)
                ->where('status', 'approved')
                ->get();

            $ranked = $participants->map(function ($participant) use ($predictionStats) {
                $stats = $predictionStats->get($participant->id);
                return [
                    'participant_id'          => $participant->id,
                    'user_id'                 => $participant->user_id,
                    'total_points'            => $stats ? (int) $stats->total_points : 0,
                    'exact_scores_count'      => $stats ? (int) $stats->exact_scores_count : 0,
                    'correct_results_count'   => $stats ? (int) $stats->correct_results_count : 0,
                    'wrong_predictions_count' => $stats ? (int) $stats->wrong_predictions_count : 0,
                    'predictions_count'       => $stats ? (int) $stats->predictions_count : 0,
                ];
            })
            ->sortByDesc('exact_scores_count')
            ->sortByDesc('total_points')
            ->values();

            $position = 1;
            foreach ($ranked as $row) {
                TournamentRanking::updateOrCreate(
                    ['tournament_id' => $tournament->id, 'participant_id' => $row['participant_id']],
                    [
                        'user_id'                 => $row['user_id'],
                        'total_points'            => $row['total_points'],
                        'exact_scores_count'      => $row['exact_scores_count'],
                        'correct_results_count'   => $row['correct_results_count'],
                        'wrong_predictions_count' => $row['wrong_predictions_count'],
                        'predictions_count'       => $row['predictions_count'],
                        'position'                => $position++,
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
