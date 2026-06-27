<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $user = auth()->user();

        if (! $user->hasVerifiedPhone()) {
            return redirect()->route('phone.verify');
        }

        // Todos los torneos activos son visibles para cualquier usuario autenticado
        $availableTournaments = Tournament::query()
            ->where('is_active', true)
            ->whereIn('status', ['open', 'running'])
            ->orderBy('starts_at')
            ->get();

        $availableTournamentIds = $availableTournaments->pluck('id');

        // Participaciones del usuario (jugadas)
        $participants = $user->participants()->with('tournament')->latest()->get();

        // Torneo seleccionado: prioriza torneos con participación, luego cualquier activo
        $selectedTournamentId = $request->integer('torneo')
            ?: $participants->pluck('tournament_id')->intersect($availableTournamentIds)->first()
            ?: $availableTournamentIds->first();

        $selectedTournament = $selectedTournamentId
            ? $availableTournaments->firstWhere('id', $selectedTournamentId)
            : null;

        // Jugadas del usuario en el torneo seleccionado
        $tournamentParticipants = $selectedTournamentId
            ? $participants->where('tournament_id', $selectedTournamentId)->values()
            : collect();

        // Jugada activa: por param ?jugada= o la primera
        $selectedParticipantId = $request->integer('jugada') ?: null;
        $selectedParticipant = $selectedParticipantId
            ? $tournamentParticipants->firstWhere('id', $selectedParticipantId)
            : $tournamentParticipants->first();

        $selectedStatus = $request->string('estado', 'abiertos')->toString();

        // Partidos: todos los del torneo activo son visibles, sin restricción por inscripción
        $matchesQuery = FootballMatch::query()
            ->with([
                'tournament',
                'phase',
                'homeTeam',
                'awayTeam',
                'predictions' => fn ($query) => $selectedParticipant
                    ? $query->where('participant_id', $selectedParticipant->id)
                    : $query->whereRaw('0=1'),
            ])
            ->whereIn('tournament_id', $availableTournamentIds)
            ->whereHas('homeTeam')
            ->whereHas('awayTeam');

        if ($selectedTournament) {
            $matchesQuery->where('tournament_id', $selectedTournament->id);
        }

        // Filtro desde la perspectiva del usuario:
        // Cerrado = ya guardaste tu pronóstico o la ventana ya cerró
        // Abierto  = ventana abierta y todavía no has guardado
        if ($selectedStatus === 'cerrados') {
            $matchesQuery->where(function ($q) use ($selectedParticipant) {
                $q->where('prediction_closes_at', '<=', now());
                if ($selectedParticipant?->isApproved()) {
                    $q->orWhereHas('predictions', fn ($pq) => $pq->where('participant_id', $selectedParticipant->id));
                }
            });
        } else {
            $matchesQuery->where('prediction_closes_at', '>', now())->whereIn('status', ['scheduled', 'live']);
            if ($selectedParticipant?->isApproved()) {
                $matchesQuery->whereDoesntHave('predictions', fn ($pq) => $pq->where('participant_id', $selectedParticipant->id));
            }
        }

        $tournamentMatches = $matchesQuery->orderBy('starts_at')->get();

        return view('dashboard', [
            'availableTournaments'   => $availableTournaments,
            'participants'           => $participants,
            'tournamentParticipants' => $tournamentParticipants,
            'selectedTournament'     => $selectedTournament,
            'selectedParticipant'    => $selectedParticipant,
            'selectedStatus'         => $selectedStatus,
            'tournamentMatches'      => $tournamentMatches,
            'upcomingMatches'        => $tournamentMatches->where('prediction_closes_at', '>', now())->whereIn('status', ['scheduled', 'live']),
            'rankings'               => $user->rankings()->with(['tournament', 'participant'])->get()->keyBy('participant_id'),
        ]);
    }
}
