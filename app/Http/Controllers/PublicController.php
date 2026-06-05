<?php

namespace App\Http\Controllers;

use App\Models\Tournament;
use App\Services\RankingService;
use Illuminate\View\View;

class PublicController extends Controller
{
    public function home(): View
    {
        return view('public.home', [
            'tournaments' => Tournament::query()
                ->where('is_active', true)
                ->whereIn('status', ['open', 'running'])
                ->latest('starts_at')
                ->take(6)
                ->get(),
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
