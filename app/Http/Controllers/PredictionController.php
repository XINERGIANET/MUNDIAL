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
        abort_if(! $match->isPredictionOpen(), 403, 'El pronóstico para este partido ya está cerrado.');
        abort_unless($match->homeTeam && $match->awayTeam, 403, 'Este partido no tiene equipos completos.');
        abort_unless($match->homeTeam->is_active && $match->awayTeam->is_active, 403, 'Este cruce aún tiene equipos por definir.');

        $participant = TournamentParticipant::query()
            ->where('id', $request->integer('participant_id'))
            ->where('user_id', $user->id)
            ->where('tournament_id', $match->tournament_id)
            ->firstOrFail();

        abort_unless(MatchAccess::canParticipantAccess($participant, $match), 403, 'Tu participación aún no tiene acceso a este partido.');

        $alreadySaved = Prediction::query()
            ->where('match_id', $match->id)
            ->where('participant_id', $participant->id)
            ->exists();

        abort_if($alreadySaved, 403, 'Ya guardaste tu pronóstico para este partido. No se puede cambiar.');

        Prediction::create([
            'tournament_id'        => $match->tournament_id,
            'match_id'             => $match->id,
            'user_id'              => $user->id,
            'participant_id'       => $participant->id,
            'predicted_home_score' => $request->integer('predicted_home_score'),
            'predicted_away_score' => $request->integer('predicted_away_score'),
            'result_type'          => 'pending',
            'locked_at'            => now(),
        ]);

        return back()->with('status', 'Pronóstico guardado y cerrado.');
    }

    public function bulkStore(Request $request, Tournament $tournament): RedirectResponse
    {
        $user = $request->user();

        abort_unless($user->hasVerifiedPhone(), 403);

        $validated = $request->validate([
            'participant_id' => ['required', 'integer'],
            'save_mode'      => ['required', 'in:partial,final'],
            'predictions'    => ['nullable', 'array'],
            'predictions.*.predicted_home_score' => ['nullable', 'integer', 'min:0', 'max:30'],
            'predictions.*.predicted_away_score' => ['nullable', 'integer', 'min:0', 'max:30'],
        ]);

        // Verificar que la jugada pertenece al usuario y al torneo
        $participant = TournamentParticipant::query()
            ->where('id', $validated['participant_id'])
            ->where('user_id', $user->id)
            ->where('tournament_id', $tournament->id)
            ->firstOrFail();

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

        abort_if($participant->hasFinalizedPredictions(), 403, 'Tus pronosticos finales ya fueron guardados y no se pueden editar.');
        abort_if($openMatches->isEmpty(), 403, 'Tu participacion aun no tiene acceso a partidos de este torneo.');

        if ($validated['save_mode'] === 'final' && ! $participant->isApproved()) {
            throw ValidationException::withMessages([
                'predictions' => 'La cortesia de bienvenida solo permite guardar pronosticos parciales.',
            ]);
        }

        $existingPredictions = Prediction::query()
            ->where('tournament_id', $tournament->id)
            ->where('participant_id', $participant->id)
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

        DB::transaction(function () use ($completePredictions, $tournament, $user, $participant, $validated) {
            foreach ($completePredictions as $matchId => $predictionData) {
                Prediction::updateOrCreate(
                    ['match_id' => $matchId, 'participant_id' => $participant->id],
                    [
                        'tournament_id'        => $tournament->id,
                        'user_id'              => $user->id,
                        'predicted_home_score' => $predictionData['predicted_home_score'],
                        'predicted_away_score' => $predictionData['predicted_away_score'],
                        'result_type'          => 'pending',
                    ]
                );
            }

            if ($validated['save_mode'] === 'final') {
                $now = now();

                Prediction::query()
                    ->where('tournament_id', $tournament->id)
                    ->where('participant_id', $participant->id)
                    ->update(['locked_at' => $now]);

                $participant->forceFill(['predictions_finalized_at' => $now])->save();
            }
        });

        return back()->with('status', $validated['save_mode'] === 'final'
            ? 'Pronosticos guardados definitivamente. Ya no se pueden editar.'
            : 'Pronosticos guardados parcialmente.');
    }
}
