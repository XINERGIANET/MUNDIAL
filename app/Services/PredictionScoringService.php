<?php

namespace App\Services;

use App\Models\FootballMatch;
use App\Models\Prediction;

class PredictionScoringService
{
    public function score(Prediction $prediction, FootballMatch $match): array
    {
        $tournament = $match->tournament;

        if ($prediction->predicted_home_score === $match->home_score && $prediction->predicted_away_score === $match->away_score) {
            return ['points' => $tournament->exact_score_points, 'type' => 'exact_score'];
        }

        if ($this->trend($prediction->predicted_home_score, $prediction->predicted_away_score) === $this->trend($match->home_score, $match->away_score)) {
            return ['points' => $tournament->correct_result_points, 'type' => 'correct_result'];
        }

        return ['points' => $tournament->wrong_prediction_points, 'type' => 'wrong'];
    }

    private function trend(int $home, int $away): string
    {
        return match ($home <=> $away) {
            1 => 'home',
            0 => 'draw',
            -1 => 'away',
        };
    }
}
