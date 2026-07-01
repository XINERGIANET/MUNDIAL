<?php

namespace Database\Seeders;

use App\Models\FootballMatch;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentPhase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorldCup2026RoundOf32Seeder extends Seeder
{
    // Partidos de Dieciseisavos de Final — datos del bracket oficial FIFA 2026.
    // Horarios en UTC. Fuente: Wikipedia/en 2026 FIFA World Cup knockout stage (27 jun 2026).
    // Formato: [local, visitante, inicio_utc, octavo_idx (1-8), lado ('home'|'away')]
    // octavo_idx corresponde al orden de partidos de octavos (1=primero del 4/7, 2=segundo, etc.)
    private const FIXTURES = [
        // Partido 73 → alimenta Octavo 1 (local)
        ['Sudáfrica',          'Canadá',                 '2026-06-28 19:00:00', 1, 'home'],  // SoFi Stadium, Inglewood
        // Partido 75 → alimenta Octavo 1 (visitante)
        ['Países Bajos',       'Marruecos',              '2026-06-30 01:00:00', 1, 'away'],  // Estadio BBVA, Guadalupe
        // Partido 74 → alimenta Octavo 2 (local)
        ['Alemania',           'Paraguay',               '2026-06-29 20:30:00', 2, 'home'],  // Gillette Stadium, Foxborough
        // Partido 77 → alimenta Octavo 2 (visitante)
        ['Francia',            'Suecia',                 '2026-06-30 21:00:00', 2, 'away'],  // MetLife Stadium, East Rutherford
        // Partido 76 → alimenta Octavo 3 (local)
        ['Brasil',             'Japón',                  '2026-06-29 17:00:00', 3, 'home'],  // NRG Stadium, Houston
        // Partido 78 → alimenta Octavo 3 (visitante)
        ['Costa de Marfil',    'Noruega',                '2026-06-30 17:00:00', 3, 'away'],  // AT&T Stadium, Arlington
        // Partido 79 → alimenta Octavo 4 (local)
        ['México',      'Ecuador',    '2026-07-01 01:00:00', 4, 'home'],  // Estadio Azteca, Ciudad de México
        // Partido 80 → alimenta Octavo 4 (visitante)
        ['Inglaterra',  'Congo DR',   '2026-07-01 16:00:00', 4, 'away'],  // Mercedes-Benz Stadium, Atlanta
        // Partido 83 → alimenta Octavo 5 (local)
        ['Portugal',    'Croacia',    '2026-07-02 23:00:00', 5, 'home'],  // BMO Field, Toronto
        // Partido 84 → alimenta Octavo 5 (visitante)
        ['España',      'Austria',    '2026-07-02 19:00:00', 5, 'away'],  // SoFi Stadium, Inglewood
        // Partido 81 → alimenta Octavo 6 (local)
        ['Estados Unidos', 'Bosnia y Herzegovina', '2026-07-02 00:00:00', 6, 'home'],  // Levi's Stadium, Santa Clara
        // Partido 82 → alimenta Octavo 6 (visitante)
        ['Bélgica',     'Senegal',    '2026-07-01 20:00:00', 6, 'away'],  // Lumen Field, Seattle
        // Partido 86 → alimenta Octavo 7 (local)
        ['Argentina',   'Cabo Verde', '2026-07-03 22:00:00', 7, 'home'],  // Hard Rock Stadium, Miami Gardens
        // Partido 88 → alimenta Octavo 7 (visitante)
        ['Australia',   'Egipto',     '2026-07-03 18:00:00', 7, 'away'],  // AT&T Stadium, Arlington
        // Partido 85 → alimenta Octavo 8 (local)
        ['Suiza',       'Argelia',    '2026-07-03 03:00:00', 8, 'home'],  // BC Place, Vancouver
        // Partido 87 → alimenta Octavo 8 (visitante)
        ['Colombia',    'Ghana',      '2026-07-04 01:30:00', 8, 'away'],  // Arrowhead Stadium, Kansas City
    ];

    // Timestamps de los octavos tal como están en la BD (WorldCup2026MatchesSeeder).
    // Índice 1 = primer partido del 4/7, 2 = segundo, y así sucesivamente.
    private const OCTAVO_STARTS = [
        1 => '2026-07-04 13:00:00',
        2 => '2026-07-04 17:00:00',
        3 => '2026-07-05 13:00:00',
        4 => '2026-07-05 17:00:00',
        5 => '2026-07-06 13:00:00',
        6 => '2026-07-06 17:00:00',
        7 => '2026-07-07 13:00:00',
        8 => '2026-07-07 17:00:00',
    ];

    public function run(): void
    {
        $tournament = Tournament::where('slug', 'mundial-demo-2026')->firstOrFail();

        $this->addConfirmedTeams();

        $phase = TournamentPhase::updateOrCreate(
            ['tournament_id' => $tournament->id, 'name' => 'Dieciseisavos de Final'],
            ['type' => 'knockout', 'order' => 0, 'is_active' => true]
        );

        $sourceMatchIds = [];

        foreach (self::FIXTURES as [$home, $away, $startsAt, $octavoIdx, $side]) {
            $match = FootballMatch::updateOrCreate(
                [
                    'tournament_id' => $tournament->id,
                    'home_team_id'  => $this->team($home)->id,
                    'away_team_id'  => $this->team($away)->id,
                    'starts_at'     => $startsAt,
                ],
                [
                    'phase_id'             => $phase->id,
                    'group_id'             => null,
                    'prediction_closes_at' => date('Y-m-d H:i:s', strtotime($startsAt . ' -10 minutes')),
                    'status'               => 'scheduled',
                ]
            );

            $sourceMatchIds[$octavoIdx][$side] = $match->id;
        }

        $octavoPhase = TournamentPhase::where('tournament_id', $tournament->id)
            ->where('name', 'Octavos de final')
            ->first();

        foreach (self::OCTAVO_STARTS as $idx => $startsAt) {
            $octavo = FootballMatch::where('tournament_id', $tournament->id)
                ->where('starts_at', $startsAt)
                ->when($octavoPhase, fn ($q) => $q->where('phase_id', $octavoPhase->id))
                ->first();

            if ($octavo && isset($sourceMatchIds[$idx])) {
                $octavo->update([
                    'home_source_match_id' => $sourceMatchIds[$idx]['home'] ?? null,
                    'away_source_match_id' => $sourceMatchIds[$idx]['away'] ?? null,
                ]);
            }
        }
    }

    private function addConfirmedTeams(): void
    {
        $teams = [
            ['name' => 'Paraguay',            'fifa_code' => 'PAR', 'flag' => 'py'],
            ['name' => 'Suecia',              'fifa_code' => 'SWE', 'flag' => 'se'],
            ['name' => 'Países Bajos',        'fifa_code' => 'NED', 'flag' => 'nl'],
            ['name' => 'Japón',               'fifa_code' => 'JPN', 'flag' => 'jp'],
            ['name' => 'Bélgica',             'fifa_code' => 'BEL', 'flag' => 'be'],
            ['name' => 'España',              'fifa_code' => 'ESP', 'flag' => 'es'],
            ['name' => 'Cabo Verde',          'fifa_code' => 'CPV', 'flag' => 'cv'],
            ['name' => 'Australia',           'fifa_code' => 'AUS', 'flag' => 'au'],
            ['name' => 'Egipto',              'fifa_code' => 'EGY', 'flag' => 'eg'],
            // Equipos de 16avos confirmados
            ['name' => 'Ecuador',             'fifa_code' => 'ECU', 'flag' => 'ec'],
            ['name' => 'Inglaterra',          'fifa_code' => 'ENG', 'flag' => 'gb-eng'],
            ['name' => 'Congo DR',            'fifa_code' => 'COD', 'flag' => 'cd'],
            ['name' => 'Portugal',            'fifa_code' => 'POR', 'flag' => 'pt'],
            ['name' => 'Croacia',             'fifa_code' => 'CRO', 'flag' => 'hr'],
            ['name' => 'Austria',             'fifa_code' => 'AUT', 'flag' => 'at'],
            ['name' => 'Senegal',             'fifa_code' => 'SEN', 'flag' => 'sn'],
            ['name' => 'Argelia',             'fifa_code' => 'ALG', 'flag' => 'dz'],
            ['name' => 'Ghana',               'fifa_code' => 'GHA', 'flag' => 'gh'],
        ];

        foreach ($teams as $data) {
            Team::updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                [
                    'name'        => $data['name'],
                    'logo_path'   => "https://flagcdn.com/w160/{$data['flag']}.png",
                    'fifa_code'   => $data['fifa_code'],
                    'country'     => $data['name'],
                    'description' => 'Selección clasificada a los Dieciseisavos del Mundial 2026.',
                    'is_active'   => true,
                ]
            );
        }
    }

    private function team(string $name): Team
    {
        $slug = Str::slug($name);

        $team = Team::where('slug', $slug)->first();
        if ($team) {
            return $team;
        }

        // Posición del bracket aún sin confirmar (ej. "Clasificado 3ro C/E")
        return Team::firstOrCreate(
            ['slug' => 'slot-' . $slug],
            [
                'name'        => $name,
                'fifa_code'   => 'TBD',
                'country'     => 'Por confirmar',
                'description' => 'Clasificación pendiente de confirmarse.',
                'is_active'   => false,
            ]
        );
    }
}
