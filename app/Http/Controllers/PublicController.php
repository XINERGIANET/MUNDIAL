<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Tournament;
use App\Services\RankingService;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        $tournament = Tournament::where('slug', 'mundial-demo-2026')
            ->where('is_active', true)
            ->first();

        $matches = $tournament
            ? FootballMatch::with(['homeTeam', 'awayTeam', 'phase'])
                ->where('tournament_id', $tournament->id)
                ->whereHas('phase', fn ($q) => $q->where('name', 'Dieciseisavos de final'))
                ->orderBy('starts_at')
                ->get()
            : collect();

        return view('public.home', [
            'tournament' => $tournament,
            'matches'    => $matches,
        ]);
    }

    public function tournaments(): View
    {
        return view('public.tournaments', [
            'tournaments' => Tournament::query()->where('is_active', true)->orderBy('starts_at')->get(),
        ]);
    }

    public function ranking(Tournament $tournament, RankingService $rankingService): View
    {
        return view('public.ranking', [
            'tournament' => $tournament,
            'rankings' => $rankingService->getRanking($tournament),
        ]);
    }
}
