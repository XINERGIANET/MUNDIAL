<?php

namespace App\Http\Controllers;

use App\Models\FootballMatch;
use App\Models\Prediction;
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

        $octavos = $tournament
            ? FootballMatch::with([
                'homeTeam',
                'awayTeam',
                'homeSourceMatch.homeTeam',
                'homeSourceMatch.awayTeam',
                'awaySourceMatch.homeTeam',
                'awaySourceMatch.awayTeam',
              ])
                ->where('tournament_id', $tournament->id)
                ->whereHas('phase', fn ($q) => $q->where('order', '>', 0))
                ->whereHas('homeTeam')
                ->whereHas('awayTeam')
                ->orderBy('starts_at')
                ->get()
            : collect();

        return view('public.home', [
            'tournament' => $tournament,
            'octavos'    => $octavos,
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
        $rankings = $rankingService->getRanking($tournament);

        $matches = FootballMatch::query()
            ->with(['phase', 'homeTeam', 'awayTeam'])
            ->where('tournament_id', $tournament->id)
            ->whereHas('homeTeam')
            ->whereHas('awayTeam')
            ->orderBy('starts_at')
            ->get();

        // Cargar pronósticos de todos los participantes rankeados
        // Solo se muestran para partidos ya cerrados (prediction_closes_at pasó)
        $participantIds = $rankings->pluck('participant_id')->filter()->values();
        $closedMatchIds = $matches->filter(fn ($m) => $m->prediction_closes_at->isPast())->pluck('id');

        $allPredictions = Prediction::query()
            ->whereIn('participant_id', $participantIds)
            ->whereIn('match_id', $closedMatchIds)
            ->get()
            ->groupBy('participant_id')
            ->map(fn ($preds) => $preds->keyBy('match_id'));

        return view('public.ranking', [
            'tournament'  => $tournament,
            'rankings'    => $rankings,
            'matches'     => $matches,
            'predictions' => $allPredictions,
        ]);
    }
}
