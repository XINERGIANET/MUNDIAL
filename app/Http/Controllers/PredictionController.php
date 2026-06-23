<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePredictionRequest;
use App\Models\FootballMatch;
use App\Models\Prediction;
use App\Models\Tournament;
use App\Models\TournamentParticipant;
use App\Support\MatchAccess;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PredictionController extends Controller
{
    public function store(StorePredictionRequest $request, FootballMatch $match): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->hasVerifiedPhone(), 403);
        abort_if(! $match->isPredictionOpen(), 403, 'El pronostico para este partido ya esta cerrado.');
        abort_unless($match->homeTeam && $match->awayTeam, 403, 'Este partido no tiene equipos completos.');
        abort_unless($match->homeTeam->is_active && $match->awayTeam->is_active, 403, 'Este cruce aun tiene equipos por definir.');

        $participant = $user->participants()
            ->where('tournament_id', $match->tournament_id)
            ->first();

        abort_unless(MatchAccess::canParticipantAccess($participant, $match), 403, 'Tu participacion aun no tiene acceso a este partido.');

        if (! $participant && $match->is_welcome_courtesy) {
            $participant = TournamentParticipant::firstOrCreate(
                ['tournament_id' => $match->tournament_id, 'user_id' => $user->id],
                ['status' => 'pending_payment', 'payment_status' => 'unpaid']
            );
        }

        abort_if($participant?->hasFinalizedPredictions(), 403, 'Tus pronosticos finales ya fueron guardados y no se pueden editar.');

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

    public function bulkStore(Request $request, Tournament $tournament): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->hasVerifiedPhone(), 403);

        $participant = $user->participants()
            ->where('tournament_id', $tournament->id)
            ->first();

        $validated = $request->validate([
            'save_mode' => ['required', 'in:partial,final'],
            'predictions' => ['nullable', 'array'],
            'predictions.*.predicted_home_score' => ['nullable', 'integer', 'min:0', 'max:30'],
            'predictions.*.predicted_away_score' => ['nullable', 'integer', 'min:0', 'max:30'],
        ]);

        $submittedPredictions = collect($validated['predictions'] ?? []);
        $openMatches = FootballMatch::query()
            ->with(['homeTeam', 'awayTeam'])
            ->where('tournament_id', $tournament->id)
            ->whereHas('homeTeam')
            ->whereHas('awayTeam')
            ->get()
            ->filter(fn (FootballMatch $match) => $match->isPredictionOpen()
                && MatchAccess::canParticipantAccess($participant, $match)
                && $match->homeTeam->is_active
                && $match->awayTeam->is_active)
            ->keyBy('id');

        abort_if($participant?->hasFinalizedPredictions(), 403, 'Tus pronosticos finales ya fueron guardados y no se pueden editar.');

        abort_if($openMatches->isEmpty(), 403, 'Tu participacion aun no tiene acceso a partidos de este torneo.');

        if (! $participant) {
            $participant = TournamentParticipant::firstOrCreate(
                ['tournament_id' => $tournament->id, 'user_id' => $user->id],
                ['status' => 'pending_payment', 'payment_status' => 'unpaid']
            );
        }

        if ($validated['save_mode'] === 'final' && ! $participant->isApproved()) {
            throw ValidationException::withMessages([
                'predictions' => 'La cortesia de bienvenida solo permite guardar pronosticos parciales.',
            ]);
        }

        $existingPredictions = Prediction::query()
            ->where('tournament_id', $tournament->id)
            ->where('user_id', $user->id)
            ->get()
            ->keyBy('match_id');

        $completePredictions = [];
        $incompleteMatches = [];

        foreach ($submittedPredictions as $matchId => $predictionData) {
            if (! $openMatches->has((int) $matchId)) {
                continue;
            }

            $hasHome = array_key_exists('predicted_home_score', $predictionData) && $predictionData['predicted_home_score'] !== null && $predictionData['predicted_home_score'] !== '';
            $hasAway = array_key_exists('predicted_away_score', $predictionData) && $predictionData['predicted_away_score'] !== null && $predictionData['predicted_away_score'] !== '';

            if ($hasHome xor $hasAway) {
                $incompleteMatches[] = $matchId;
                continue;
            }

            if ($hasHome && $hasAway) {
                $completePredictions[(int) $matchId] = [
                    'predicted_home_score' => (int) $predictionData['predicted_home_score'],
                    'predicted_away_score' => (int) $predictionData['predicted_away_score'],
                ];
            }
        }

        if ($incompleteMatches !== []) {
            throw ValidationException::withMessages([
                'predictions' => 'Cada pronostico debe tener los dos marcadores o quedar completamente vacio.',
            ]);
        }

        if ($validated['save_mode'] === 'final') {
            $missingMatches = $openMatches->keys()->filter(function (int $matchId) use ($completePredictions, $existingPredictions) {
                return ! isset($completePredictions[$matchId]) && ! $existingPredictions->has($matchId);
            });

            if ($missingMatches->isNotEmpty()) {
                throw ValidationException::withMessages([
                    'predictions' => 'Completa todos los partidos abiertos antes de guardar definitivamente.',
                ]);
            }
        }

        DB::transaction(function () use ($completePredictions, $openMatches, $tournament, $user, $participant, $validated) {
            foreach ($completePredictions as $matchId => $predictionData) {
                Prediction::updateOrCreate(
                    ['match_id' => $matchId, 'user_id' => $user->id],
                    [
                        'tournament_id' => $tournament->id,
                        'predicted_home_score' => $predictionData['predicted_home_score'],
                        'predicted_away_score' => $predictionData['predicted_away_score'],
                        'result_type' => 'pending',
                    ]
                );
            }

            if ($validated['save_mode'] === 'final') {
                $now = now();

                Prediction::query()
                    ->where('tournament_id', $tournament->id)
                    ->where('user_id', $user->id)
                    ->whereIn('match_id', $openMatches->keys())
                    ->update(['locked_at' => $now]);

                $participant->forceFill(['predictions_finalized_at' => $now])->save();
            }
        });

        return back()->with('status', $validated['save_mode'] === 'final'
            ? 'Pronosticos guardados definitivamente. Ya no se pueden editar.'
            : 'Pronosticos guardados parcialmente.');
    }
}
