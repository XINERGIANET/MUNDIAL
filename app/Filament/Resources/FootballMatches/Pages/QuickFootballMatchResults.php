<?php

namespace App\Filament\Resources\FootballMatches\Pages;

use App\Filament\Resources\FootballMatches\FootballMatchResource;
use App\Models\FootballMatch;
use App\Services\MatchResultService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class QuickFootballMatchResults extends Page
{
    protected static string $resource = FootballMatchResource::class;

    protected string $view = 'filament.resources.football-matches.pages.quick-results';

    /**
     * @var array<int, array{home_score: int|string|null, away_score: int|string|null}>
     */
    public array $scores = [];

    public function mount(): void
    {
        foreach ($this->matches() as $match) {
            $this->scores[$match->id] = [
                'home_score' => $match->home_score,
                'away_score' => $match->away_score,
            ];
        }
    }

    public function getTitle(): string|Htmlable
    {
        return 'Resultados rapidos';
    }

    public function getBreadcrumb(): ?string
    {
        return 'Resultados rapidos';
    }

    public function matches(): Collection
    {
        return FootballMatch::query()
            ->with(['tournament', 'homeTeam', 'awayTeam', 'group', 'phase'])
            ->whereHas('homeTeam')
            ->whereHas('awayTeam')
            ->orderBy('starts_at')
            ->get();
    }

    public function randomize(): void
    {
        foreach ($this->matches() as $match) {
            $this->scores[$match->id] = [
                'home_score' => random_int(0, 4),
                'away_score' => random_int(0, 4),
            ];
        }

        Notification::make()
            ->title('Marcadores aleatorios generados')
            ->body('Revisa las casillas y guarda cuando quieras aplicar los resultados.')
            ->success()
            ->send();
    }

    public function save(): void
    {
        $saved = 0;
        $invalid = 0;

        foreach ($this->matches() as $match) {
            $homeScore = $this->scores[$match->id]['home_score'] ?? null;
            $awayScore = $this->scores[$match->id]['away_score'] ?? null;

            if ($homeScore === null || $homeScore === '' || $awayScore === null || $awayScore === '') {
                continue;
            }

            if (! is_numeric($homeScore) || ! is_numeric($awayScore) || $homeScore < 0 || $awayScore < 0 || $homeScore > 30 || $awayScore > 30) {
                $invalid++;

                continue;
            }

            $admin = Auth::user();

            if (! $admin) {
                continue;
            }

            app(MatchResultService::class)->register($match, (int) $homeScore, (int) $awayScore, $admin);

            $saved++;
        }

        if ($invalid > 0) {
            Notification::make()
                ->title('Hay marcadores invalidos')
                ->body('Solo se guardaron filas con marcadores entre 0 y 30.')
                ->danger()
                ->send();
        }

        Notification::make()
            ->title($saved === 1 ? '1 resultado guardado' : "{$saved} resultados guardados")
            ->success()
            ->send();
    }
}
