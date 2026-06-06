<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Tournament;
use App\Models\TournamentGroup;
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

        $participants = $user->participants()->with('tournament')->latest()->get();
        $approvedTournamentIds = $participants->where('status', 'approved')->pluck('tournament_id');
        $selectedTournamentId = $request->integer('torneo') ?: $approvedTournamentIds->first();
        $selectedTournament = $selectedTournamentId ? Tournament::find($selectedTournamentId) : null;
        $selectedGroupId = $request->integer('grupo') ?: null;
        $selectedStatus = $request->string('estado', 'abiertos')->toString();

        $matchesQuery = FootballMatch::query()
            ->with([
                'tournament',
                'phase',
                'group',
                'homeTeam',
                'awayTeam',
                'predictions' => fn ($query) => $query->where('user_id', $user->id),
            ])
            ->whereIn('tournament_id', $approvedTournamentIds)
            ->whereHas('homeTeam')
            ->whereHas('awayTeam');

        if ($selectedTournament) {
            $matchesQuery->where('tournament_id', $selectedTournament->id);
        }

        if ($selectedGroupId) {
            $matchesQuery->where('group_id', $selectedGroupId);
        }

        match ($selectedStatus) {
            'cerrados' => $matchesQuery->where('prediction_closes_at', '<=', now()),
            'resultados' => $matchesQuery->where('status', 'finished'),
            'todos' => null,
            default => $matchesQuery->where('prediction_closes_at', '>', now())->whereIn('status', ['scheduled', 'live']),
        };

        $tournamentMatches = $matchesQuery
            ->orderBy('starts_at')
            ->get();

        return view('dashboard', [
            'availableTournaments' => Tournament::query()->where('is_active', true)->where('status', 'open')->get(),
            'participants' => $participants,
            'approvedTournaments' => Tournament::query()->whereIn('id', $approvedTournamentIds)->orderBy('starts_at')->get(),
            'selectedTournament' => $selectedTournament,
            'selectedGroupId' => $selectedGroupId,
            'selectedStatus' => $selectedStatus,
            'tournamentGroups' => $selectedTournament
                ? TournamentGroup::query()->where('tournament_id', $selectedTournament->id)->orderBy('order')->get()
                : collect(),
            'tournamentMatches' => $tournamentMatches,
            'upcomingMatches' => $tournamentMatches->where('prediction_closes_at', '>', now())->whereIn('status', ['scheduled', 'live']),
            'rankings' => $user->rankings()->with('tournament')->get()->keyBy('tournament_id'),
        ]);
    }
}
