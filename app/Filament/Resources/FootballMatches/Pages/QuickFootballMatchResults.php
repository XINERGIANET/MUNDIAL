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
        $saved = 0;

        foreach ($this->matches() as $match) {
            $homeScore = random_int(0, 4);
            $awayScore = random_int(0, 4);

            $this->scores[$match->id] = [
                'home_score' => $homeScore,
                'away_score' => $awayScore,
            ];

            if ($this->registerScore($match, $homeScore, $awayScore) === 'saved') {
                $saved++;
            }
        }

        Notification::make()
            ->title('Resultados aleatorios registrados')
            ->body("Se guardaron {$saved} partidos aleatorios en un solo clic.")
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

            $status = $this->registerScore($match, $homeScore, $awayScore);

            if ($status === 'saved') {
                $saved++;
            }

            if ($status === 'invalid') {
                $invalid++;
            }
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

    private function registerScore(FootballMatch $match, int|string|null $homeScore, int|string|null $awayScore): string
    {
        if ($homeScore === null || $homeScore === '' || $awayScore === null || $awayScore === '') {
            return 'skip';
        }

        if (! is_numeric($homeScore) || ! is_numeric($awayScore) || $homeScore < 0 || $awayScore < 0 || $homeScore > 30 || $awayScore > 30) {
            return 'invalid';
        }

        $admin = Auth::user();

        if (! $admin) {
            return 'skip';
        }

        app(MatchResultService::class)->register($match, (int) $homeScore, (int) $awayScore, $admin);

        return 'saved';
    }
}
