<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = auth()->user();

        if (! $user->hasVerifiedPhone()) {
            return redirect()->route('phone.verify');
        }

        $participants = $user->participants()->with('tournament')->latest()->get();
        $approvedTournamentIds = $participants->where('status', 'approved')->pluck('tournament_id');

        return view('dashboard', [
            'availableTournaments' => Tournament::query()->where('is_active', true)->where('status', 'open')->get(),
            'participants' => $participants,
            'upcomingMatches' => FootballMatch::query()
                ->with(['tournament', 'homeTeam', 'awayTeam', 'predictions' => fn ($query) => $query->where('user_id', $user->id)])
                ->whereIn('tournament_id', $approvedTournamentIds)
                ->where('prediction_closes_at', '>', now())
                ->whereIn('status', ['scheduled', 'live'])
                ->orderBy('prediction_closes_at')
                ->take(12)
                ->get(),
            'rankings' => $user->rankings()->with('tournament')->get()->keyBy('tournament_id'),
        ]);
    }
}
