<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePredictionRequest;
use App\Models\FootballMatch;
use App\Models\Prediction;
use Illuminate\Http\RedirectResponse;

class PredictionController extends Controller
{
    public function store(StorePredictionRequest $request, FootballMatch $match): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->hasVerifiedPhone(), 403);
        abort_if(! $match->isPredictionOpen(), 403, 'El pronostico para este partido ya esta cerrado.');
        abort_unless($match->homeTeam->is_active && $match->awayTeam->is_active, 403, 'Este cruce aun tiene equipos por definir.');

        $participant = $user->participants()
            ->where('tournament_id', $match->tournament_id)
            ->where('status', 'approved')
            ->first();

        abort_unless($participant, 403, 'Tu participacion aun no esta aprobada para este torneo.');

        Prediction::updateOrCreate(
            ['match_id' => $match->id, 'user_id' => $user->id],
            [
                'tournament_id' => $match->tournament_id,
                'predicted_home_score' => $request->integer('predicted_home_score'),
                'predicted_away_score' => $request->integer('predicted_away_score'),
                'result_type' => 'pending',
            ]
        );

        return back()->with('status', 'Pronostico guardado.');
    }
}
