<?php

namespace Database\Seeders;

use App\Models\FootballMatch;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentPhase;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class WorldCup2026MatchesSeeder extends Seeder
{
    // Cruces de Dieciseisavos de Final (28 jun – 3 jul).
    // Los equipos aún no confirmados aparecen como placeholders hasta
    // que se actualice el seeder con los 32 clasificados definitivos.
    // Horarios en UTC (hora Lima UTC-5 + 5h). Equipos según bracket oficial FIFA.
    // "A definir" = slot pendiente de confirmación de grupos aún en disputa.
    private const ROUND_32_FIXTURES = [
        ['2026-06-28 19:00:00', 'Sudáfrica',      'Canadá'],    // Dom 28/6 2:00 PM Lima
        ['2026-06-29 17:00:00', 'Brasil',         'A definir'], // Lun 29/6 12:00 PM Lima
        ['2026-06-29 20:30:00', 'Alemania',       'A definir'], // Lun 29/6 3:30 PM Lima
        ['2026-06-30 01:00:00', 'A definir',      'Marruecos'], // Lun 29/6 8:00 PM Lima
        ['2026-06-30 17:00:00', 'A definir',      'A definir'], // Mar 30/6 12:00 PM Lima
        ['2026-06-30 21:00:00', 'A definir',      'A definir'], // Mar 30/6 4:00 PM Lima
        ['2026-07-01 01:00:00', 'México',         'A definir'], // Mar 30/6 8:00 PM Lima
        ['2026-07-01 16:00:00', 'A definir',      'A definir'], // Miér 1/7 11:00 AM Lima
        ['2026-07-01 20:00:00', 'A definir',      'A definir'], // Miér 1/7 3:00 PM Lima
        ['2026-07-02 00:00:00', 'Estados Unidos', 'A definir'], // Miér 1/7 7:00 PM Lima
        ['2026-07-02 19:00:00', 'A definir',      'A definir'], // Jue 2/7 2:00 PM Lima
        ['2026-07-02 23:00:00', 'A definir',      'A definir'], // Jue 2/7 6:00 PM Lima
        ['2026-07-03 03:00:00', 'Suiza',          'A definir'], // Jue 2/7 10:00 PM Lima
        ['2026-07-03 18:00:00', 'A definir',      'A definir'], // Vie 3/7 1:00 PM Lima
        ['2026-07-03 22:00:00', 'Argentina',      'A definir'], // Vie 3/7 5:00 PM Lima
        ['2026-07-04 01:30:00', 'A definir',      'A definir'], // Vie 3/7 8:30 PM Lima
    ];

    private const LATER_FIXTURES = [
        ['round_16',    '2026-07-04 13:00:00', 'Ganador Llave 1',  'Ganador Llave 2'],
        ['round_16',    '2026-07-04 17:00:00', 'Ganador Llave 3',  'Ganador Llave 4'],
        ['round_16',    '2026-07-05 13:00:00', 'Ganador Llave 5',  'Ganador Llave 6'],
        ['round_16',    '2026-07-05 17:00:00', 'Ganador Llave 7',  'Ganador Llave 8'],
        ['round_16',    '2026-07-06 13:00:00', 'Ganador Llave 9',  'Ganador Llave 10'],
        ['round_16',    '2026-07-06 17:00:00', 'Ganador Llave 11', 'Ganador Llave 12'],
        ['round_16',    '2026-07-07 13:00:00', 'Ganador Llave 13', 'Ganador Llave 14'],
        ['round_16',    '2026-07-07 17:00:00', 'Ganador Llave 15', 'Ganador Llave 16'],
        ['quarter_final', '2026-07-09 16:00:00', 'Ganador Octavo 1', 'Ganador Octavo 2'],
        ['quarter_final', '2026-07-10 15:00:00', 'Ganador Octavo 3', 'Ganador Octavo 4'],
        ['quarter_final', '2026-07-11 17:00:00', 'Ganador Octavo 5', 'Ganador Octavo 6'],
        ['quarter_final', '2026-07-11 21:00:00', 'Ganador Octavo 7', 'Ganador Octavo 8'],
        ['semi_final',    '2026-07-14 15:00:00', 'Ganador Cuarto 1', 'Ganador Cuarto 2'],
        ['semi_final',    '2026-07-15 15:00:00', 'Ganador Cuarto 3', 'Ganador Cuarto 4'],
        ['third_place',   '2026-07-18 17:00:00', 'Perdedor Semi 1',  'Perdedor Semi 2'],
        ['final',         '2026-07-19 15:00:00', 'Ganador Semi 1',   'Ganador Semi 2'],
    ];

    public function run(): void
    {
        $tournament = Tournament::where('slug', 'mundial-demo-2026')->firstOrFail();

        $round32Phase = TournamentPhase::updateOrCreate(
            ['tournament_id' => $tournament->id, 'name' => 'Dieciseisavos de final'],
            ['type' => 'knockout', 'order' => 1, 'is_active' => true]
        );

        foreach (self::ROUND_32_FIXTURES as [$startsAt, $home, $away]) {
            FootballMatch::updateOrCreate(
                [
                    'tournament_id' => $tournament->id,
                    'home_team_id'  => $this->resolveTeam($home)->id,
                    'away_team_id'  => $this->resolveTeam($away)->id,
                    'starts_at'     => $startsAt,
                ],
                [
                    'phase_id'             => $round32Phase->id,
                    'group_id'             => null,
                    'prediction_closes_at' => date('Y-m-d H:i:s', strtotime($startsAt . ' -1 hour')),
                    'status'               => 'scheduled',
                ]
            );
        }

        $phaseMap = [
            'round_16'      => ['Octavos de final', 2],
            'quarter_final' => ['Cuartos de final', 3],
            'semi_final'    => ['Semifinal',        4],
            'third_place'   => ['Tercer puesto',    5],
            'final'         => ['Final',            6],
        ];

        foreach (self::LATER_FIXTURES as [$type, $startsAt, $home, $away]) {
            [$name, $order] = $phaseMap[$type];
            $phase = TournamentPhase::firstOrCreate(
                ['tournament_id' => $tournament->id, 'name' => $name],
                ['type' => $type === 'final' ? 'final' : 'knockout', 'order' => $order, 'is_active' => true]
            );

            FootballMatch::updateOrCreate(
                [
                    'tournament_id' => $tournament->id,
                    'home_team_id'  => $this->placeholderTeam($home)->id,
                    'away_team_id'  => $this->placeholderTeam($away)->id,
                    'starts_at'     => $startsAt,
                ],
                [
                    'phase_id'             => $phase->id,
                    'group_id'             => null,
                    'prediction_closes_at' => date('Y-m-d H:i:s', strtotime($startsAt . ' -1 hour')),
                    'status'               => 'scheduled',
                ]
            );
        }
    }

    // Usa el equipo real si ya fue sembrado; crea un placeholder si aún no está confirmado.
    private function resolveTeam(string $name): Team
    {
        return Team::where('name', $name)->first() ?? $this->placeholderTeam($name);
    }

    private function placeholderTeam(string $name): Team
    {
        return Team::firstOrCreate(
            ['slug' => 'slot-' . Str::slug($name)],
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
